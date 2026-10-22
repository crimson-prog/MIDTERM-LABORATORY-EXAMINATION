<?php

namespace App\Observers;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Support\Facades\DB;
use Throwable;

class AccountObserver
{
   
    public function created(Transaction $transaction): void
    {
        try {
            DB::transaction(function () use ($transaction) {
                $account = $this->lockAccount($transaction);

                $amount = (float) $transaction->amount;
                $newBalance = $transaction->type === 'deposit'
                    ? (float) $account->balance + $amount
                    : (float) $account->balance - $amount;

                if ($transaction->type !== 'deposit'
                    && ($newBalance < (float) $account->minimum_balance || $newBalance < 0)) {
                    throw InsufficientBalanceException::for($account, $amount);
                }

                $account->balance = $newBalance;
                $account->save();

                if ($transaction->type === 'transfer' && $transaction->to_account_id) {
                    $this->creditDestination($transaction, $amount);
                }

                $this->handleLowBalanceNotification($account, $transaction->type);
            });
        } catch (Throwable $e) {
            if (DB::transactionLevel() === 0) {
                $transaction->delete();
            }

            throw $e;
        }
    }

   
    private function lockAccount(Transaction $transaction): Account
    {
        $ids = array_filter([$transaction->account_id, $transaction->to_account_id]);

        $accounts = Account::whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        return $accounts[$transaction->account_id];
    }

    private function creditDestination(Transaction $transaction, float $amount): void
    {
        $destination = Account::whereKey($transaction->to_account_id)->firstOrFail();
        $destination->balance = (float) $destination->balance + $amount;
        $destination->save();
    }

    
    private function handleLowBalanceNotification(Account $account, string $type): void
    {
        if ((float) $account->balance > (float) $account->minimum_balance) {
            if ($account->low_balance_notified_at !== null) {
                $account->forceFill(['low_balance_notified_at' => null])->saveQuietly();
            }

            return;
        }

        if ($type === 'deposit' || $account->low_balance_notified_at !== null) {
            return;
        }

        $account->forceFill(['low_balance_notified_at' => now()])->saveQuietly();

        $account->user?->notify(new LowBalanceNotification($account));
    }
}

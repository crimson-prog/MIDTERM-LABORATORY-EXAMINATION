<?php

namespace App\Exceptions;

use App\Models\Account;
use Exception;

class InsufficientBalanceException extends Exception
{
    public static function for(Account $account, float $amount): self
    {
        return new self(sprintf(
            'Insufficient funds: account %s has a balance of %s, but a %s of %s would drop it below the minimum balance of %s.',
            $account->account_number,
            number_format((float) $account->balance, 2),
            'withdrawal',
            number_format($amount, 2),
            number_format((float) $account->minimum_balance, 2)
        ));
    }
}

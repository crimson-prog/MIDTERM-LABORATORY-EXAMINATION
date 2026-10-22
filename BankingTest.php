<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\LowBalanceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BankingTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function account(User $user, string $balance = '1000.00', string $minimum = '100.00'): Account
    {
        return Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-'.uniqid(),
            'balance' => $balance,
            'minimum_balance' => $minimum,
        ]);
    }

    public function test_account_belongs_to_user_and_has_many_transactions(): void
    {
        $user = $this->user();
        $account = $this->account($user);

        Transaction::create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 100,
        ]);

        $this->assertTrue($account->user->is($user));
        $this->assertCount(1, $account->transactions);
        $this->assertTrue($account->transactions->first()->account->is($account));
    }

    public function test_deposit_increases_the_balance_through_the_observer(): void
    {
        $user = $this->user();
        $account = $this->account($user, '1000.00');

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 250.50,
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame('1250.50', $account->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 250.50,
        ]);
    }

    public function test_withdrawal_decreases_the_balance(): void
    {
        $user = $this->user();
        $account = $this->account($user, '1000.00');

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 300,
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame('700.00', $account->fresh()->balance);
    }

    public function test_withdrawal_below_the_minimum_balance_is_rejected_and_rolled_back(): void
    {
        $user = $this->user();
        $account = $this->account($user, '500.00', '100.00');

        $this->actingAs($user)
            ->post(route('transactions.store'), [
                'account_id' => $account->id,
                'type' => 'withdrawal',
                'amount' => 450,
            ])
            ->assertSessionHasErrors('amount');

        $this->assertSame('500.00', $account->fresh()->balance);
        $this->assertDatabaseMissing('transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
        ]);
    }

    public function test_validation_rejects_unknown_values(): void
    {
        $user = $this->user();
        $account = $this->account($user);

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'type' => 'transfer',
            'amount' => 0,
        ])->assertSessionHasErrors(['type', 'amount']);
    }

    public function test_low_balance_notification_is_sent_once(): void
    {
        $user = $this->user();
        $account = $this->account($user, '200.00', '100.00');

        Notification::fake();

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 100,
        ])->assertRedirect(route('transactions.index'));

        Notification::assertSentTo($user, LowBalanceNotification::class, 1);

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 1,
        ]);

        Notification::assertSentTo($user, LowBalanceNotification::class, 1);
    }

    public function test_dashboard_stats_endpoint_returns_json(): void
    {
        $user = $this->user();
        $this->account($user, '750.00');

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk()
            ->assertJsonStructure([
                'total_accounts',
                'total_money',
                'total_transactions_today',
                'generated_at',
            ]);

        $this->assertSame(1, $response->json('total_accounts'));
        $this->assertSame('750.00', $response->json('total_money'));
    }

    public function test_dashboard_page_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('transactions.create'))->assertRedirect(route('login'));
    }
}

<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::create([
            'name' => 'Demo User',
            'email' => 'demo@bank.test',
            'password' => 'password',
        ]);

        $second = User::create([
            'name' => 'Second User',
            'email' => 'second@bank.test',
            'password' => 'password',
        ]);

        $savings = Account::create([
            'user_id' => $demo->id,
            'account_number' => 'ACC-1001',
            'balance' => 0,
            'minimum_balance' => 100,
        ]);

        $current = Account::create([
            'user_id' => $demo->id,
            'account_number' => 'ACC-1002',
            'balance' => 0,
            'minimum_balance' => 100,
        ]);

        $other = Account::create([
            'user_id' => $second->id,
            'account_number' => 'ACC-2001',
            'balance' => 0,
            'minimum_balance' => 100,
        ]);

        $transactions = [
            [$savings, 'deposit', 5000, 'Salary deposit'],
            [$savings, 'withdrawal', 1200, 'Rent payment'],
            [$savings, 'deposit', 250, 'Refund'],
            [$current, 'deposit', 250, 'Opening deposit'],
            [$current, 'withdrawal', 150, 'Dinner'],
            [$other, 'deposit', 900, 'Opening deposit'],
        ];

        foreach ($transactions as [$account, $type, $amount, $notes]) {
            Transaction::create([
                'account_id' => $account->id,
                'type' => $type,
                'amount' => $amount,
                'reference_number' => 'REF-'.strtoupper(Str::random(8)),
                'notes' => $notes,
            ]);
        }
    }
}

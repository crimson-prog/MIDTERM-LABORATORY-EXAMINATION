<?php

namespace App\Rules;

use App\Models\Account;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SufficientFunds implements ValidationRule
{
    public function __construct(private ?Account $account, private string $type) {}

    
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->account === null || $this->type === 'deposit') {
            return;
        }

        $balance = (float) $this->account->balance;
        $minimum = (float) $this->account->minimum_balance;
        $amount = (float) $value;

        if ($balance - $amount < $minimum) {
            $fail(
                'The account does not have enough funds. The available balance is $'
                .number_format($balance, 2).' and the minimum balance is $'.number_format($minimum, 2).'.'
            );
        }
    }
}

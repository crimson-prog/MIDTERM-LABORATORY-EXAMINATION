@extends('layouts.app')

@section('title', 'New Transaction - Banking System')

@section('content')
<div class="card" style="max-width:620px">
    <h1>New Transaction</h1>
    <p class="sub">Deposits add money to the balance. Withdrawals are rejected if the balance would fall below the minimum balance.</p>

    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf

        <div class="field">
            <label for="account_id">Account</label>
            <select id="account_id" name="account_id" required>
                <option value="">-- Select an account --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>
                        {{ $account->account_number }} - balance $ {{ number_format((float) $account->balance, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type" required>
                    <option value="deposit" @selected(old('type') === 'deposit')>Deposit</option>
                    <option value="withdrawal" @selected(old('type') === 'withdrawal')>Withdrawal</option>
                </select>
            </div>

            <div class="field">
                <label for="amount">Amount</label>
                <input id="amount" type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required>
            </div>
        </div>

        <div class="field">
            <label for="reference_number">Reference number (optional)</label>
            <input id="reference_number" type="text" name="reference_number" value="{{ old('reference_number') }}"
                   placeholder="Generated automatically when left empty">
        </div>

        <div class="field">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn">Save transaction</button>
        <a href="{{ route('transactions.index') }}" class="btn" style="background:#64748b">Cancel</a>
    </form>
</div>
@endsection

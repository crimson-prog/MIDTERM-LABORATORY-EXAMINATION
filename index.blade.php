@extends('layouts.app')

@section('title', 'Transactions - Banking System')

@section('content')
<div class="card">
    <h2>Transaction History</h2>

    @if ($transactions->isEmpty())
        <p class="muted">No transactions recorded yet.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Type</th>
                <th class="num">Amount</th>
                <th>Reference</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $transaction->account->account_number }}</td>
                    <td>
                        <span class="badge badge-{{ $transaction->type }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td class="num">$ {{ number_format((float) $transaction->amount, 2) }}</td>
                    <td><code>{{ $transaction->reference_number }}</code></td>
                    <td class="muted">{{ $transaction->notes }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Account;
use App\Models\Transaction;
use App\Rules\SufficientFunds;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = Account::where('user_id', $request->user()->id)->get();

        $transactions = Transaction::whereIn('account_id', $accounts->pluck('id'))
            ->with('account')
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('accounts', 'transactions'));
    }

    public function create(Request $request): View
    {
        $accounts = Account::where('user_id', $request->user()->id)->get();

        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $account = Account::where('user_id', $request->user()->id)
            ->find($request->input('account_id'));

        $validated = $request->validate([
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $request->user()->id),
            ],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                new SufficientFunds($account, (string) $request->input('type')),
            ],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                Transaction::create([
                    'account_id' => $validated['account_id'],
                    'type' => $validated['type'],
                    'amount' => $validated['amount'],
                    'reference_number' => $validated['reference_number'] ?? 'REF-'.strtoupper(Str::random(8)),
                    'notes' => $validated['notes'] ?? null,
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'The '.$validated['type'].' was recorded successfully.');
    }
}

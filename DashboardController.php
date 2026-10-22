<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = Account::where('user_id', $request->user()->id)
            ->withCount('transactions')
            ->get();

        $notifications = $request->user()->notifications()->latest()->limit(5)->get();

        return view('dashboard', compact('accounts', 'notifications'));
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total_accounts' => Account::count(),
            'total_money' => number_format((float) Account::sum('balance'), 2),
            'total_transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'generated_at' => now()->toDateTimeString(),
        ]);
    }
}

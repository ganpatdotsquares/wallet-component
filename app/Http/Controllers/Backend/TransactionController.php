<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $status = $request->input('status', '');

            $transactionsQuery = WalletTransaction::query()
                ->whereHas('user', function ($query) use ($search) {
                    if (!empty($search)) {
                        $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    }
                });

            if (!empty($status)) {
                $transactionsQuery->where('status', $status);
            }

            $transactions = $transactionsQuery
                ->sortable() // Ensure this is properly set up
                ->orderBy('created_at', 'desc')
                ->paginate(config('GET.admin_page_limit'));

            return view('backend.transactions.index', compact('transactions', 'search', 'status'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Failed to fetch transaction history.');
        }

    }

    public function show($id)
    {
        try{
            $transaction = WalletTransaction::findOrFail($id);
            return view('backend.transactions.show', compact('transaction'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Transaction not found.');
        }
    }
}
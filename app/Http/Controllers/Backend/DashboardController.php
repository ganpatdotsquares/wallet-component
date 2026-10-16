<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\WalletTransaction;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('backend.auth.login');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function dashboard()
    {
        if (Auth::guard('admin')->check()) {
            $totalUsers = User::count();
            $activeUsers = User::where('status', 1)->count();
            $totalTransactions = WalletTransaction::count();
            $adminCommission = WalletTransaction::sum('admin_commission');
            return view('backend.dashboard', compact('totalUsers', 'activeUsers', 'totalTransactions', 'adminCommission'));
        }

        return redirect("admin/login")->withSuccess('Opps! You do not have access');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function transactionsList(Request $request)
    {
        $transactions = WalletTransaction::with(['user', 'relatedUser'])->orderBy('created_at', 'desc');
        // Filters
        if ($request->user_search) {
            $transactions->whereHas('user', function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->user_search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->user_search.'%');
            });
        }
        if ($request->related_user_search) {
            $transactions->whereHas('relatedUser', function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->related_user_search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->related_user_search.'%');
            });
        }
        if ($request->type) {
            $transactions->where('type', $request->type);
        }
        if ($request->date) {
            $transactions->whereDate('created_at', $request->date);
        }
        return datatables()->of($transactions)
            ->addColumn('user', function($row) {
                return $row->user ? $row->user->name : '';
            })
            ->addColumn('related_user', function($row) {
                return $row->relatedUser ? $row->relatedUser->name : '';
            })
            ->addColumn('admin_commission', function($row) {
                return number_format($row->admin_commission, 2);
            })
            ->make(true);
    }

    public function totalAdminCommission()
    {
        $total = WalletTransaction::sum('admin_commission');
        return view('admin.total_commission', compact('total'));
    }
}

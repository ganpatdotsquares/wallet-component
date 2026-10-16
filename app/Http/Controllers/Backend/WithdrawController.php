<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Transfer;


class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $status = $request->input('status', '');

            $withdrawQuery = WithdrawRequest::query()
                            ->whereHas('user', function ($query) use ($search) {
                                if (!empty($search)) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                                }
                            });

            if (!empty($status)) {
                $withdrawQuery->where('status', $status);
            }

            $withdrawList = $withdrawQuery
                ->sortable()
                ->orderBy('created_at', 'desc')
                ->paginate(config('GET.admin_page_limit'));

            return view('backend.withdraw.index', compact('withdrawList', 'search', 'status'));
        } catch (\Exception $e) {
            Log::error('Withdraw Request Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch withdrawal requests.');
        }
    }


    public function show($id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        return view('backend.withdraw.show', compact('withdraw'));
    }

    public function changeWithdrawStatus(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $withdrawReq = WithdrawRequest::find($id);

            if (!$withdrawReq) {
                return response()->json(['message' => 'Withdraw request not found.'], 404);
            }

            if ($withdrawReq->status === 'approved') {
                return response()->json(['message' => 'Withdraw request already approved.'], 400);
            }

            $user = $withdrawReq->user;
            $wallet = $user->wallet;

            $replacement = [
                'USERNAME'     => $user->name,
                'CURRENCY'     => config('services.stripe.currency_sign', '$'),
                'AMOUNT'       => $withdrawReq->amount,
                'REQUESTED_AT' => $withdrawReq->created_at->format(config('GET.admin_date_format')),
                'STATUS'       => ucfirst($request->status),
            ];

            $msg = "Invalid status provided.";
            $code = 400;

            // Approved flow
            if ($request->status === 'approved') {
                if ($wallet->balance < $withdrawReq->amount) {
                    DB::rollBack();
                    return response()->json(['message' => 'Insufficient wallet balance.'], 400);
                }

                // Deduct balance
                $wallet->balance -= $withdrawReq->amount;
                $wallet->save();

                if ($withdrawReq->method === 'stripe') {
                    // Stripe payout
                    if (!$user->stripe_account_id) {
                        DB::rollBack();
                        return response()->json(['message' => 'User Stripe account not connected.'], 400);
                    }

                    // Example: transfer to connected account
                    Stripe::setApiKey(config('services.stripe.secret'));

                    Transfer::create([
                        "amount"      => $withdrawReq->amount * 100, // cents
                        "currency"    => config('services.stripe.currency', 'usd'),
                        "destination" => $user->stripe_account_id,
                    ]);
                }

                // Mark request approved
                $withdrawReq->status = 'approved';
                $withdrawReq->save();

                $replacement['ACTION'] = "Your withdrawal request has been approved.";
                $msg = "Withdraw request approved successfully.";
                $code = 200;
            }

            // Declined flow
            if ($request->status === 'declined') {
                $withdrawReq->status = 'declined';
                $withdrawReq->save();

                $replacement['ACTION'] = "Your withdrawal request has been declined.";
                $msg = "Withdraw request declined successfully.";
                $code = 200;
            }

            DB::commit();
            return response()->json(['message' => $msg], $code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdraw status update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update status.'], 500);
        }
    }
}
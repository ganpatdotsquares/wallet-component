<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Mail\AdminMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc'); 
        $users = User::sortable()->when($search, function ($query) use ($search) {
            return $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });
        if (isset($request->status)) {

            $users->where('status', $request->status);
        }
        if ($sortField === 'name') {
            $users->orderByRaw("CONCAT(first_name, ' ', last_name) {$sortDirection}");
        } else {
            $users->orderBy($sortField, $sortDirection);
        }
        $users = $users
            ->orderBy('created_at', 'desc')
            ->paginate(config('GET.admin_page_limit'))
            ->withQueryString();

        return view('backend.user.index', compact('users', 'search'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('backend.user.show', compact('user'));
    }

    public function changeStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->id);

            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            $user->status = !$user->status;
            $user->save();

            DB::commit();
            return redirect()->route('admin.users.index')->with("status", "Status updated successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::error('Status update failed: ' . $exception->getMessage());
            return redirect()->back()->with('error', 'Failed to update status.');
        }
    }

    public function changeUserStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            $user->user_status = $request->action;
            $user->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update status. Please try again.'], 500);
        }
        $replacement['USERNAME'] = $user->name;

        // Send notification email based on the action
        if ($request->action === 'approve') {
            $data = [
                'to' => $user->email,
                'template' => 'account-approve-by-admin-to-user',
                'hooksVars' => $replacement
            ];
            Mail::to($user->email)->send(new AdminMail($data));
        } elseif ($request->action === 'decline') {
            $data = [
                'to' => $user->email,
                'template' => 'account-decline-by-admin-to-user',
                'hooksVars' => $replacement
            ];
            Mail::to($user->email)->send(new AdminMail($data));
        }

        // If the user is an NDIS agency, notify other users
        

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}

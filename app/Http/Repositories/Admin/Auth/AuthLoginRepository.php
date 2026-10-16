<?php

namespace App\Http\Repositories\Admin\Auth;

use Illuminate\Http\Request;
use App\Models\Admin; // Assuming Admin is your user model
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\AdminMail; // Assuming AdminMail is your mail class
use Illuminate\Support\Facades\Mail;
use App\Providers\RouteServiceProvider;
use App\Mail\SendMail;
use Hash;
use Auth;
use Session;


class AuthLoginRepository implements AuthLoginRepositoryInterface
{

    public function find(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $credentials = [
                    'email'    => $request->email,
                    'password' => $request->password,
                ];

                // Retrieve the 'remember' checkbox value
                $remember = $request->has('remember') ? true : false;
                // Attempt to authenticate the user with 'remember' option
                if (!Auth::guard('admin')->attempt($credentials, $remember)) {
                    \Log::warning('Authentication failed for email: ' . $request->email);
                    \Session::flash('admin-login-error', 'Invalid credentials.');
                    return redirect()->back()->withInput();
                }

                // Redirect to the dashboard if authentication is successful
                return redirect()->route('admin.dashboard');
            } catch (\Exception $e) {
                \Log::error('Authentication failed with exception: ' . $e->getMessage());
                \Session::flash('admin-login-error', 'An error occurred: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        } else {
            // If not a POST request, check if the user is already authenticated
            if (!Auth::guard('admin')->check()) {
                return view('backend.auth.login');
            }
            return redirect()->route('admin.dashboard');
        }
    }

    public function logout(Request $request)
    {
        // Log out the admin user
        Auth::guard('admin')->logout();

        // Invalidate the session to clear all data
        $request->session()->invalidate();

        // Regenerate the session token to prevent session fixation
        $request->session()->regenerateToken();

        // Redirect to the admin login page with a success message
        \Session::flash('admin-logout', 'Logout successful.');
        return redirect()->route('admin.login');
    }

    function resetpassword(Request $request)
    {
        try {
            return view('backend.auth.forgot-pass');
        } catch (\Throwable $th) {
            \Session::flash('admin-login-error', $th->getMessage());
            return redirect('/admin/login');
        }
    }

    public function sendResetpasswordMail(Request $request)
    {
        try {
            $user = Admin::where('email', $request->email)->first();
            // 
            if ($request->has('email')) {
                // dd($request->email);
                $replacement = [];
                $replacement['USER_NAME'] = $user->name;

                $resetPassword = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->orderBy('created_at', 'DESC')
                    ->first();

                if (!$resetPassword) {
                    DB::table('password_reset_tokens')->insert([
                        'email' => $request->email,
                        'token' => \Str::random(64),
                        'created_at' => Carbon::now()
                    ]);
                }

                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->update(['token' => \Str::random(64)]);
                $tokenData = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

                $token = $tokenData->token;
                $link = \App::make('url')->to('/admin/password-reset') . '/' . $token . '?email=' . urlencode($request->email);

                $replacement['RESET_PASSWORD_LINK'] = "<a href='" . $link . "' style='color:#1a0dab;'>Reset Link</a>";

                $data = ['to' => $request->email, 'template' => 'admin-reset-Password', 'hooksVars' => $replacement];
                Mail::to($request->email)->send(new AdminMail($data));

                return redirect()->back()
                    ->with('status', 'Reset password link has been sent to your email address.');
            }
            return redirect()->back()->with('error', 'Email address is not registered with us.');
        } catch (\Exception $e) {

            // Log the error message for debugging
            \Log::error('Error sending reset password email: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'A network error occurred. Please try again.');
        }
    }

    public function getPassword($token, Request $request)
    {
        $email = '';
        $resetPassword = DB::table('password_reset_tokens')->where('token', $token)->first();
        // dd($token);
        if (!$resetPassword) {
            return redirect()->route('admin.forgot_password')
                ->with('error', 'Link has been expired.');
        }
        if ($request->has('email')) {
            $email = $request->input('email');
        }
        return view('backend.auth.reset', ['token' => $token, 'email' => $email]);
    }

    public function postPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',

        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

        if (!$updatePassword)
            return back()->withInput()->with('error', 'Invalid token!');

        $user = Admin::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('admin.login')
                    ->with('status', 'Your password has been changed!');
    }
    
    public function updateprofile($request)
    {
        // Get the currently authenticated user
        $user = auth()->guard('admin')->user();
        // Update the profile data
        $user->name = $request->input('name');
        $user->mobile_number = $request->input('mobile_number');

        // Handle file upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && file_exists(public_path('/backend/images/profile/' . $user->profile_image))) {
                unlink(public_path('/backend/images/profile/' . $user->profile_image));
            }

            // Store new profile image
            $file = $request->file('profile_image');
            $photo_name = time() . "." . $file->getClientOriginalExtension();
            $directory = public_path('/backend/images/profile');
            $file->move($directory, $photo_name);
            $user->profile_image = $photo_name;
        }

        // Save the updated user profile
        $user->save();

        // Redirect with success message
        return redirect()->route('admin.profile')
            ->with('status', 'Your profile updated successfully!');
    }

    public function password($request)
    {
        $user = auth()->guard('admin')->user(); // Ensure you are retrieving the authenticated user

        // Check if the current password matches
        if (!Hash::check($request->input('currentpassword'), $user->password)) {
            return redirect()->route('admin.profile', ['tab' => 2])
                ->with('error', 'Your old password does not match the password you provided. Please try again.');
        }

        // Check if the new password is the same as the current password
        if ($request->input('currentpassword') === $request->input('newpassword')) {
            return redirect()->route('admin.profile', ['tab' => 2])
                ->with('error', 'New Password cannot be the same as your old password. Please choose a different password.');
        }

        // Check if the new password confirmation matches the new password
        if ($request->input('newpassword_confirmation') !== $request->input('newpassword')) {
            return redirect()->route('admin.profile', ['tab' => 2])
                ->with('error', 'New Password must be the same as the confirmed new password.');
        }
        $user->password = Hash::make($request->input('newpassword'));
        $user->save();
        return redirect()->route('admin.profile', ['tab' => 2])
            ->with('status', 'Password changed successfully!');
    }
}

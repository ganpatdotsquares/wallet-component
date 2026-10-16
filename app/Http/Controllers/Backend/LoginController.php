<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Html\Html;
use App\Http\Repositories\Admin\Auth\AuthLoginRepositoryInterface;
use Validator;
use Auth;
use Hash;
use Session;
use DB;
use Illuminate\Support\Carbon;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Http\Requests\Admin\Auth\AdminFortgotPassRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(AuthLoginRepositoryInterface $AuthLoginRepositoryInterface)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
        $this->AuthLoginRepositoryInterface = $AuthLoginRepositoryInterface;
    }

    /**
     * login a user using email & password.
     *
     * @return void
     */
    public function login_view(Request $request)
    {
        return view('backend.auth.login');
    }
    public function index(AdminLoginRequest $request)
    {
        return $this->AuthLoginRepositoryInterface->find($request);
    }
    public function logout(Request $request)
    {
        return $this->AuthLoginRepositoryInterface->logout($request);
    }
    public function resetpassword(Request $request)
    {
        return $this->AuthLoginRepositoryInterface->resetpassword($request);
    }
    public function sendResetpasswordMail(AdminFortgotPassRequest $request)
    {
        return $this->AuthLoginRepositoryInterface->sendResetpasswordMail($request);
    }
    public function getPassword($token, Request $request)
    {
        return $this->AuthLoginRepositoryInterface->getPassword($token, $request);
    }
    public function postPassword(ResetPasswordRequest $request)
    {
        return $this->AuthLoginRepositoryInterface->postPassword($request);
    }
}

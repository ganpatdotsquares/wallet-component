<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Admin\Auth\AuthLoginRepositoryInterface;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Admin\Auth\ValidateFormRequest;
use App\Http\Requests\Admin\Auth\PasswordRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use Auth;
use Hash;
use Session;
use App\Http\Requests\Admin\Auth\ProfileUpdateRequest;

class ProfileController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthLoginRepositoryInterface $AuthLoginRepositoryInterface)
    {
        $this->AuthLoginRepositoryInterface = $AuthLoginRepositoryInterface;
    }

    /**
     * login a user using email & password.
     *
     * @return void
     */
    public function profile()
    {
        $user = auth()->guard('admin')->user();
        return view('backend.auth.profileadmin', compact('user'));
    }
    
    public function updateAdmin(ProfileUpdateRequest $request)
    {
        return $this->AuthLoginRepositoryInterface->updateprofile($request);
    }

    public function password(PasswordRequest $request)
    { 
        return $this->AuthLoginRepositoryInterface->password( $request);
    }
}

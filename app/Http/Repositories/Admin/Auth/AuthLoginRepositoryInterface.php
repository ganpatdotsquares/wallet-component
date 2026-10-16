<?php

namespace App\Http\Repositories\Admin\Auth;

use Illuminate\Http\Request;

interface AuthLoginRepositoryInterface
{
    public function find(Request $request);
    public function logout(Request $request);
    public function sendResetpasswordMail(Request $request);
    public function getPassword($token, Request $request);
    public function postPassword(Request $request);
    public function updateprofile($request);
    public function password($request);
}

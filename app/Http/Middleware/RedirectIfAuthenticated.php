<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        // Check if the user is authenticated
        if (!Auth::guard($guard)->check()) {
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login');
            }
            return redirect()->route('front.home');
        }
        $currentUrl = $request->fullUrl();
        $cleanedUrl = str_replace('/public', '', $currentUrl);
        if ($currentUrl !== $cleanedUrl) {
            return redirect($cleanedUrl);
        }
        return $next($request);
    }
}

<?php



namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiGlobalFunctions;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Log;



class CheckAuthorization
{
    use ApiGlobalFunctions;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        Log::info('Incoming Request:', [
            'url' => $request->url(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'params' => $request->all()  // This will log all input parameters (both query and body)
        ]);
        $authInformation = apache_request_headers();

        // Check if Authorization header exists and is not empty
        $authorization = $authInformation['Authorization'] ?? null;

        if (!$authorization) {
            return $this->sendError($this->messageDefault('You are not authorized to access.'), '', '401');
        }

        // Extract token from Authorization header
        $token = trim(str_replace("Bearer", "", $authorization));

        // Get the authenticated user using Sanctum
        $user = auth('sanctum')->user();

        // Check if user is authenticated
        if (!$user) {
            return $this->sendError($this->messageDefault('You are not authorized to access any more.'), '', '401');
        }

        // Check user status
        if ($user->status == 0) {
            $user->tokens->each(function ($token) {
                $token->delete();
            });
            //$this->logout($request);
            return $this->sendError($this->messageDefault('Your account has been deactivated. Please contact the admin.'), '', '401');
        }

        // Update device information if present
        // $user->update([
        //     'device_id' => $authInformation['Device-Id'] ?? null,
        //     'device_type' => $authInformation['Device-Type'] ?? null,
        // ]);

        // Set authenticated user in request attributes
        $request->attributes->set('Auth', $user);

        return $next($request);
    }
}

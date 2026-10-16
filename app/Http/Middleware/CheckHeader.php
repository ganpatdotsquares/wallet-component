<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Str;

class CheckHeader
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
        // Convert camelCase keys to snake_case for request body (e.g., JSON)
        if ($request->isJson() || $request->expectsJson()) {
            $snakeCaseInput = collect($request->all())->mapWithKeys(function ($value, $key) {
                return [Str::snake($key) => $value];
            });

            // Replace input with snake_case version
            $request->replace($snakeCaseInput->toArray());
        }

        // Optionally convert query params (if needed)
        $snakeCaseQuery = collect($request->query())->mapWithKeys(function ($value, $key) {
            return [Str::snake($key) => $value];
        });
        $request->query->replace($snakeCaseQuery->toArray());

        return $next($request);

        // $authInformation = apache_request_headers();
        // $APP_VERSION = 1;

        // $versionCode = $authInformation['Version-Code'] ?? null;
        // $deviceType = $authInformation['Device-Type'] ?? null;

        // if (!in_array($deviceType, ['ios', 'android'])) {
        //     return $this->sendError($this->messageDefault('You are not authorized to access.'), '', '401');
        // }
        // if (!empty($versionCode) && $versionCode == $APP_VERSION) {
        //     return $next($request);
        // }
        // return $this->sendErrorForVersion($this->messageDefault('A new version is available. Please update the app.'), '', '200');
    }
}

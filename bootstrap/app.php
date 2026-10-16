<?php

use App\Http\Middleware\RedirectIfNotAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(function () {
        // Register web routes
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // Register admin routes
        Route::middleware(['web', 'admin'])
            ->prefix('admin')
            ->group(base_path('routes/admin.php'));

        // Register api routes
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        // Register console routes
        Route::middleware('console')
            ->group(base_path('routes/console.php'));
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
            'webhook/cvcheck'
        ]);
        $middleware->prependToGroup('web', [
            // RedirectIfNotAuthenticated::class,
        ]);

        $middleware->prependToGroup('admin', [
            // AdminMiddleware::class,
        ]);

        $middleware->alias([
            'api.header' => \App\Http\Middleware\CheckHeader::class,
            'api.authtoken' => \App\Http\Middleware\CheckAuthorization::class,
            'PreventBackHistory' => \App\Http\Middleware\PreventBackHistory::class,
            'auth' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'guest' => \App\Http\Middleware\RedirectIfNotAuthenticated::class
        ]);
        // $middleware->web(prepend: [
        //     AdminMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('admin/*')) {
                // Render the custom admin 404 page
                return response()->view('backend.errors.404', [], 404);
            }
            return response()->view('errors.404', status: 500);
        });
    })->create();

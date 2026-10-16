<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Admin's Repo
use App\Http\Repositories\Admin\Auth\AuthLoginRepositoryInterface;
use App\Http\Repositories\Admin\Auth\AuthLoginRepository;

use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // ADMIN REPO REGISTER
        
        // Bind the Admin Auth repository
        $this->app->bind(AuthLoginRepositoryInterface::class, AuthLoginRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        View::share([]);
    }
}

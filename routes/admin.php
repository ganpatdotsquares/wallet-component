<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\SettingManagerController;
use App\Http\Controllers\Backend\WithdrawController;
use App\Http\Controllers\Backend\TransactionController;


Route::middleware('admin')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::any('/', [LoginController::class, 'login_view'])->name('admin.login');
        Route::post('post-login', [LoginController::class, 'index'])->name('admin.login.post');
        Route::any('/forgot-password', [LoginController::class, 'resetpassword'])->name('admin.forgot_password');
        Route::any('/resetpassword', [LoginController::class, 'sendResetpasswordMail'])->name('admin.send_reset_mail');
        Route::get('/password-reset/{token}', [LoginController::class, 'getPassword'])->name('admin.get_reset_password');
        Route::post('/password-reset-submit', [LoginController::class, 'postPassword'])->name('admin.password.change');
    });

    Route::middleware(['auth:admin', 'PreventBackHistory'])->group(function () {
        //Auth Routes
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
        Route::any('/logout', [LoginController::class, 'logout'])->name('admin.logout');
        Route::any('/profile', [ProfileController::class, 'profile'])->name('admin.profile');
        Route::any('/update-profile', [ProfileController::class, 'updateAdmin'])->name('admin.update_profile');
        Route::any('/change-password', [ProfileController::class, 'password'])->name('admin.change_password');

        // Page Routes
        Route::get('/page', [PageController::class, 'index'])->name('admin.page');
        Route::get('/page/create', [PageController::class, 'create'])->name('admin.page.create');
        Route::post('/page/store', [PageController::class, 'store'])->name('admin.page.store');
        Route::get('/page/destroy', [PageController::class, 'delete'])->name('admin.page.destroy');
        Route::get('/page/changeStatus', [PageController::class, 'changeStatus'])->name('admin.page.changestatus');
        Route::get('/page/view/{id}', [PageController::class, 'view'])->name('admin.page.view');
        Route::get('/page/edit/{id}', [PageController::class, 'edit'])->name('admin.page.edit');
        Route::post('/page/update/{id}', [PageController::class, 'update'])->name('admin.page.update');

        // User Routes
        Route::resource('users', UserController::class, ['as' => 'admin']);
        Route::get('users/{id}/status', [UserController::class, 'changeStatus'])->name('admin.users.changeStatus');
        Route::get('users/{id}/changeUserStatus', [UserController::class, 'changeUserStatus'])->name('admin.users.changeUserStatus');

        // Withdraw Request Routes
        Route::resource('withdraws', WithdrawController::class, ['as' => 'admin']);
        Route::post('withdraws/{id}/status', [WithdrawController::class, 'changeWithdrawStatus'])->name('admin.withdraws.changeStatus');

        // Setting Routes
        Route::group(['prefix' => 'settings'], function () {
            Route::get('logos', [SettingManagerController::class, 'getlogos'])->name('admin.settingtheme');
            Route::post('update-theme-images', [SettingManagerController::class, 'storeLogos'])->name('admin.setting.logo.update');

            Route::get('social', [SettingManagerController::class, 'getSocial'])->name('admin.social');
            Route::post('update-social', [SettingManagerController::class, 'storeSocial'])->name('admin.setting.social.update');

            Route::get('smtp', [SettingManagerController::class, 'getSmtpSetting'])->name('admin.setting.smtp');
            Route::post('save-smtp-settings', [SettingManagerController::class, 'updateSmtpSetting'])->name('admin.setting.smtp.update');
            Route::get('general', [SettingManagerController::class, 'getGeneralSetting'])->name('admin.setting.general');
            Route::get('general/add', [SettingManagerController::class, 'addGeneralSetting'])->name('admin.setting.general.add');
            Route::post('general/add', [SettingManagerController::class, 'storeGeneralSetting'])->name('admin.setting.general.store');
            Route::get('general/view/{id}', [SettingManagerController::class, 'showGeneralSetting'])->name('admin.setting.general.view');
            Route::get('general/edit/{id}', [SettingManagerController::class, 'editGeneralSetting'])->name('admin.setting.general.edit');
            Route::patch('general/{id}/edit', [SettingManagerController::class, 'updateGeneralSetting'])->name('admin.setting.general.update');
            Route::delete('general/destroy/{id}', [SettingManagerController::class, 'destroyGeneralSetting'])->name('admin.setting.general.destroy');

        });

        // Transaction History
        Route::resource('transactions', TransactionController::class, ['as' => 'admin']);
        
    });
});

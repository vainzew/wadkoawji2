<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\TelegramService; // Tambahkan import ini
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Daftarkan TelegramService sebagai singleton
        $this->app->singleton(TelegramService::class, function ($app) {
            return new TelegramService();
        });
        
        // View composers untuk Core UI layouts
        view()->composer('layouts.coreui-master', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('layouts.coreui.sidebar', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('layouts.coreui.header', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('layouts.coreui.footer', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('layouts.auth', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth.login', function ($view) {
            $view->with('setting', Setting::first());
        });
        
        // View composers untuk pengeluaran views
        view()->composer('pengeluaran.*', function ($view) {
            $view->with('setting', Setting::first());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS untuk production/cloudflare tunnel
        if (request()->header('x-forwarded-proto') == 'https' || app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share settings with all views
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $view->with('appSettings', Setting::getInstance());
                }
            } catch (\Exception $e) {
                // Silently fail if database not available
            }
        });
    }
}

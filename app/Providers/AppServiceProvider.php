<?php

namespace App\Providers;

use App\Models\BookingCharge;
use App\Models\GuestFolio;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Observers\BookingChargeObserver;
use App\Observers\GuestFolioObserver;
use App\Observers\InvoiceObserver;
use App\Observers\PaymentObserver;
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
        GuestFolio::observe(GuestFolioObserver::class);
        BookingCharge::observe(BookingChargeObserver::class);
        Payment::observe(PaymentObserver::class);
        Invoice::observe(InvoiceObserver::class);

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

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Production sunucuda public path düzeltmesi - ÖNCE BURASI ÇALIŞIR
        if (isset($_SERVER['DOCUMENT_ROOT']) && 
            strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
            $this->app->bind('path.public', function() {
                return realpath($_SERVER['DOCUMENT_ROOT']);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Carbon Türkçe dil desteği
        Carbon::setLocale('tr');
    }
}

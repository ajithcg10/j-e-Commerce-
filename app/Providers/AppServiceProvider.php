<?php

namespace App\Providers;

use App\CartService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class,function(){
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schedule::command('payout:vendors')->monthly(1,'00:00')->withoutOverlapping();
        Vite::prefetch(concurrency: 3);
        
    }
}

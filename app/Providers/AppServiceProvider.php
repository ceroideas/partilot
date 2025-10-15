<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Participation;
use App\Observers\UserObserver;
use App\Observers\ParticipationObserver;

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
        // Registrar Observer para vinculación automática de vendedores
        User::observe(UserObserver::class);
        
        // Registrar Observer para auditoría de participaciones
        Participation::observe(ParticipationObserver::class);
    }
}

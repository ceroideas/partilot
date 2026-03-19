<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        // Modo debug de correo: forzar TODOS los envíos a un email de pruebas.
        if (config('mail.debug_mode') && filled(config('mail.debug_to'))) {
            Mail::alwaysTo(config('mail.debug_to'));
            Log::info('MAIL_DEBUG_MODE activo: todos los emails se redirigen a ' . config('mail.debug_to'));
        }
    }
}

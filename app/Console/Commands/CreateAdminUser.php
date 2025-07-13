<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@partilot.com} {--password=admin123} {--name=Administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear un usuario administrador por defecto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Verificar si ya existe un usuario con ese email
        if (User::where('email', $email)->exists()) {
            $this->error("Ya existe un usuario con el email: {$email}");
            return 1;
        }

        // Crear el usuario administrador
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Usuario administrador creado exitosamente!");
        $this->info("Email: {$email}");
        $this->info("Contraseña: {$password}");
        $this->info("Nombre: {$name}");

        return 0;
    }
} 
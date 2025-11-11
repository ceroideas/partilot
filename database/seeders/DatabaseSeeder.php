<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Manager;
use App\Models\Administration;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@partilot.com',
            'password' => bcrypt(12345678),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Crear usuario para el manager
        $user = User::create([
            'name' => 'Jorge Ruiz Ortega',
            'email' => 'jorge@elbuholotero.com',
            'password' => bcrypt(12345678),
            'role' => User::ROLE_ADMINISTRATION,
        ]);

        // Crear manager
        $manager = Manager::create([
            'user_id' => $user->id,
            'name' => 'Jorge',
            'last_name' => 'Ruiz',
            'last_name2' => 'Ortega',
            'nif_cif' => '12345678A',
            'birthday' => '1985-03-15',
            'email' => 'jorge@elbuholotero.com',
            'phone' => '941 900 900',
            'comment' => 'Gestor principal de la administración El Buho Lotero',
        ]);

        // Crear administración
        Administration::create([
            'manager_id' => $manager->id,
            'web' => 'www.elbuholotero.es',
            'name' => 'El Buho Lotero',
            'receiving' => '05716',
            'society' => 'Jorge Ruiz Ortega',
            'nif_cif' => 'B26262626',
            'province' => 'La Rioja',
            'city' => 'Logroño',
            'postal_code' => '26001',
            'address' => 'Avd. Club Deportivo 28',
            'email' => 'info@elbuholotero.es',
            'phone' => '941 900 900',
            'account' => 'ES91 2100 0418 4502 0005 1332',
        ]);
    }
}

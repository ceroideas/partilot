<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Exception;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, eliminar las restricciones de clave foránea existentes si existen
        $this->dropForeignKeys();
        
        // Crear tablas temporales para guardar los datos existentes
        Schema::create('sellers_temp', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("last_name2")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("birthday")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("comment")->nullable();
            $table->integer("status")->nullable();
            $table->integer('entity_id')->nullable();
            $table->timestamps();
        });

        Schema::create('managers_temp', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("last_name2")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("birthday")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("comment")->nullable();
            $table->timestamps();
        });

        // Copiar datos existentes a tablas temporales
        if (Schema::hasTable('sellers')) {
            DB::statement('INSERT INTO sellers_temp SELECT * FROM sellers');
        }
        
        if (Schema::hasTable('managers')) {
            DB::statement('INSERT INTO managers_temp SELECT * FROM managers');
        }

        // Migrar datos de managers a users
        $this->migrateManagersToUsers();
        
        // Migrar datos de sellers a users
        $this->migrateSellersToUsers();
        
        // Eliminar las tablas existentes
        Schema::dropIfExists('sellers');
        Schema::dropIfExists('managers');
        
        // Crear la nueva estructura de sellers
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'entity_id']);
        });

        // Crear la nueva estructura de managers
        Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'entity_id']);
        });

        // Migrar las relaciones
        $this->migrateRelations();
        
        // Eliminar tablas temporales
        Schema::dropIfExists('sellers_temp');
        Schema::dropIfExists('managers_temp');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
        Schema::dropIfExists('managers');
        Schema::dropIfExists('sellers_temp');
        Schema::dropIfExists('managers_temp');
    }

    /**
     * Eliminar restricciones de clave foránea existentes
     */
    private function dropForeignKeys()
    {
        // Eliminar restricciones de sellers si existen
        if (Schema::hasTable('sellers')) {
            try {
                Schema::table('sellers', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropForeign(['entity_id']);
                });
            } catch (Exception $e) {
                // Las restricciones no existen, continuar
            }
        }

        // Eliminar restricciones de managers si existen
        if (Schema::hasTable('managers')) {
            try {
                Schema::table('managers', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropForeign(['entity_id']);
                });
            } catch (Exception $e) {
                // Las restricciones no existen, continuar
            }
        }
    }

    /**
     * Migrar datos de managers a users
     */
    private function migrateManagersToUsers()
    {
        $managers = DB::table('managers_temp')->get();
        
        foreach ($managers as $manager) {
            if ($manager->email) {
                // Verificar si ya existe un usuario con ese email
                $existingUser = DB::table('users')->where('email', $manager->email)->first();
                
                if (!$existingUser) {
                    // Crear nuevo usuario con los datos del manager
                    $userId = DB::table('users')->insertGetId([
                        'name' => $manager->name ?? '',
                        'last_name' => $manager->last_name ?? null,
                        'last_name2' => $manager->last_name2 ?? null,
                        'nif_cif' => $manager->nif_cif ?? null,
                        'birthday' => $manager->birthday ?? null,
                        'email' => $manager->email,
                        'phone' => $manager->phone ?? null,
                        'comment' => $manager->comment ?? null,
                        'image' => $manager->image ?? null,
                        'status' => true,
                        'password' => bcrypt('password'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Actualizar el manager con el nuevo user_id
                    DB::table('managers_temp')->where('id', $manager->id)->update(['user_id' => $userId]);
                } else {
                    // Usar el usuario existente
                    DB::table('managers_temp')->where('id', $manager->id)->update(['user_id' => $existingUser->id]);
                }
            }
        }
    }

    /**
     * Migrar datos de sellers a users
     */
    private function migrateSellersToUsers()
    {
        $sellers = DB::table('sellers_temp')->get();
        
        foreach ($sellers as $seller) {
            if ($seller->email) {
                // Verificar si ya existe un usuario con ese email
                $existingUser = DB::table('users')->where('email', $seller->email)->first();
                
                if (!$existingUser) {
                    // Crear nuevo usuario con los datos del seller
                    $userId = DB::table('users')->insertGetId([
                        'name' => $seller->name ?? '',
                        'last_name' => $seller->last_name ?? null,
                        'last_name2' => $seller->last_name2 ?? null,
                        'nif_cif' => $seller->nif_cif ?? null,
                        'birthday' => $seller->birthday ?? null,
                        'email' => $seller->email,
                        'phone' => $seller->phone ?? null,
                        'comment' => $seller->comment ?? null,
                        'image' => $seller->image ?? null,
                        'status' => $seller->status ?? true,
                        'password' => bcrypt('password'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Actualizar el seller con el nuevo user_id
                    DB::table('sellers_temp')->where('id', $seller->id)->update(['user_id' => $userId]);
                } else {
                    // Usar el usuario existente
                    DB::table('sellers_temp')->where('id', $seller->id)->update(['user_id' => $existingUser->id]);
                }
            }
        }
    }

    /**
     * Migrar las relaciones
     */
    private function migrateRelations()
    {
        // Migrar sellers
        $sellers = DB::table('sellers_temp')->whereNotNull('user_id')->whereNotNull('entity_id')->get();
        
        foreach ($sellers as $seller) {
            DB::table('sellers')->insert([
                'user_id' => $seller->user_id,
                'entity_id' => $seller->entity_id,
                'created_at' => $seller->created_at,
                'updated_at' => $seller->updated_at,
            ]);
        }

        // Migrar managers
        $managers = DB::table('managers_temp')->whereNotNull('user_id')->get();
        
        foreach ($managers as $manager) {
            // Buscar la entidad asociada al manager
            $entity = DB::table('entities')->where('manager_id', $manager->id)->first();
            
            if ($entity) {
                DB::table('managers')->insert([
                    'user_id' => $manager->user_id,
                    'entity_id' => $entity->id,
                    'created_at' => $manager->created_at,
                    'updated_at' => $manager->updated_at,
                ]);
            }
        }
    }
};

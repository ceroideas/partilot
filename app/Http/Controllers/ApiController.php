<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Exception;

class ApiController extends Controller
{
    public function test()
    {
        // Primero, actualizar la estructura de la tabla users
        $this->updateUsersTable();
        
        // Luego, eliminar las restricciones de clave foránea existentes si existen
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
            DB::statement('INSERT INTO sellers_temp (id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, status, entity_id, created_at, updated_at) 
                          SELECT id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, status, entity_id, created_at, updated_at FROM sellers');
        }
        
        if (Schema::hasTable('managers')) {
            DB::statement('INSERT INTO managers_temp (id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, created_at, updated_at) 
                          SELECT id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, created_at, updated_at FROM managers');
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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('administration_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'entity_id']);
            $table->unique(['user_id', 'administration_id']);
        });

        // Migrar las relaciones
        $this->migrateRelations();
        
        // Eliminar tablas temporales
        Schema::dropIfExists('sellers_temp');
        Schema::dropIfExists('managers_temp');

        // Eliminar la columna manager_id de entities si existe
        if (Schema::hasColumn('entities', 'manager_id')) {
            Schema::table('entities', function (Blueprint $table) {
                $table->dropColumn('manager_id');
            });
        }

        // Agregar la columna administration_id a managers si no existe
        if (!Schema::hasColumn('managers', 'administration_id')) {
            Schema::table('managers', function (Blueprint $table) {
                $table->foreignId('administration_id')->nullable()->constrained()->onDelete('cascade');
            });
        }

        return response()->json(['message' => 'Migración completada exitosamente']);
    }

    /**
     * Actualizar la estructura de la tabla users
     */
    private function updateUsersTable()
    {
        // Verificar si las columnas ya existen para evitar errores
        $columns = [
            'last_name',
            'last_name2', 
            'nif_cif',
            'birthday',
            'phone',
            'comment',
            'image',
            'status'
        ];

        foreach ($columns as $column) {
            if (!Schema::hasColumn('users', $column)) {
                try {
                    Schema::table('users', function (Blueprint $table) use ($column) {
                        switch ($column) {
                            case 'last_name':
                            case 'last_name2':
                            case 'nif_cif':
                            case 'birthday':
                            case 'phone':
                            case 'image':
                                $table->string($column)->nullable();
                                break;
                            case 'comment':
                                $table->text($column)->nullable();
                                break;
                            case 'status':
                                $table->boolean($column)->default(true);
                                break;
                        }
                    });
                } catch (Exception $e) {
                    // La columna ya existe o hay otro error, continuar
                }
            }
        }
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
                });
            } catch (Exception $e) {
                // La restricción no existe, continuar
            }
            
            try {
                Schema::table('sellers', function (Blueprint $table) {
                    $table->dropForeign(['entity_id']);
                });
            } catch (Exception $e) {
                // La restricción no existe, continuar
            }
        }

        // Eliminar restricciones de managers si existen
        if (Schema::hasTable('managers')) {
            try {
                Schema::table('managers', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (Exception $e) {
                // La restricción no existe, continuar
            }
            
            try {
                Schema::table('managers', function (Blueprint $table) {
                    $table->dropForeign(['entity_id']);
                });
            } catch (Exception $e) {
                // La restricción no existe, continuar
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
            // Buscar la entidad asociada al manager usando el manager_id original
            $entity = DB::table('entities')->where('manager_id', $manager->id)->first();
            
            // Buscar la administración asociada al manager usando el manager_id original
            $administration = DB::table('administrations')->where('manager_id', $manager->id)->first();
            
            if ($entity) {
                DB::table('managers')->insert([
                    'user_id' => $manager->user_id,
                    'entity_id' => $entity->id,
                    'administration_id' => null,
                    'created_at' => $manager->created_at,
                    'updated_at' => $manager->updated_at,
                ]);
            } elseif ($administration) {
                DB::table('managers')->insert([
                    'user_id' => $manager->user_id,
                    'entity_id' => null,
                    'administration_id' => $administration->id,
                    'created_at' => $manager->created_at,
                    'updated_at' => $manager->updated_at,
                ]);
            }
        }
    }

    public function checkParticipation(Request $r)
    {
        // Validar que el parámetro 'ref' esté presente
        $ref = $r->query('ref');
        if (!$ref) {
            return response()->json([
                'success' => false,
                'message' => 'El parámetro ref es obligatorio.'
            ], 400);
        }

        // Buscar el set que contenga el ticket con la referencia 'r' igual a $ref
        $set = \App\Models\Set::whereNotNull('tickets')->with(['reserve.lottery'])->get()->first(function($set) use ($ref) {
            if (!is_array($set->tickets)) return false;
            foreach ($set->tickets as $ticket) {
                if (isset($ticket['r']) && $ticket['r'] == $ref) {
                    return true;
                }
            }
            return false;
        });

        if (!$set) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún set con esa referencia.'
            ], 404);
        }

        // Retornar todos los datos del set, incluyendo reserve y reserve.lottery
        return response()->json([
            'success' => true,
            'set' => $set,
            'reserve' => $set->reserve,
            'entity' => $set->reserve ? $set->reserve->entity : null,
            'lottery' => $set->reserve ? $set->reserve->lottery : null
        ]);
    }

    public function showParticipationTicket(Request $request)
    {
        $ticket = null;
        $error = null;
        $prizeInfo = null;
        
        if ($request->has('ref')) {
            $response = $this->checkParticipation($request);
            $data = $response->getData(true);
            if (empty($data['success'])) {
                $error = $data['message'] ?? 'Error desconocido.';
            } else {
                $set = $data['set'];
                $reserve = $data['reserve'];
                $lottery = $data['lottery'];
                $ref = $request->query('ref');
                $ticketData = null;
                if (isset($set['tickets']) && is_array($set['tickets'])) {
                    foreach ($set['tickets'] as $t) {
                        if (isset($t['r']) && $t['r'] == $ref) {
                            $ticketData = $t;
                            break;
                        }
                    }
                }
                
                // Verificar si el sorteo tiene resultados
                $lotteryModel = \App\Models\Lottery::find($lottery['id']);
                if ($lotteryModel && $lotteryModel->results) {
                    $prizeInfo = $this->checkWinningNumbers($lotteryModel, $ticketData['numbers'] ?? [], $ticketData['total_participations'] ?? 0);
                }
                
                $ticket = [
                    'data' => $ticketData,
                    'set' => $set,
                    'reserve' => $reserve,
                    'lottery' => $lottery,
                    'prize_info' => $prizeInfo
                ];
            }
        }
        
        return view('social.participation-ticket', compact('ticket', 'error'));
    }

    private function checkWinningNumbers($lottery, $ticketNumbers, $totalParticipations)
    {
        if (!$lottery->results || !$ticketNumbers) {
            return null;
        }

        $results = $lottery->results;
        $prizeInfo = [
            'has_won' => false,
            'prize_category' => null,
            'prize_amount' => 0,
            'matching_numbers' => []
        ];

        // Verificar cada número del ticket
        foreach ($ticketNumbers as $number) {
            $numberStr = str_pad($number, 5, '0', STR_PAD_LEFT);
            
            // Verificar premios
            if (isset($results['premios'])) {
                foreach ($results['premios'] as $category => $prize) {
                    if (isset($prize['numero']) && $prize['numero'] == $numberStr) {
                        $prizeInfo['has_won'] = true;
                        $prizeInfo['prize_category'] = $category;
                        $prizeInfo['prize_amount'] = $prize['importe'] ?? 0;
                        $prizeInfo['matching_numbers'][] = $number;
                    }
                }
            }

            // Verificar reintegros
            if (isset($results['reintegros']) && in_array($numberStr, $results['reintegros'])) {
                $prizeInfo['has_won'] = true;
                $prizeInfo['prize_category'] = 'reintegro';
                $prizeInfo['prize_amount'] = 20; // Valor del reintegro
                $prizeInfo['matching_numbers'][] = $number;
            }
        }

        return $prizeInfo;
    }
} 
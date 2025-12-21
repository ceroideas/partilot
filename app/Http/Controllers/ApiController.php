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
        // Añadir campos lottery_type_code e is_special a la tabla lotteries
        // if (!Schema::hasColumn('lotteries', 'lottery_type_code')) {
        //     Schema::table('lotteries', function (Blueprint $table) {
        //         $table->string('lottery_type_code', 2)->nullable()->after('lottery_type_id')
        //               ->comment('Código del tipo de sorteo: J, X, S, N, B, V');
        //     });
        // }

        // if (!Schema::hasColumn('lotteries', 'is_special')) {
        //     Schema::table('lotteries', function (Blueprint $table) {
        //         $table->boolean('is_special')->default(false)->after('lottery_type_code')
        //               ->comment('Indica si es un sorteo especial (ej: 15€ Especial)');
        //     });
        // }

        // return response()->json([
        //     'message' => 'Migración completada exitosamente',
        //     'fields_added' => [
        //         'lottery_type_code' => 'string(2) nullable - Código del tipo de sorteo',
        //         'is_special' => 'boolean default(false) - Indica si es sorteo especial'
        //     ],
        //     'note' => 'lottery_types.identificador mantiene códigos simples (J,X,S,N,B,V) para compatibilidad'
        // ]);

        // Schema::table('scrutiny_entity_results', function (Blueprint $table) {
        //     $table->integer('total_non_winning')->default(0)->after('total_returned');
        // });

        // return;

        // Schema::dropIfExists('administration_lottery_scrutinies');
        // Schema::create('administration_lottery_scrutinies', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('administration_id')->onDelete('cascade');
        //     $table->foreignId('lottery_id')->onDelete('cascade');
        //     $table->foreignId('lottery_result_id')->nullable()->onDelete('cascade');
            
        //     // Metadatos del escrutinio
        //     $table->timestamp('scrutiny_date')->nullable();
        //     $table->boolean('is_scrutinized')->default(false);
        //     $table->json('scrutiny_summary')->nullable(); // Resumen: total premiadas, no premiadas, importe total
            
        //     // Información del usuario que realizó el escrutinio
        //     $table->foreignId('scrutinized_by')->nullable()->constrained('users')->onDelete('set null');
        //     $table->text('comments')->nullable();
            
        //     $table->timestamps();
            
        //     // Índices
        //     /*$table->index(['administration_id', 'lottery_id']);
        //     $table->index(['lottery_id', 'is_scrutinized']);
        //     $table->unique(['administration_id', 'lottery_id']); // Un escrutinio por administración por sorteo*/
        // });

        // Schema::dropIfExists('scrutiny_entity_results');
        // Schema::create('scrutiny_entity_results', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('administration_lottery_scrutiny_id')->onDelete('cascade');
        //     $table->foreignId('entity_id')->onDelete('cascade');
            
        //     // Datos de las reservas de la entidad
        //     $table->json('reserved_numbers')->nullable(); // Números reservados por la entidad
        //     $table->integer('total_reserved')->default(0); // Total de números reservados
        //     $table->integer('total_issued')->default(0); // Total emitidos
        //     $table->integer('total_sold')->default(0); // Total vendidos
        //     $table->integer('total_returned')->default(0); // Total devueltos
            
        //     // Resultados del escrutinio
        //     $table->json('winning_numbers')->nullable(); // Números ganadores de esta entidad
        //     $table->integer('total_winning')->default(0); // Total de números premiados
        //     $table->decimal('total_prize_amount', 15, 2)->default(0); // Importe total de premios
        //     $table->decimal('prize_per_participation', 10, 2)->default(0); // Premio por participación
            
        //     // Detalles de premios por categoría
        //     $table->json('prize_breakdown')->nullable(); // Desglose de premios: {tipo_premio: {numeros: [], importe: 0}}
            
        //     $table->timestamps();
            
        //     // Índices
        //     /*$table->index(['administration_lottery_scrutiny_id', 'entity_id']);
        //     $table->index(['entity_id', 'total_winning']);*/
        // });


        // return;
        // // Primero, actualizar la estructura de la tabla users
        // $this->updateUsersTable();
        
        // // Luego, eliminar las restricciones de clave foránea existentes si existen
        // $this->dropForeignKeys();
        
        // // Crear tablas temporales para guardar los datos existentes
        // Schema::create('sellers_temp', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('user_id')->nullable();
        //     $table->string("image")->nullable();
        //     $table->string("name")->nullable();
        //     $table->string("last_name")->nullable();
        //     $table->string("last_name2")->nullable();
        //     $table->string("nif_cif")->nullable();
        //     $table->string("birthday")->nullable();
        //     $table->string("email")->nullable();
        //     $table->string("phone")->nullable();
        //     $table->string("comment")->nullable();
        //     $table->integer("status")->nullable();
        //     $table->integer('entity_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('managers_temp', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('user_id')->nullable();
        //     $table->string("image")->nullable();
        //     $table->string("name")->nullable();
        //     $table->string("last_name")->nullable();
        //     $table->string("last_name2")->nullable();
        //     $table->string("nif_cif")->nullable();
        //     $table->string("birthday")->nullable();
        //     $table->string("email")->nullable();
        //     $table->string("phone")->nullable();
        //     $table->string("comment")->nullable();
        //     $table->timestamps();
        // });

        // // Copiar datos existentes a tablas temporales
        // if (Schema::hasTable('sellers')) {
        //     DB::statement('INSERT INTO sellers_temp (id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, status, entity_id, created_at, updated_at) 
        //                   SELECT id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, status, entity_id, created_at, updated_at FROM sellers');
        // }
        
        // if (Schema::hasTable('managers')) {
        //     DB::statement('INSERT INTO managers_temp (id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, created_at, updated_at) 
        //                   SELECT id, user_id, image, name, last_name, last_name2, nif_cif, birthday, email, phone, comment, created_at, updated_at FROM managers');
        // }

        // // Migrar datos de managers a users
        // $this->migrateManagersToUsers();
        
        // // Migrar datos de sellers a users
        // $this->migrateSellersToUsers();
        
        // // Eliminar las tablas existentes
        // Schema::dropIfExists('sellers');
        // Schema::dropIfExists('managers');
        
        // // Crear la nueva estructura de sellers
        // Schema::create('sellers', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('entity_id')->constrained()->onDelete('cascade');
        //     $table->timestamps();
        //     $table->unique(['user_id', 'entity_id']);
        // });

        // // Crear la nueva estructura de managers
        // Schema::create('managers', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->foreignId('entity_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->foreignId('administration_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->timestamps();
        //     $table->unique(['user_id', 'entity_id']);
        //     $table->unique(['user_id', 'administration_id']);
        // });

        // // Migrar las relaciones
        // $this->migrateRelations();
        
        // // Eliminar tablas temporales
        // Schema::dropIfExists('sellers_temp');
        // Schema::dropIfExists('managers_temp');

        // // Eliminar la columna manager_id de entities si existe
        // if (Schema::hasColumn('entities', 'manager_id')) {
        //     Schema::table('entities', function (Blueprint $table) {
        //         $table->dropColumn('manager_id');
        //     });
        // }

        // // Agregar la columna administration_id a managers si no existe
        // if (!Schema::hasColumn('managers', 'administration_id')) {
        //     Schema::table('managers', function (Blueprint $table) {
        //         $table->foreignId('administration_id')->nullable()->constrained()->onDelete('cascade');
        //     });
        // }

        // Agregar campos 'series' y 'billetes_serie' a la tabla lottery_types si no existen
        // $fieldsAdded = [];
        // if (!Schema::hasColumn('lottery_types', 'series')) {
        //     Schema::table('lottery_types', function (Blueprint $table) {
        //         $table->integer('series')->default(10)->after('ticket_price')->comment('Cantidad de series para el tipo de sorteo');
        //     });
        //     $fieldsAdded[] = 'series';
        // }
        // if (!Schema::hasColumn('lottery_types', 'billetes_serie')) {
        //     Schema::table('lottery_types', function (Blueprint $table) {
        //         $table->integer('billetes_serie')->default(100000)->after('series')->comment('Billetes por cada serie');
        //     });
        //     $fieldsAdded[] = 'billetes_serie';
        // }

        // if (!empty($fieldsAdded)) {
        //     return response()->json([
        //         'message' => 'Migración completada exitosamente',
        //         'fields_added' => $fieldsAdded
        //     ]);
        // }

        // return response()->json(['message' => 'No se realizaron cambios en la estructura de la base de datos']);

        Schema::table('sellers', function (Blueprint $table) {
            // Campos para vendedores externos (replicando users)
            $table->string('email')->nullable()->after('entity_id');
            $table->string('name')->nullable()->after('email');
            $table->string('last_name')->nullable()->after('name');
            $table->string('last_name2')->nullable()->after('last_name');
            $table->string('nif_cif')->nullable()->after('last_name2');
            $table->date('birthday')->nullable()->after('nif_cif');
            $table->boolean('status')->default(true)->after('birthday');
            $table->string('phone')->nullable()->after('status');
            $table->string('image')->nullable()->after('phone');
            
            // Campo para diferenciar tipo de vendedor
            $table->enum('seller_type', ['partilot', 'externo'])->default('partilot')->after('status');
            
            // Comentarios adicionales
            $table->text('comment')->nullable()->after('seller_type');
        });
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
        // Redirigir a la nueva URL externa manteniendo el parámetro ref
        if ($request->has('ref')) {
            $ref = $request->query('ref');
            $redirectUrl = 'https://web.elbuholotero.es/loteria-empresas-parti.php?ref=' . urlencode($ref);
            return redirect($redirectUrl);
        }
        
        // Si no hay ref, redirigir sin parámetro
        return redirect('https://web.elbuholotero.es/loteria-empresas-parti.php');
        
        /* Código anterior comentado - redirección implementada
        $ticket = null;
        $error = null;
        $prizeInfo = null;
        
        if ($request->has('ref')) {
            $ref = $request->query('ref');
            
            // Buscar el set que contiene la referencia en tickets
            $set = \App\Models\Set::whereNotNull('tickets')
                ->with(['reserve.lottery', 'reserve.entity'])
                ->get()
                ->first(function($set) use ($ref) {
                    if (!is_array($set->tickets)) return false;
                    foreach ($set->tickets as $ticket) {
                        if (isset($ticket['r']) && $ticket['r'] == $ref) {
                            return true;
                        }
                    }
                    return false;
                });
            
            if (!$set) {
                $error = 'No se encontró ninguna participación con esa referencia.';
            } else {
                // Encontrar el número de participación correspondiente a la referencia
                $participationNumber = null;
                foreach ($set->tickets as $ticket) {
                    if (isset($ticket['r']) && $ticket['r'] == $ref) {
                        $participationNumber = $ticket['n'];
                            break;
                        }
                }
                
                /*\Log::info("Set Tickets: " . json_encode($set->tickets));
                \Log::info("Looking for ref: " . $ref);
                \Log::info("Found participation number: " . ($participationNumber ?? 'NULL'));*/
                
                // Buscar la participación por set_id y participation_number
                $participation = \App\Models\Participation::where('set_id', $set->id)
                    ->where('participation_number', $participationNumber)
                    ->first();

                    // return $participation;
                
                if (!$participation) {
                    $error = 'No se encontró la participación correspondiente a esa referencia.';
                } else if ($participation->status !== 'vendida') {
                    \Log::info("Participation Status: " . $participation->status);
                    $error = 'Esta participación no está asignada.';
                } else {
                    \Log::info("Participation Status: " . $participation->status . " (OK)");
                    $reserve = $set->reserve;
                    $lottery = $reserve->lottery;
                    
                    // Obtener los números ganadores desde reservation_numbers
                    $reservedNumbers = $reserve->reservation_numbers ?? [];
                    
                    // Manejar todos los números reservados como posibles ganadores
                    $winningNumbers = [];
                    
                    // Si reservation_numbers tiene solo 1 número, todas las participaciones
                    // del set tienen el mismo número ganador
                    if (count($reservedNumbers) == 1) {
                        $winningNumbers = $reservedNumbers;
                    } else {
                        // Si hay múltiples números, usar todos los números reservados
                        $winningNumbers = $reservedNumbers;
                    }
                    
                    /*\Log::info("Reserved Numbers: " . json_encode($reservedNumbers));
                    \Log::info("Participation Number: " . $participationNumber);
                    \Log::info("Winning Number Index: " . ($participationNumber - 1));
                    \Log::info("Reserved Numbers Count: " . count($reservedNumbers));
                    \Log::info("Winning Number: " . ($winningNumber ?? 'NULL'));*/
                
                // Debug: Log de información
                /*\Log::info("=== DEBUG PARTICIPATION TICKET ===");
                \Log::info("Winning Number: " . ($winningNumber ?? 'NULL'));
                \Log::info("Set ID: " . $set->id);
                \Log::info("Participation Number: " . $participationNumber);*/
                
                // Buscar en los resultados del escrutinio guardado para todos los números ganadores
                $scrutinyResults = [];
                $totalPrizeAmount = 0;
                $allWinningCategories = [];
                
                if (!empty($winningNumbers)) {
                    $scrutinyResults = DB::table('scrutiny_detailed_results')
                        ->join('administration_lottery_scrutinies', 'scrutiny_detailed_results.scrutiny_id', '=', 'administration_lottery_scrutinies.id')
                        ->whereIn('scrutiny_detailed_results.winning_number', $winningNumbers)
                        ->where('scrutiny_detailed_results.set_id', $set->id)
                        ->where('administration_lottery_scrutinies.is_scrutinized', true) // Buscar en escrutinios procesados, no solo guardados
                        ->select('scrutiny_detailed_results.*')
                        ->get();
                    
                    \Log::info("Scrutiny Results Found: " . $scrutinyResults->count());
                    
                    // Calcular premio total y categorías ganadoras
                    foreach ($scrutinyResults as $result) {
                        $totalPrizeAmount += $result->premio_por_participacion;
                        $categories = json_decode($result->winning_categories, true);
                        if (is_array($categories)) {
                            // Construir estructura correcta para la vista
                            foreach ($categories as $category) {
                                if (is_array($category) && isset($category['categoria']) && isset($category['premio_decimo'])) {
                                    $allWinningCategories[] = $category;
                                } elseif (is_string($category) && !empty(trim($category))) {
                                    // Si es solo un string, crear estructura básica
                                    $allWinningCategories[] = [
                                        'categoria' => $category,
                                        'premio_decimo' => $result->premio_por_decimo ?? 0
                                    ];
                                }
                            }
                        }
                    }
                }
                
                if ($scrutinyResults->count() > 0) {
                    // Usar datos del escrutinio guardado
                    $prizeInfo = [
                        'has_won' => true,
                        'prize_category' => 'Premio del Escrutinio',
                        'prize_amount' => $totalPrizeAmount,
                        'matching_numbers' => $winningNumbers,
                        'winning_categories' => $allWinningCategories,
                        'scrutiny_results_count' => $scrutinyResults->count()
                    ];
                } else {
                    // No hay premio en el escrutinio guardado
                    $prizeInfo = [
                        'has_won' => false,
                        'prize_category' => null,
                        'prize_amount' => 0,
                        'matching_numbers' => $winningNumbers,
                        'winning_categories' => []
                    ];
                }
                
                $ticket = [
                    'data' => [
                        'participation_code' => $participation->participation_code, // El participation_code de la participación (1/00046)
                        'participation_number' => $ref, // La referencia original buscada (000100061758806276046)
                        'numbers' => $reservedNumbers,
                        'winning_numbers' => $winningNumbers
                    ],
                    'set' => $set,
                    'reserve' => $reserve,
                    'lottery' => $lottery,
                    'prize_info' => $prizeInfo
                ];
                }
            }
        }
        
        return view('social.participation-ticket', compact('ticket', 'error'));
    }

    private function checkWinningNumbers($lottery, $ticketNumbers, $totalParticipations)
    {
        if (!$lottery->result || !$ticketNumbers) {
            return null;
        }

        $result = $lottery->result;
        $prizeInfo = [
            'has_won' => false,
            'prize_category' => null,
            'prize_amount' => 0,
            'matching_numbers' => []
        ];

        // Verificar cada número del ticket
        foreach ($ticketNumbers as $number) {
            $numberStr = str_pad($number, 5, '0', STR_PAD_LEFT);
            
            // Verificar primer premio
            if ($result->primer_premio && isset($result->primer_premio['decimo']) && $result->primer_premio['decimo'] == $numberStr) {
                $prizeInfo['has_won'] = true;
                $prizeInfo['prize_category'] = 'Primer Premio';
                $prizeInfo['prize_amount'] = ($result->primer_premio['prize'] ?? 0) / 100; // Convertir de céntimos a euros
                $prizeInfo['matching_numbers'][] = $number;
                continue;
            }
            
            // Verificar segundo premio
            if ($result->segundo_premio && isset($result->segundo_premio['decimo']) && $result->segundo_premio['decimo'] == $numberStr) {
                $prizeInfo['has_won'] = true;
                $prizeInfo['prize_category'] = 'Segundo Premio';
                $prizeInfo['prize_amount'] = ($result->segundo_premio['prize'] ?? 0) / 100;
                $prizeInfo['matching_numbers'][] = $number;
                continue;
            }
            
            // Verificar terceros premios
            if ($result->terceros_premios) {
                foreach ($result->terceros_premios as $premio) {
                    if (isset($premio['decimo']) && $premio['decimo'] == $numberStr) {
                        $prizeInfo['has_won'] = true;
                        $prizeInfo['prize_category'] = 'Tercer Premio';
                        $prizeInfo['prize_amount'] = ($premio['prize'] ?? 0) / 100;
                        $prizeInfo['matching_numbers'][] = $number;
                        break;
                    }
                }
            }
            
            // Verificar cuartos premios
            if ($result->cuartos_premios) {
                foreach ($result->cuartos_premios as $premio) {
                    if (isset($premio['decimo']) && $premio['decimo'] == $numberStr) {
                        $prizeInfo['has_won'] = true;
                        $prizeInfo['prize_category'] = 'Cuarto Premio';
                        $prizeInfo['prize_amount'] = ($premio['prize'] ?? 0) / 100;
                        $prizeInfo['matching_numbers'][] = $number;
                        break;
                    }
                }
            }
            
            // Verificar quintos premios
            if ($result->quintos_premios) {
                foreach ($result->quintos_premios as $premio) {
                    if (isset($premio['decimo']) && $premio['decimo'] == $numberStr) {
                        $prizeInfo['has_won'] = true;
                        $prizeInfo['prize_category'] = 'Quinto Premio';
                        $prizeInfo['prize_amount'] = ($premio['prize'] ?? 0) / 100;
                        $prizeInfo['matching_numbers'][] = $number;
                        break;
                    }
                }
            }

            // Verificar reintegros
            if ($result->reintegros) {
                foreach ($result->reintegros as $reintegro) {
                    if (isset($reintegro['decimo']) && $reintegro['decimo'] == substr($numberStr, -1)) {
                $prizeInfo['has_won'] = true;
                        $prizeInfo['prize_category'] = 'Reintegro';
                        $prizeInfo['prize_amount'] = ($reintegro['prize'] ?? 0) / 100;
                $prizeInfo['matching_numbers'][] = $number;
                        break;
                    }
                }
            }
        }

        return $prizeInfo;
    }

    public function checkDelete($type, $id)
    {
        $canDelete = true;
        $message = '';

        switch ($type) {
            case 'set':
                $set = \App\Models\Set::find($id);
                if ($set) {
                    if ($set->participations()->count() > 0) {
                        $canDelete = false;
                        $message = 'El set no se puede borrar porque tiene participaciones asociadas.';
                    } elseif ($set->designFormats()->count() > 0) {
                        $canDelete = false;
                        $message = 'El set no se puede borrar porque tiene diseños asociados.';
                    }
                }
                break;
            case 'reserve':
                $reserve = \App\Models\Reserve::find($id);
                if ($reserve) {
                    if ($reserve->sets()->count() > 0) {
                        $canDelete = false;
                        $message = 'La reserva no se puede borrar porque tiene sets asociados.';
                    }
                }
                break;
            case 'lottery':
                $lottery = \App\Models\Lottery::find($id);
                if ($lottery) {
                    if ($lottery->reserves()->count() > 0) {
                        $canDelete = false;
                        $message = 'El sorteo no se puede borrar porque tiene reservas asociadas.';
                    } elseif ($lottery->participations()->count() > 0) {
                        $canDelete = false;
                        $message = 'El sorteo no se puede borrar porque tiene participaciones asociadas.';
                    } elseif ($lottery->result()) {
                        $canDelete = false;
                        $message = 'El sorteo no se puede borrar porque tiene resultados asociados.';
                    } elseif ($lottery->administrationScrutinies()->count() > 0) {
                        $canDelete = false;
                        $message = 'El sorteo no se puede borrar porque tiene escrutinios asociados.';
                    }
                }
                break;
            case 'entity':
                $entity = \App\Models\Entity::find($id);
                if ($entity) {
                    if ($entity->reserves()->count() > 0) {
                        $canDelete = false;
                        $message = 'La entidad no se puede borrar porque tiene reservas asociadas.';
                    } elseif ($entity->scrutinyResults()->count() > 0) {
                        $canDelete = false;
                        $message = 'La entidad no se puede borrar porque tiene resultados de escrutinio asociados.';
                    } elseif ($entity->sellers()->count() > 0) {
                        $canDelete = false;
                        $message = 'La entidad no se puede borrar porque tiene vendedores asociados.';
                    }
                }
                break;
            case 'administration':
                $administration = \App\Models\Administration::find($id);
                if ($administration) {
                    if ($administration->entities()->count() > 0) {
                        $canDelete = false;
                        $message = 'La administración no se puede borrar porque tiene entidades asociadas.';
                    } elseif ($administration->manager()) {
                        $canDelete = false;
                        $message = 'La administración no se puede borrar porque tiene un manager asociado.';
                    } elseif ($administration->lotteryScrutinies()->count() > 0) {
                        $canDelete = false;
                        $message = 'La administración no se puede borrar porque tiene escrutinios asociados.';
                    }
                }
                break;
            case 'user':
                // Para usuarios, quizás no hay restricciones, o verificar si es super_admin
                $user = \App\Models\User::find($id);
                if ($user && $user->role === 'super_admin') {
                    $canDelete = false;
                    $message = 'El usuario super administrador no se puede borrar.';
                }
                break;
            case 'lottery_type':
                $lotteryType = \App\Models\LotteryType::find($id);
                if ($lotteryType) {
                    if ($lotteryType->lotteries()->count() > 0) {
                        $canDelete = false;
                        $message = 'El tipo de lotería no se puede borrar porque tiene sorteos asociados.';
                    }
                }
                break;
            default:
                $canDelete = false;
                $message = 'Tipo no reconocido.';
        }

        return response()->json(['can_delete' => $canDelete, 'message' => $message]);
    }

    public function deleteItem($type, $id)
    {
        switch ($type) {
            case 'set':
                \App\Models\Set::find($id)->delete();
                break;
            case 'reserve':
                \App\Models\Reserve::find($id)->delete();
                break;
            case 'lottery':
                \App\Models\Lottery::find($id)->delete();
                break;
            case 'entity':
                \App\Models\Entity::find($id)->delete();
                break;
            case 'administration':
                \App\Models\Administration::find($id)->delete();
                break;
            case 'user':
                \App\Models\User::find($id)->delete();
                break;
            case 'lottery_type':
                \App\Models\LotteryType::find($id)->delete();
                break;
        }

        return response()->json(['success' => true]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiController extends Controller
{
    //
    public function test()
    {
        /*Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('managers', function (Blueprint $table) {
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
            $table->timestamps();
        });

        Schema::create('administrations', function (Blueprint $table) {
            $table->id();
            $table->string("manager_id")->nullable();
            $table->string("web")->nullable();
            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("receiving")->nullable();
            $table->string("society")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("province")->nullable();
            $table->string("city")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("address")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("account")->nullable();
            $table->integer("status")->nullable();
            $table->timestamps();
        });

        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->integer('administration_id')->nullable();
            $table->integer('manager_id')->nullable();
            $table->string("image")->nullable();
            $table->string("name")->nullable();
            $table->string("province")->nullable();
            $table->string("city")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("address")->nullable();
            $table->string("nif_cif")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("comments")->nullable();
            $table->integer("status")->nullable();
            $table->timestamps();
        });

        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@partilot.com',
            'password' => bcrypt(12345678),
        ]);

        Schema::dropIfExists('lotteries');
        Schema::create('lotteries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Número/Nombre del sorteo
            $table->text('description')->nullable(); // Descripción del sorteo
            $table->date('draw_date')->nullable(); // Fecha del sorteo
            $table->time('draw_time')->nullable(); // Hora del sorteo
            $table->date('deadline_date')->nullable(); // Fecha límite
            $table->decimal('ticket_price', 10, 2)->nullable(); // Precio del décimo
            $table->integer('total_tickets')->nullable(); // Total de boletos disponibles
            $table->integer('sold_tickets')->default(0); // Boletos vendidos
            $table->string('prize_description')->nullable(); // Descripción del premio
            $table->decimal('prize_value', 10, 2)->nullable(); // Valor del premio
            $table->string('image')->nullable(); // Imagen del sorteo
            $table->integer('status')->default(1);
            $table->integer('lottery_type_id');
            $table->timestamps();
        });

        Schema::create('lottery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nombre del tipo de sorteo
            $table->decimal('ticket_price', 10, 2)->nullable(); // Precio del décimo
            $table->json('prize_categories')->nullable(); // Categorías de premios en JSON
            $table->boolean('is_active')->default(true); // Si está activo
            $table->timestamps();
        });

        Schema::dropIfExists('reserves');
        Schema::create('reserves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            $table->json('reservation_numbers'); // Array de números reservados
            $table->decimal('total_amount', 10, 2);
            $table->integer('total_tickets');
            $table->decimal('reservation_amount', 10, 2); // Importe a reservar
            $table->integer('reservation_tickets'); // Cantidad de décimos
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('reservation_date');
            $table->timestamp('expiration_date')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('sets');
        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('reserve_id')->constrained('reserves')->onDelete('cascade');
            
            // Información del set
            $table->string('set_name');
            $table->text('set_description')->nullable();
            
            // Configuración de participaciones
            $table->integer('total_participations')->nullable();
            $table->decimal('participation_price', 10, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            
            // Campos adicionales para tipos de participaciones
            $table->decimal('played_amount', 10, 2)->nullable(); // Importe jugado por número
            $table->decimal('donation_amount', 10, 2)->nullable(); // Importe donativo
            $table->decimal('total_participation_amount', 10, 2)->nullable(); // Importe total participación
            $table->integer('physical_participations')->default(0); // Participaciones físicas
            $table->integer('digital_participations')->default(0); // Participaciones digitales
            $table->date('deadline_date')->nullable(); // Fecha límite
            
            // Estado (0=inactivo, 1=activo, 2=pausado)
            $table->tinyInteger('status')->default(1);
            
            // Timestamps
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['entity_id', 'status']);
            $table->index(['reserve_id']);
            $table->index('status');
        });*/

        /*Schema::table('sets', function(Blueprint $table) {
            //
            $table->json('tickets')->nullable();
        });*/

        /*Schema::dropIfExists('design_formats');
        Schema::create('design_formats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('lottery_id');
            $table->unsignedBigInteger('set_id');
            $table->string('format')->nullable();
            $table->string('page')->nullable();
            $table->integer('rows')->nullable();
            $table->integer('cols')->nullable();
            $table->string('orientation')->nullable();
            $table->decimal('margin_up', 8, 2)->nullable();
            $table->decimal('margin_right', 8, 2)->nullable();
            $table->decimal('margin_left', 8, 2)->nullable();
            $table->decimal('margin_top', 8, 2)->nullable();
            $table->decimal('identation', 8, 2)->nullable();
            $table->decimal('matrix_box', 8, 2)->nullable();
            $table->decimal('page_rigth', 8, 2)->nullable();
            $table->decimal('page_bottom', 8, 2)->nullable();
            $table->string('guide_color', 20)->nullable();
            $table->decimal('guide_weight', 8, 2)->nullable();
            $table->integer('participation_number')->nullable();
            $table->integer('participation_from')->nullable();
            $table->integer('participation_to')->nullable();
            $table->integer('participation_page')->nullable();
            $table->boolean('guides')->nullable();
            $table->string('generate', 10)->nullable();
            $table->string('documents', 10)->nullable();

            $table->decimal('horizontal_space', 8,2)->nullable();
            $table->decimal('vertical_space', 8,2)->nullable();
            $table->decimal('margin_custom', 8,2)->nullable();

            $table->json('blocks')->nullable(); // HTML de los containment-wrapper
            $table->longText('participation_html')->nullable();
            $table->longText('cover_html')->nullable();
            $table->longText('back_html')->nullable();
            $table->json('backgrounds')->nullable();
            $table->json('output')->nullable();
            $table->timestamps();

            // Foreign keys (opcional, si existen las tablas referenciadas)
            // $table->foreign('entity_id')->references('id')->on('entities');
            // $table->foreign('lottery_id')->references('id')->on('lotteries');
            // $table->foreign('set_id')->references('id')->on('sets');
        });*/

        /*Schema::table('design_formats', function(Blueprint $table) {
            //
            $table->decimal('horizontal_space', 8,2)->nullable();
            $table->decimal('vertical_space', 8,2)->nullable();
            $table->decimal('margin_custom', 8,2)->nullable();
            $table->longText('margins')->nullable();
        });*/

        /*Schema::table('lottery_types', function (Blueprint $table) {
            $table->string('identificador', 2)->nullable()->after('name');
        });*/

        Schema::create('lottery_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_id')->constrained('lotteries')->onDelete('cascade');
            
            // Premio Especial
            $table->json('premio_especial')->nullable();
            
            // Primer Premio
            $table->json('primer_premio')->nullable();
            
            // Segundo Premio
            $table->json('segundo_premio')->nullable();
            
            // Arrays de premios
            $table->json('terceros_premios')->nullable();
            $table->json('cuartos_premios')->nullable();
            $table->json('quintos_premios')->nullable();
            
            // Arrays de extracciones
            $table->json('extracciones_cinco_cifras')->nullable();
            $table->json('extracciones_cuatro_cifras')->nullable();
            $table->json('extracciones_tres_cifras')->nullable();
            $table->json('extracciones_dos_cifras')->nullable();
            
            // Reintegros
            $table->json('reintegros')->nullable();
            
            // Metadatos
            $table->timestamp('results_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['lottery_id', 'results_date']);
            $table->index('is_published');
        });


        // https://www.loteriasyapuestas.es/servicios/buscadorSorteos?game_id=LNAC&celebrados=false&fechaInicioInclusiva=20250802&fechaFinInclusiva=20250802
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

    /**
     * Muestra la vista personalizada del ticket de participación.
     */
    public function showParticipationTicket(Request $request)
    {
        $ticket = null;
        $error = null;
        if ($request->has('ref')) {
            $response = $this->checkParticipation($request);
            $data = $response->getData(true); // array asociativo
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
                $ticket = [
                    'titulo' => $reserve['entity']['name'] ?? 'Participación',
                    'sorteo' => $lottery['description'] ?? '',
                    'numeros' => implode(' - ', $reserve['reservation_numbers']),
                    'jugado' => $reserve['total_amount'],
                    'serie' => $ticketData['n'] ?? '',
                    'referencia' => $ticketData['r'] ?? $ref,
                ];
            }
        } else {
            $error = 'Referencia de ticket no proporcionada.';
        }
        return view('participation_ticket', compact('ticket', 'error'));
    }
}

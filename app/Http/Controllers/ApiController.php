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

        Schema::table('sets', function(Blueprint $table) {
            //
            $table->json('tickets')->nullable();
        });

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
        $set = \App\Models\Set::whereNotNull('tickets')->get()->first(function($set) use ($ref) {
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

        // Retornar todos los datos del set
        return response()->json([
            'success' => true,
            'set' => $set
        ]);
    }
}

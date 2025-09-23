<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'draw_date',
        'draw_time',
        'deadline_date',
        'ticket_price',
        'total_tickets',
        'sold_tickets',
        'prize_description',
        'prize_value',
        'image',
        'status',
        'lottery_type_id',
        'lottery_type_code', // J, X, S, N, B, V
        'is_special' // Para sorteos especiales como 15€ Especial
    ];

    protected $casts = [
        'draw_date' => 'date',
        'deadline_date' => 'date',
        'draw_time' => 'datetime',
        'ticket_price' => 'decimal:2',
        'prize_value' => 'decimal:2',
        'is_special' => 'boolean',
    ];

    // Relación con Tipo de Lotería
    public function lotteryType()
    {
        return $this->belongsTo(LotteryType::class, 'lottery_type_id');
    }

    // Relación con Participaciones
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    // Relación con Reservas
    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }

    // Relación con Resultados
    public function result()
    {
        return $this->hasOne(LotteryResult::class);
    }

    // Relación con los escrutinios de administraciones
    public function administrationScrutinies()
    {
        return $this->hasMany(AdministrationLotteryScrutiny::class);
    }

    /**
     * Verificar si una administración ha escrutado este sorteo
     */
    public function isScrutinizedByAdministration($administrationId)
    {
        return $this->administrationScrutinies()
            ->where('administration_id', $administrationId)
            ->where('is_scrutinized', true)
            ->exists();
    }

    /**
     * Obtener el escrutinio de una administración específica
     */
    public function getAdministrationScrutiny($administrationId)
    {
        return $this->administrationScrutinies()
            ->where('administration_id', $administrationId)
            ->first();
    }

    /**
     * Obtener el identificador único del tipo de sorteo
     * Combina precio + código + especial para identificar exactamente el tipo
     */
    public function getLotteryTypeIdentifier()
    {
        // Convertir ticket_price a entero para evitar decimales
        $ticketPrice = intval($this->ticket_price);
        $identifier = $ticketPrice . '_' . $this->lottery_type_code;
        
        // Manejar casos especiales
        if ($this->is_special && $this->lottery_type_code == 'S' && $ticketPrice == 15) {
            $identifier .= '_ESPECIAL';
        }
        
        return $identifier;
    }

    /**
     * Obtener la configuración del tipo de sorteo
     */
    public function getLotteryTypeConfig()
    {
        $identifier = $this->getLotteryTypeIdentifier();
        $lotteryTypes = config('lotteryTypes');
        
        return $lotteryTypes[$identifier] ?? null;
    }

    /**
     * Obtener las categorías de premios aplicables para este sorteo
     */
    public function getApplicablePrizeCategories()
    {
        $identifier = $this->getLotteryTypeIdentifier();
        $categories = config('lotteryCategories');
        
        $applicableCategories = [];
        
        foreach ($categories as $category) {
            $prizeAmount = $category['importe_por_tipo'][$identifier] ?? 0;
            $prizeCount = is_array($category['cantidad_premios']) 
                ? ($category['cantidad_premios'][$identifier] ?? 0)
                : $category['cantidad_premios'];
            
            if ($prizeAmount > 0 && $prizeCount > 0) {
                $applicableCategories[] = array_merge($category, [
                    'importe_aplicable' => $prizeAmount,
                    'cantidad_aplicable' => $prizeCount
                ]);
            }
        }
        
        return $applicableCategories;
    }

    /**
     * Verificar si este sorteo tiene un tipo específico de premio
     */
    public function hasPrizeCategory($categoryKey)
    {
        $identifier = $this->getLotteryTypeIdentifier();
        $categories = config('lotteryCategories');
        
        $category = collect($categories)->firstWhere('key_categoria', $categoryKey);
        
        if (!$category) {
            return false;
        }
        
        $prizeAmount = $category['importe_por_tipo'][$identifier] ?? 0;
        $prizeCount = is_array($category['cantidad_premios']) 
            ? ($category['cantidad_premios'][$identifier] ?? 0)
            : $category['cantidad_premios'];
        
        return $prizeAmount > 0 && $prizeCount > 0;
    }
}

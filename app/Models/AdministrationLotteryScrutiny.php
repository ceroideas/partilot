<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrationLotteryScrutiny extends Model
{
    use HasFactory;

    protected $fillable = [
        'administration_id',
        'lottery_id',
        'lottery_result_id',
        'scrutiny_date',
        'is_scrutinized',
        'is_saved',
        'saved_at',
        'saved_by',
        'scrutiny_summary',
        'scrutinized_by',
        'comments'
    ];

    protected $casts = [
        'scrutiny_date' => 'datetime',
        'is_scrutinized' => 'boolean',
        'is_saved' => 'boolean',
        'saved_at' => 'datetime',
        'scrutiny_summary' => 'array'
    ];

    /**
     * Relación con Administration
     */
    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Relación con Lottery
     */
    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    /**
     * Relación con LotteryResult
     */
    public function lotteryResult()
    {
        return $this->belongsTo(LotteryResult::class);
    }

    /**
     * Relación con el usuario que realizó el escrutinio
     */
    public function scrutinizedBy()
    {
        return $this->belongsTo(User::class, 'scrutinized_by');
    }

    /**
     * Relación con el usuario que guardó el escrutinio
     */
    public function savedBy()
    {
        return $this->belongsTo(User::class, 'saved_by');
    }

    /**
     * Relación con los resultados detallados del escrutinio
     */
    public function detailedResults()
    {
        return $this->hasMany(ScrutinyDetailedResult::class, 'scrutiny_id');
    }

    /**
     * Obtener el resumen del escrutinio
     */
    public function getScrutinySummaryAttribute($value)
    {
        $summary = json_decode($value, true);
        
        return [
            'total_entities' => $summary['total_entities'] ?? 0,
            'total_winning_participations' => $summary['total_winning_participations'] ?? 0,
            'total_non_winning_participations' => $summary['total_non_winning_participations'] ?? 0,
            'total_prize_amount' => $summary['total_prize_amount'] ?? 0.00
        ];
    }

    /**
     * Establecer el resumen del escrutinio
     */
    public function setScrutinySummaryAttribute($value)
    {
        $this->attributes['scrutiny_summary'] = json_encode($value);
    }

    /**
     * Scope para escrutinios completados
     */
    public function scopeScrutinized($query)
    {
        return $query->where('is_scrutinized', true);
    }

    /**
     * Scope para escrutinios pendientes
     */
    public function scopePending($query)
    {
        return $query->where('is_scrutinized', false);
    }

    /**
     * Scope para escrutinios guardados
     */
    public function scopeSaved($query)
    {
        return $query->where('is_saved', true);
    }

    /**
     * Scope para escrutinios no guardados
     */
    public function scopeNotSaved($query)
    {
        return $query->where('is_saved', false);
    }

    /**
     * Scope por administración
     */
    public function scopeByAdministration($query, $administrationId)
    {
        return $query->where('administration_id', $administrationId);
    }

    /**
     * Calcular automáticamente el resumen cuando se guardan los resultados de entidades
     */
    public function calculateSummary()
    {
        $entityResults = $this->entityResults()->get();
        
        $summary = [
            'total_entities' => $entityResults->count(),
            'total_winning_participations' => $entityResults->sum('winning_participations'),
            'total_non_winning_participations' => $entityResults->sum('total_reserved') - $entityResults->sum('winning_participations'),
            'total_prize_amount' => $entityResults->sum('total_prize_amount')
        ];

        $this->scrutiny_summary = $summary;
        $this->save();

        return $summary;
    }
}

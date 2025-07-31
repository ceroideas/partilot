<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'lottery_id',
        'premio_especial',
        'primer_premio',
        'segundo_premio',
        'terceros_premios',
        'cuartos_premios',
        'quintos_premios',
        'extracciones_cinco_cifras',
        'extracciones_cuatro_cifras',
        'extracciones_tres_cifras',
        'extracciones_dos_cifras',
        'reintegros',
        'results_date',
        'is_published'
    ];

    protected $casts = [
        'premio_especial' => 'array',
        'primer_premio' => 'array',
        'segundo_premio' => 'array',
        'terceros_premios' => 'array',
        'cuartos_premios' => 'array',
        'quintos_premios' => 'array',
        'extracciones_cinco_cifras' => 'array',
        'extracciones_cuatro_cifras' => 'array',
        'extracciones_tres_cifras' => 'array',
        'extracciones_dos_cifras' => 'array',
        'reintegros' => 'array',
        'results_date' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected $appends = ['refunds','thirds','fourths','fifths','5figures','4figures','3figures','2figures'];

    // Relación con Lottery
    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }



    // Método para verificar si tiene resultados
    public function hasResults()
    {
        return !empty($this->premio_especial) || 
               !empty($this->primer_premio) || 
               !empty($this->segundo_premio);
    }

    // Método para obtener todos los premios como array
    public function getAllPrizes()
    {
        return [
            'premioEspecial' => $this->premio_especial,
            'primerPremio' => $this->primer_premio,
            'segundoPremio' => $this->segundo_premio,
            'tercerosPremios' => $this->terceros_premios ?? [],
            'cuartosPremios' => $this->cuartos_premios ?? [],
            'quintosPremios' => $this->quintos_premios ?? [],
            'extraccionesDeCincoCifras' => $this->extracciones_cinco_cifras ?? [],
            'extraccionesDeCuatroCifras' => $this->extracciones_cuatro_cifras ?? [],
            'extraccionesDeTresCifras' => $this->extracciones_tres_cifras ?? [],
            'extraccionesDeDosCifras' => $this->extracciones_dos_cifras ?? [],
            'reintegros' => $this->reintegros ?? [],
        ];
    }

    public function getRefundsAttribute()
    {
        $arr = [];
        foreach ($this->reintegros as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function getThirdsAttribute()
    {
        $arr = [];
        foreach ($this->terceros_premios as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function getFourthsAttribute()
    {
        $arr = [];
        foreach ($this->cuartos_premios as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function getFifthsAttribute()
    {
        $arr = [];
        foreach ($this->quintos_premios as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function get5FiguresAttribute()
    {
        $arr = [];
        foreach ($this->extracciones_cinco_cifras as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function get4FiguresAttribute()
    {
        $arr = [];
        foreach ($this->extracciones_cuatro_cifras as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function get3FiguresAttribute()
    {
        $arr = [];
        foreach ($this->extracciones_tres_cifras as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }

    public function get2FiguresAttribute()
    {
        $arr = [];
        foreach ($this->extracciones_dos_cifras as $key => $value) {
            $arr[] = $value['decimo'];
        }
        return $arr;
    }
}

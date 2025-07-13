<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = [
        'administration_id',
        'manager_id',
        'image',
        'name',
        'province',
        'city',
        'postal_code',
        'address',
        'nif_cif',
        'phone',
        'email',
        'comments'
    ];

    /**
     * Relación con Administration
     */
    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Relación con Manager
     */
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }
}

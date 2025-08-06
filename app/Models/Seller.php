<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entity_id'
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Entity
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Obtener el nombre completo del vendedor desde el usuario
     */
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->full_name : '';
    }

    /**
     * Obtener el estado como texto desde el usuario
     */
    public function getStatusTextAttribute()
    {
        return $this->user && $this->user->status ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtener el estado como clase CSS desde el usuario
     */
    public function getStatusClassAttribute()
    {
        return $this->user && $this->user->status ? 'success' : 'danger';
    }

    /**
     * Accesores para los datos del usuario
     */
    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : '';
    }

    public function getLastNameAttribute()
    {
        return $this->user ? $this->user->last_name : '';
    }

    public function getLastName2Attribute()
    {
        return $this->user ? $this->user->last_name2 : '';
    }

    public function getNifCifAttribute()
    {
        return $this->user ? $this->user->nif_cif : '';
    }

    public function getBirthdayAttribute()
    {
        return $this->user ? $this->user->birthday : '';
    }

    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : '';
    }

    public function getPhoneAttribute()
    {
        return $this->user ? $this->user->phone : '';
    }

    public function getCommentAttribute()
    {
        return $this->user ? $this->user->comment : '';
    }

    public function getImageAttribute()
    {
        return $this->user ? $this->user->image : '';
    }

    public function getStatusAttribute()
    {
        return $this->user ? $this->user->status : false;
    }
}

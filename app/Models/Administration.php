<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    use HasFactory;
    protected $fillable = [
        "web",
        "name",
        "receiving",
        "society",
        "nif_cif",
        "province",
        "city",
        "postal_code",
        "address",
        "email",
        "phone",
        "account",
        "manager_id",
        "status",
        "image"
    ];

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager');
    }
}

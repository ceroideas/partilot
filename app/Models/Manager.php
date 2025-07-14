<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "image",
        "name",
        "last_name",
        "last_name2",
        "nif_cif",
        "birthday",
        "email",
        "phone",
        "comment",
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

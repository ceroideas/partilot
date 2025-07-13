<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "name",
        "last_name",
        "last_name2",
        "nif_cif",
        "birthday",
        "email",
        "phone",
        "comment",
    ];
}

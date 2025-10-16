<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialWeb extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'title',
        'description',
        'banner_image',
        'small_image',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}

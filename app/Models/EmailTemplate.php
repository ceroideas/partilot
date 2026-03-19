<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'trigger_text',
        'condition_text',
        'subject_template',
        'body_template',
        'enabled_email',
        'enabled_notification',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'enabled_email' => 'boolean',
        'enabled_notification' => 'boolean',
    ];
}


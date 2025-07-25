<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'set_id',
        'format',
        'page',
        'rows',
        'cols',
        'orientation',
        'margin_up',
        'margin_right',
        'margin_left',
        'margin_top',
        'identation',
        'matrix_box',
        'page_rigth',
        'page_bottom',
        'guide_color',
        'guide_weight',
        'participation_number',
        'participation_from',
        'participation_to',
        'participation_page',
        'guides',
        'generate',
        'documents',
        'blocks',
        'participation_html',
        'vertical_space',
        'horizontal_space',
        'margin_custom',
        'cover_html',
        'back_html',
        'backgrounds',
        'margins',
        'output',
    ];

    protected $casts = [
        'blocks' => 'array',
        'guides' => 'boolean',
        'backgrounds' => 'array',
        'output' => 'array',
        'margins' => 'array',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}

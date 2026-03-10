<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignExternalInvitationFile extends Model
{
    protected $table = 'design_external_invitation_files';

    protected $fillable = [
        'design_external_invitation_id',
        'path',
        'original_name',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(DesignExternalInvitation::class, 'design_external_invitation_id');
    }
}

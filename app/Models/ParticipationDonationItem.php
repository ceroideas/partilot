<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipationDonationItem extends Model
{
    protected $fillable = [
        'donation_id',
        'participation_id',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(ParticipationDonation::class, 'donation_id');
    }

    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }
}

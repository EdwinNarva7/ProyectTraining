<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoverySession extends Model
{
    protected $fillable = [
        'recovery_request_id',
        'apprentice_id',
        'date',
        'scheduled_start_time',
        'scheduled_end_time',
        'start_time',
        'end_time',
        'duration_minutes',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'scheduled_start_time' => 'datetime',
        'scheduled_end_time' => 'datetime',
    ];

    public function recoveryRequest(): BelongsTo
    {
        return $this->belongsTo(RecoveryRequest::class);
    }

    public function apprentice(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apprentice_id');
    }
}

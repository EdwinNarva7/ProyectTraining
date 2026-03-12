<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penalty extends Model
{
    protected $fillable = [
        'apprentice_id',
        'date',
        'scheduled_hours',
        'attended_hours',
        'penalty_hours',
        'schedule_id',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'scheduled_hours' => 'decimal:2',
        'attended_hours' => 'decimal:2',
        'penalty_hours' => 'decimal:2',
    ];

    public function apprentice(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apprentice_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function recoveryRequests(): HasMany
    {
        return $this->hasMany(RecoveryRequest::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'apprentice_id',
        'start_at',
        'end_at',
        'duration_minutes'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime'
    ];

    public function apprentice(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apprentice_id');
    }

    public function isCompleted(): bool
    {
        return !is_null($this->end_at);
    }

    public function getDurationInHoursAttribute(): float
    {
        if (!$this->isCompleted()) {
            return 0;
        }

        return round($this->duration_minutes / 60, 2);
    }
}

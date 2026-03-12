<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'apprentice_id',
        'event_type',
        'occurred_at',
        'source',
        'note',
        'created_by'
    ];

    protected $casts = [
        'occurred_at' => 'datetime'
    ];

    public function apprentice(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apprentice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isEntry(): bool
    {
        return $this->event_type === 'entrada';
    }

    public function isExit(): bool
    {
        return $this->event_type === 'salida';
    }
}

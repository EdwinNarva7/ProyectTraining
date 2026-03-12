<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecoveryRequest extends Model
{
    protected $fillable = [
        'penalty_id',
        'apprentice_id',
        'requested_date',
        'hours_requested',
        'status',
        'admin_id',
        'admin_notes'
    ];

    protected $casts = [
        'requested_date' => 'date',
        'hours_requested' => 'decimal:2',
    ];

    public function penalty(): BelongsTo
    {
        return $this->belongsTo(Penalty::class);
    }

    public function apprentice(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apprentice_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function recoverySessions(): HasMany
    {
        return $this->hasMany(RecoverySession::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

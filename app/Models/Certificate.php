<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    protected $fillable = [
        'apprentice_id',
        'hours_completed',
        'issued_at',
        'status',
        'pdf_path',
        'email_sent_at',
        'email_to',
        'created_by'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'email_sent_at' => 'datetime'
    ];

    public function apprentice(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apprentice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(CertificateEvent::class);
    }

    public function isGenerated(): bool
    {
        return $this->status === 'generado';
    }

    public function isSent(): bool
    {
        return $this->status === 'enviado';
    }

    public function isDownloaded(): bool
    {
        return $this->status === 'descargado';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'anulado';
    }
}

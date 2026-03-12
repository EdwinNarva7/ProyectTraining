<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'certificate_id',
        'event',
        'detail'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }
}

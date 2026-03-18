<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprenticeProfile extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'phase_id',
        'document_number',
        'phone',
        'cohort',
        'start_date',
        'end_date',
        'fingerprint_template',
        'fingerprint_enrolled_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'fingerprint_enrolled_at' => 'datetime'
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function hasFingerprint(): bool
    {
        return !is_null($this->fingerprint_template);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

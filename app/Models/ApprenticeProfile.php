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
        'personal_email',
        'blood_type',
        'emergency_contact',
        'residence_address',
        'job_title',
        'technical_advisor',
        'cohort',
        'fiche_number',
        'start_date',
        'end_date',
        'fingerprint_template',
        'fingerprint_enrolled_at',
        'technologist_id'
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

    public function technologist(): BelongsTo
    {
        return $this->belongsTo(Technologist::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($profile) {
            // First check if a technologist is assigned
            if ($profile->technologist_id && ($profile->isDirty('technologist_id') || $profile->isDirty('phase_id'))) {
                $technologist = Technologist::with('phase')->find($profile->technologist_id);
                if ($technologist) {
                    $profile->phase_id = $technologist->phase_id;
                    $profile->start_date = $technologist->start_date ?? $technologist->phase?->start_date;
                    $profile->end_date = $technologist->end_date;
                }
            } 
            // Fallback to phase if no technologist but phase is dirty (legacy or specific use case)
            elseif ($profile->phase_id && $profile->isDirty('phase_id')) {
                $phase = Phase::find($profile->phase_id);
                if ($phase) {
                    $profile->start_date = $phase->start_date;
                    $profile->end_date = $phase->end_date;
                }
            }
        });
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

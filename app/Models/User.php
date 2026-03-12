<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'full_name',
        'name',
        'email',
        'password',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function apprenticeProfile(): HasOne
    {
        return $this->hasOne(ApprenticeProfile::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'apprentice_id');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'apprentice_id');
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'apprentice_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'apprentice_id');
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class, 'apprentice_id');
    }

    public function recoveryRequests(): HasMany
    {
        return $this->hasMany(RecoveryRequest::class, 'apprentice_id');
    }

    public function recoverySessions(): HasMany
    {
        return $this->hasMany(RecoverySession::class, 'apprentice_id');
    }

    public function isAdmin(): bool
    {
        return in_array($this->role?->name, ['Administrador', 'Admin']);
    }

    public function isApprentice(): bool
    {
        return $this->role?->name === 'Aprendiz';
    }

    /**
     * Accesor para mantener compatibilidad con el atributo 'name'
     */
    protected function name(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->full_name,
            set: fn($value) => ['full_name' => $value],
        );
    }
}

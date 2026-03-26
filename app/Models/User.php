<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Obtener el total de minutos laborados (Asistencia + Recuperación)
     */
    public function getTotalWorkedMinutes($phaseId = null)
    {
        $attendanceMinutes = $this->attendanceSessions()
            ->when($phaseId, function ($query) use ($phaseId) {
                return $query->whereHas('apprentice.apprenticeProfile', function ($p) use ($phaseId) {
                    $p->where('phase_id', $phaseId);
                });
            })
            ->sum('duration_minutes');

        $recoveryMinutes = $this->recoverySessions()
            ->where('status', 'completed')
            ->when($phaseId, function ($query) use ($phaseId) {
                return $query->whereHas('apprentice.apprenticeProfile', function ($p) use ($phaseId) {
                    $p->where('phase_id', $phaseId);
                });
            })
            ->sum('duration_minutes');

        return $attendanceMinutes + $recoveryMinutes;
    }

    /**
     * Obtener el total esperado de minutos laborados basado en el horario del tecnólogo
     * y las fechas de inicio/fin de la fase (o perfil).
     */
    public function getExpectedWorkedMinutes()
    {
        $profile = $this->apprenticeProfile;
        if (!$profile) return 0;
        
        $startDate = $profile->start_date ? \Carbon\Carbon::parse($profile->start_date) : null;
        $endDate = $profile->end_date ? \Carbon\Carbon::parse($profile->end_date) : null;

        if (!$startDate || !$endDate || $startDate->gt($endDate)) {
            return 0;
        }

        $schedules = collect();
        if ($profile->technologist_id) {
            $technologist = \App\Models\Technologist::with('schedules')->find($profile->technologist_id);
            if ($technologist) {
                $schedules = $technologist->schedules;
            }
        } else {
            // En caso de que se siga manejando un horario individual heredado en la misma tabla schedule
            $schedules = $this->schedules;
        }
        
        if ($schedules->isEmpty()) return 0;

        $weeklyMinutes = [];
        foreach ($schedules as $schedule) {
            if (!$schedule->start_time || !$schedule->end_time) continue;
            
            $start = \Carbon\Carbon::parse($schedule->start_time);
            $end = \Carbon\Carbon::parse($schedule->end_time);
            $duration = $start->diffInMinutes($end);
            
            if (!isset($weeklyMinutes[$schedule->weekday])) {
                $weeklyMinutes[$schedule->weekday] = 0;
            }
            $weeklyMinutes[$schedule->weekday] += $duration;
        }

        // Para saber qué día es hoy, si end_date es en el futuro, la expectativa total hasta final de la fase será esa
        // pero podemos calcular tanto "lo que debería llevar hasta hoy" vs "expectativa de la fase completa"
        // Según lo solicitado, quieren el "total que debe cumplir" en la fase.
        $totalMinutes = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $weekday = $currentDate->dayOfWeekIso; // 1 = Lunes, 7 = Domingo
            if (isset($weeklyMinutes[$weekday])) {
                $totalMinutes += $weeklyMinutes[$weekday];
            }
            $currentDate->addDay();
        }

        return $totalMinutes;
    }

    /**
     * Obtener minutos laborados hoy
     */
    public function getDailyWorkedMinutes()
    {
        $today = Carbon::today();

        $attendanceMinutes = $this->attendanceSessions()
            ->whereDate('start_at', $today)
            ->sum('duration_minutes');

        $recoveryMinutes = $this->recoverySessions()
            ->where('status', 'completed')
            ->whereDate('date', $today)
            ->sum('duration_minutes');

        return $attendanceMinutes + $recoveryMinutes;
    }

    /**
     * Obtener minutos laborados esta semana
     */
    public function getWeeklyWorkedMinutes()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $attendanceMinutes = $this->attendanceSessions()
            ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
            ->sum('duration_minutes');

        $recoveryMinutes = $this->recoverySessions()
            ->where('status', 'completed')
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum('duration_minutes');

        return $attendanceMinutes + $recoveryMinutes;
    }

    /**
     * Obtener minutos recuperados totales
     */
    public function getTotalRecoveredMinutes()
    {
        return $this->recoverySessions()
            ->where('status', 'completed')
            ->sum('duration_minutes');
    }

    /**
     * Convertir minutos a formato legible (H:i) o Horas decimales
     */
    public function formatMinutesToHours($minutes)
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours}h {$remainingMinutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } elseif ($remainingMinutes > 0) {
            return "{$remainingMinutes}m";
        } else {
            return "00:00";
        }
    }

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
        'status',
        'profile_photo_path'
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

    public function isGerente(): bool
    {
        return $this->role?->name === 'Gerente';
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

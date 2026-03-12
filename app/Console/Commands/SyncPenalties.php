<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\Penalty;
use Carbon\Carbon;

class SyncPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-penalties {--days=7 : Numero de días hacia atrás a revisar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza y genera penalizaciones por horas faltantes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $apprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->get();

        $this->info("Revisando penalizaciones para " . $apprentices->count() . " aprendices en los últimos $days días...");

        foreach ($apprentices as $apprentice) {
            $this->info("Procesando: {$apprentice->full_name}");
            for ($i = 0; $i <= $days; $i++) {
                $date = Carbon::today()->subDays($i);
                $this->processDate($apprentice, $date);
            }
        }

        $this->info("Sincronización completada.");
    }

    private function processDate($apprentice, $date)
    {
        $weekday = $date->dayOfWeek;
        // Carbon: 0=Domingo, 1=Lunes... 6=Sábado. Convertir a 1=Lunes ... 7=Domingo
        $weekday = $weekday === 0 ? 7 : $weekday;

        $schedule = Schedule::where('apprentice_id', $apprentice->id)
            ->where('weekday', $weekday)
            ->where('status', 'activo')
            ->first();

        if (!$schedule) {
            return;
        }

        // Calcular minutos programados
        $scheduledMinutes = Carbon::parse($schedule->start_time)->diffInMinutes(Carbon::parse($schedule->end_time));

        // Sumar todas las sesiones completadas ese día
        $attendedMinutes = AttendanceSession::where('apprentice_id', $apprentice->id)
            ->where('start_at', 'LIKE', $date->format('Y-m-d') . '%')
            ->whereNotNull('end_at')
            ->sum('duration_minutes');

        $pendingMinutes = max(0, $scheduledMinutes - $attendedMinutes);

        if ($pendingMinutes > 0) {
            // No generar penalización para el día de hoy si aún estamos en horario de trabajo
            if ($date->isToday()) {
                $scheduledEnd = Carbon::today()->setTimeFrom(Carbon::parse($schedule->end_time));
                if (Carbon::now()->lt($scheduledEnd)) {
                    // Si ya cerró una sesión hoy (aunque falten horas), se le penaliza inmediatamente
                    $hasClosedSessionToday = AttendanceSession::where('apprentice_id', $apprentice->id)
                        ->whereDate('start_at', $date)
                        ->whereNotNull('end_at')
                        ->exists();

                    if (!$hasClosedSessionToday) {
                        return; // Aún tiene tiempo de entrar
                    }
                }
            }

            $penalty = Penalty::where('apprentice_id', $apprentice->id)
                ->where('date', '=', $date->toDateString())
                ->first();

            if ($penalty) {
                if ($penalty->status === 'pending') {
                    $penalty->update([
                        'attended_hours' => $attendedMinutes / 60,
                        'penalty_hours' => $pendingMinutes / 60,
                        'scheduled_hours' => $scheduledMinutes / 60,
                    ]);
                }
            } else {
                Penalty::create([
                    'apprentice_id' => $apprentice->id,
                    'date' => $date,
                    'scheduled_hours' => $scheduledMinutes / 60,
                    'attended_hours' => $attendedMinutes / 60,
                    'penalty_hours' => $pendingMinutes / 60,
                    'schedule_id' => $schedule->id,
                    'status' => 'pending'
                ]);
                $this->line("   [+] Penalización creada: {$date->format('Y-m-d')} - {$pendingMinutes} min faltantes.");
            }
        }
    }
}

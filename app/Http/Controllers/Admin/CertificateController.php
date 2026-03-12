<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateSentMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['apprentice.apprenticeProfile', 'createdBy']);

        // Filtros
        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issued_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issued_at', '<=', $request->date_to);
        }

        $certificates = $query->orderBy('issued_at', 'desc')->paginate(15);

        // Estadísticas
        $totalCertificates = Certificate::count();
        $statusCounts = [
            'generado' => Certificate::where('status', 'generado')->count(),
            'enviado' => Certificate::where('status', 'enviado')->count(),
            'descargado' => Certificate::where('status', 'descargado')->count(),
        ];

        // Lista de aprendices para filtros
        $apprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->get();

        return view('admin.certificates.index', compact(
            'certificates',
            'apprentices',
            'totalCertificates',
            'statusCounts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener aprendices que han completado 80+ horas
        $eligibleApprentices = $this->getEligibleApprentices();

        return view('admin.certificates.create', compact('eligibleApprentices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'apprentice_id' => 'required|exists:users,id',
            'hours_completed' => 'required|numeric|min:80',
            'email_to' => 'nullable|email',
        ], [
            'apprentice_id.required' => 'Debe seleccionar un aprendiz.',
            'apprentice_id.exists' => 'El aprendiz seleccionado no existe.',
            'hours_completed.required' => 'Debe especificar las horas completadas.',
            'hours_completed.numeric' => 'Las horas deben ser un número válido.',
            'hours_completed.min' => 'El aprendiz debe haber completado al menos 80 horas.',
            'email_to.email' => 'El email debe tener un formato válido.',
        ]);

        // Verificar que el aprendiz tenga realmente las horas especificadas
        $actualHours = $this->getApprenticeHours($request->apprentice_id);

        if ($actualHours < $request->hours_completed) {
            return redirect()->back()
                ->withErrors(['hours_completed' => "El aprendiz solo ha completado {$actualHours} horas, no {$request->hours_completed} horas."])
                ->withInput();
        }

        // Verificar que no exista ya un certificado para este aprendiz
        $existingCertificate = Certificate::where('apprentice_id', $request->apprentice_id)
            ->where('status', '!=', 'anulado')
            ->first();

        if ($existingCertificate) {
            return redirect()->back()
                ->withErrors(['apprentice_id' => 'Este aprendiz ya tiene un certificado activo.'])
                ->withInput();
        }

        try {
            $certificate = Certificate::create([
                'apprentice_id' => $request->apprentice_id,
                'hours_completed' => $request->hours_completed,
                'issued_at' => now(),
                'status' => 'generado',
                'email_to' => $request->email_to,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.certificates.show', $certificate)
                ->with('success', 'Certificado creado exitosamente. Ahora puede generar el PDF.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el certificado: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['apprentice.apprenticeProfile', 'createdBy', 'events']);

        // Obtener estadísticas del aprendiz
        $apprenticeStats = $this->getApprenticeStats($certificate->apprentice_id);

        return view('admin.certificates.show', compact('certificate', 'apprenticeStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        $eligibleApprentices = $this->getEligibleApprentices();

        return view('admin.certificates.edit', compact('certificate', 'eligibleApprentices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $request->validate([
            'apprentice_id' => 'required|exists:users,id',
            'hours_completed' => 'required|numeric|min:80',
            'status' => 'required|in:generado,enviado,descargado,anulado',
            'email_to' => 'nullable|email',
        ]);

        // Verificar que el aprendiz tenga realmente las horas especificadas
        $actualHours = $this->getApprenticeHours($request->apprentice_id);

        if ($actualHours < $request->hours_completed) {
            return redirect()->back()
                ->withErrors(['hours_completed' => "El aprendiz solo ha completado {$actualHours} horas, no {$request->hours_completed} horas."])
                ->withInput();
        }

        // Verificar que no exista otro certificado para este aprendiz
        $existingCertificate = Certificate::where('apprentice_id', $request->apprentice_id)
            ->where('id', '!=', $certificate->id)
            ->where('status', '!=', 'anulado')
            ->first();

        if ($existingCertificate) {
            return redirect()->back()
                ->withErrors(['apprentice_id' => 'Este aprendiz ya tiene otro certificado activo.'])
                ->withInput();
        }

        try {
            $certificate->update([
                'apprentice_id' => $request->apprentice_id,
                'hours_completed' => $request->hours_completed,
                'status' => $request->status,
                'email_to' => $request->email_to,
            ]);

            return redirect()->route('admin.certificates.show', $certificate)
                ->with('success', 'Certificado actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el certificado: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        try {
            // Eliminar archivo PDF si existe
            if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
                Storage::delete($certificate->pdf_path);
            }

            $certificate->delete();

            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificado eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el certificado: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate PDF certificate.
     */
    public function generate(Certificate $certificate)
    {
        try {
            $pdfPath = 'certificates/' . $certificate->id . '_' . time() . '.pdf';

            // Generar PDF real usando la vista
            $pdf = Pdf::loadView('reports.certificate_pdf', compact('certificate'));
            $pdf->setPaper('letter', 'landscape');

            Storage::put($pdfPath, $pdf->output());

            $certificate->update([
                'pdf_path' => $pdfPath,
                'status' => 'generado'
            ]);

            $certificate->events()->create([
                'event' => 'generado',
                'detail' => 'Certificado PDF generado por ' . Auth::user()->full_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificado PDF generado exitosamente',
                'pdf_path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send certificate via email.
     */
    public function send(Certificate $certificate)
    {
        if (!$certificate->pdf_path || !Storage::exists($certificate->pdf_path)) {
            return response()->json([
                'success' => false,
                'message' => 'El certificado debe estar generado y el archivo debe existir antes de enviarlo.'
            ], 400);
        }

        $email = $certificate->email_to ?? $certificate->apprentice->email;

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un email válido para el envío.'
            ], 400);
        }

        try {
            // Envío real del email
            Mail::to($email)->send(new CertificateSentMail($certificate));

            $certificate->update([
                'status' => 'enviado',
                'email_sent_at' => now(),
                'email_to' => $email
            ]);

            // Registrar evento (Corregido campos)
            $certificate->events()->create([
                'event' => 'enviado',
                'detail' => 'Certificado enviado por email a ' . $email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificado enviado exitosamente a ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get eligible apprentices (80+ hours).
     */
    private function getEligibleApprentices()
    {
        return User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')
            ->with('apprenticeProfile')
            ->get()
            ->filter(function ($apprentice) {
                $hours = $this->getApprenticeHours($apprentice->id);
                return $hours >= 80;
            })
            ->map(function ($apprentice) {
                $apprentice->hours_completed = $this->getApprenticeHours($apprentice->id);
                return $apprentice;
            })
            ->sortByDesc('hours_completed');
    }

    /**
     * Get apprentice completed hours.
     */
    private function getApprenticeHours($apprenticeId)
    {
        return AttendanceSession::where('apprentice_id', $apprenticeId)
            ->whereNotNull('end_at')
            ->sum('duration_minutes') / 60;
    }

    /**
     * Get apprentice statistics.
     */
    private function getApprenticeStats($apprenticeId)
    {
        $totalSessions = AttendanceSession::where('apprentice_id', $apprenticeId)->count();
        $completedSessions = AttendanceSession::where('apprentice_id', $apprenticeId)
            ->whereNotNull('end_at')->count();
        $totalHours = $this->getApprenticeHours($apprenticeId);
        $averageSessionHours = $completedSessions > 0 ? $totalHours / $completedSessions : 0;

        return [
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'total_hours' => round($totalHours, 2),
            'average_session_hours' => round($averageSessionHours, 2)
        ];
    }

    /**
     * Download certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        if (!$certificate->pdf_path || !Storage::exists($certificate->pdf_path)) {
            return redirect()->back()
                ->withErrors(['error' => 'El archivo PDF no existe.']);
        }

        try {
            $certificate->update(['status' => 'descargado']);

            $certificate->events()->create([
                'event' => 'descargado',
                'detail' => 'Certificado descargado por ' . Auth::user()->full_name,
            ]);

            return Storage::download($certificate->pdf_path, 'certificado_' . $certificate->apprentice->full_name . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al descargar el certificado: ' . $e->getMessage()]);
        }
    }
}

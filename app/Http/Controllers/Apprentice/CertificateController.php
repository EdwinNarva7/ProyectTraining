<?php

namespace App\Http\Controllers\Apprentice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $certificates = Certificate::where('apprentice_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('aprendiz.certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        // Verificar que el certificado pertenece al usuario autenticado
        if ($certificate->apprentice_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este certificado.');
        }

        return view('aprendiz.certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        if ($certificate->apprentice_id !== Auth::id()) {
            abort(403, 'No tienes permiso para descargar este certificado.');
        }

        if (!$certificate->pdf_path || !Storage::exists($certificate->pdf_path)) {
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
                'detail' => 'Certificado PDF generado por aprendiz'
            ]);
        }

        $certificate->update(['status' => 'descargado']);
        $certificate->events()->create([
            'event' => 'descargado',
            'detail' => 'Certificado descargado por aprendiz'
        ]);

        $filename = 'certificado_' . preg_replace('/[^A-Za-z0-9_\- ]/', '', $certificate->apprentice->full_name) . '.pdf';
        return Storage::download($certificate->pdf_path, $filename);
    }
}

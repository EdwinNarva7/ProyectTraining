<x-mail::message>
    # ¡Felicidades, {{ $certificate->apprentice->name }}!

    Es un gusto para nosotros informarte que tu certificado de asistencia ha sido generado exitosamente en la plataforma
    **SIAP**.

    Este certificado acredita que has cumplido satisfactoriamente con un total de
    **{{ number_format($certificate->hours_completed, 1) }} horas** de formación.

    Adjunto a este correo encontrarás el documento oficial en formato PDF.

    <x-mail::button :url="config('app.url') . '/aprendiz/certificates'">
        Ver mis certificados
    </x-mail::button>

    Gracias por tu compromiso con tu formación profesional,<br>
    El equipo de {{ config('app.name') }}
</x-mail::message>
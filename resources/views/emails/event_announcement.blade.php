<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectText }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #4f46e5;">Anuncio del Evento: {{ $eventName }}</h2>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        
        <div style="white-space: pre-wrap;">
            {{ $messageContent }}
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 0.8rem; color: #666;">
            Este es un mensaje automático enviado desde la plataforma TeamSync.
        </p>
    </div>
</body>
</html>

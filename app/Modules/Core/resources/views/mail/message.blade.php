<!DOCTYPE html>
<html lang="es">
    <body style="margin:0;padding:0;background:#f1f4f8;font-family:Arial,Helvetica,sans-serif;color:#172338;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f4f8;padding:32px 12px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;">
                        <tr>
                            <td style="background:#101d32;padding:24px 32px;color:#ffffff;">
                                <div style="font-size:18px;font-weight:bold;">FUTNEXIS</div>
                                <div style="font-size:10px;letter-spacing:1.4px;color:#92a4bc;">SPORTS TECHNOLOGY</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px;">
                                <h1 style="margin:0 0 14px;font-size:22px;line-height:1.3;">{{ $heading }}</h1>
                                <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#526179;">{{ $body }}</p>
                                @isset($actionUrl)
                                    <a href="{{ $actionUrl }}" style="display:inline-block;padding:14px 24px;background:#2879f6;border-radius:8px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">{{ $actionText }}</a>
                                    <p style="margin:24px 0 0;font-size:12px;line-height:1.5;color:#8792a4;word-break:break-all;">Si el botón no funciona, copia este enlace en tu navegador:<br>{{ $actionUrl }}</p>
                                @endisset
                                <p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#526179;">{{ $note }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>

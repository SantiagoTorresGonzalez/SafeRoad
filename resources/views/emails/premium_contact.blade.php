<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeRoad SC — Módulo Premium</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:32px 16px;">
<tr><td align="center">

    {{-- ── Contenedor principal ── --}}
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.12);">

        {{-- ── Header con degradado ── --}}
        <tr>
            <td style="background:linear-gradient(135deg,#0d6e4f 0%,#1e3a5f 100%);padding:40px 40px 32px;text-align:center;">
                {{-- Icono shield --}}
                <div style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;border:2px solid rgba(255,255,255,0.3);">
                    <img src="https://img.icons8.com/ios-filled/50/ffffff/shield.png" width="32" height="32" alt="Shield" style="display:block;margin:16px auto 0;">
                </div>
                <h1 style="color:#ffffff;font-size:26px;font-weight:700;margin:0 0 6px;letter-spacing:-0.3px;">SafeRoad SC</h1>
                <p style="color:rgba(255,255,255,0.75);font-size:13px;margin:0;letter-spacing:1px;text-transform:uppercase;">Sabana Centro, Cundinamarca</p>
            </td>
        </tr>

        {{-- ── Cuerpo blanco ── --}}
        <tr>
            <td style="background:#ffffff;padding:40px 40px 32px;">

                {{-- Saludo --}}
                <p style="font-size:22px;font-weight:700;color:#1e293b;margin:0 0 8px;">
                    ¡Hola, {{ $user->name }}! 👋
                </p>
                <p style="font-size:15px;color:#64748b;margin:0 0 28px;line-height:1.6;">
                    Gracias por tu interés en el <strong style="color:#0d6e4f;">módulo de Predicción con IA</strong> de SafeRoad SC.
                    Aquí tienes toda la información que necesitas para dar el siguiente paso.
                </p>

                {{-- Divider --}}
                <div style="height:1px;background:linear-gradient(90deg,#0d6e4f,#1e3a5f);margin:0 0 28px;border-radius:2px;"></div>

                {{-- Título sección beneficios --}}
                <p style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#0d6e4f;margin:0 0 16px;">
                    ✦ Qué incluye el módulo premium
                </p>

                {{-- Beneficio 1 --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
                    <tr>
                        <td width="44" valign="top">
                            <div style="width:36px;height:36px;background:#dcfce7;border-radius:8px;text-align:center;line-height:36px;font-size:18px;">🗺️</div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 2px;">Predicción de zonas de riesgo con IA</p>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Algoritmos de machine learning que identifican sectores con alta probabilidad de incidentes viales.</p>
                        </td>
                    </tr>
                </table>

                {{-- Beneficio 2 --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
                    <tr>
                        <td width="44" valign="top">
                            <div style="width:36px;height:36px;background:#dbeafe;border-radius:8px;text-align:center;line-height:36px;font-size:18px;">🔥</div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 2px;">Mapas de calor predictivos por municipio</p>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Visualización geoespacial de los 11 municipios de Sabana Centro con niveles de riesgo proyectados.</p>
                        </td>
                    </tr>
                </table>

                {{-- Beneficio 3 --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
                    <tr>
                        <td width="44" valign="top">
                            <div style="width:36px;height:36px;background:#fef3c7;border-radius:8px;text-align:center;line-height:36px;font-size:18px;">🔔</div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 2px;">Alertas tempranas basadas en patrones históricos</p>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Notificaciones automáticas cuando un sector supera el umbral de riesgo según datos acumulados.</p>
                        </td>
                    </tr>
                </table>

                {{-- Beneficio 4 --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                    <tr>
                        <td width="44" valign="top">
                            <div style="width:36px;height:36px;background:#f3e8ff;border-radius:8px;text-align:center;line-height:36px;font-size:18px;">📊</div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 2px;">Reportes automáticos para autoridades</p>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Generación automática de informes ejecutivos con hallazgos y recomendaciones para planificadores territoriales.</p>
                        </td>
                    </tr>
                </table>

                {{-- Divider --}}
                <div style="height:1px;background:#e2e8f0;margin:0 0 28px;"></div>

                {{-- Precio --}}
                <div style="background:linear-gradient(135deg,#f0fdf4,#eff6ff);border:1px solid #bbf7d0;border-radius:12px;padding:24px;text-align:center;margin-bottom:28px;">
                    <p style="font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#0d6e4f;margin:0 0 8px;">Plan Premium</p>
                    <p style="font-size:42px;font-weight:800;color:#1e293b;margin:0;line-height:1;">
                        $XX
                        <span style="font-size:16px;font-weight:400;color:#64748b;">/mes</span>
                    </p>
                    <p style="font-size:13px;color:#64748b;margin:8px 0 0;">Acceso completo a todos los módulos predictivos</p>
                </div>

                {{-- CTA Button --}}
                <div style="text-align:center;margin-bottom:24px;">
                    <a href="#"
                       style="display:inline-block;background:linear-gradient(135deg,#0d6e4f,#1e3a5f);color:#ffffff;font-size:16px;font-weight:700;text-decoration:none;padding:16px 48px;border-radius:50px;letter-spacing:0.3px;box-shadow:0 4px 15px rgba(13,110,79,0.35);">
                        Contratar ahora →
                    </a>
                </div>

                <p style="font-size:12px;color:#94a3b8;text-align:center;margin:0;">
                    El botón de pago estará disponible próximamente. Te notificaremos cuando esté listo.
                </p>

            </td>
        </tr>

        {{-- ── Footer ── --}}
        <tr>
            <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 40px;text-align:center;">
                <p style="font-size:13px;font-weight:600;color:#1e3a5f;margin:0 0 4px;">SafeRoad SC</p>
                <p style="font-size:12px;color:#94a3b8;margin:0 0 12px;">Sabana Centro, Cundinamarca · Colombia</p>
                <p style="font-size:11px;color:#cbd5e1;margin:0;">
                    Recibiste este email porque solicitaste información sobre el plan premium desde el chatbot de SafeRoad SC.<br>
                    Si no fuiste tú, puedes ignorar este mensaje.
                </p>
            </td>
        </tr>

    </table>
    {{-- /Contenedor principal --}}

</td></tr>
</table>

</body>
</html>

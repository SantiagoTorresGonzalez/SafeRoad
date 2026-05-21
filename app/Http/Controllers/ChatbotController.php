<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChatbotController extends Controller
{
    /**
     * Captura de interés en el plan Premium desde el chatbot.
     * Registra el interés y envía notificación por correo (si está configurado).
     */
    public function premium(Request $request)
    {
        $request->validate([
            'canal'    => 'required|in:whatsapp,email',
            'contacto' => 'required|string|max:200',
        ]);

        $user    = auth()->user();
        $canal   = $request->canal;
        $contacto = $request->contacto;

        // Log de auditoría
        Log::info('SafeRoad Premium Interest', [
            'user_id'  => $user->id,
            'user_name'=> $user->name,
            'canal'    => $canal,
            'contacto' => $contacto,
            'ip'       => $request->ip(),
            'timestamp'=> now()->toDateTimeString(),
        ]);

        // Intentar enviar correo de confirmación (solo si MAIL está configurado)
        try {
            Mail::raw(
                "Hola {$user->name},\n\n" .
                "Hemos recibido tu interés en SafeRoad SC Premium con IA predictiva.\n" .
                "Te contactaremos pronto al {$canal}: {$contacto}.\n\n" .
                "¡Gracias por apoyar la seguridad vial en Sabana Centro!\n\n" .
                "— Equipo SafeRoad SC",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('¡Gracias por tu interés en SafeRoad SC Premium!');
                }
            );
        } catch (\Exception $e) {
            // Mail no configurado en entorno de desarrollo — solo loguear
            Log::warning('SafeRoad: no se pudo enviar correo premium: ' . $e->getMessage());
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Interés registrado correctamente.',
        ]);
    }
}

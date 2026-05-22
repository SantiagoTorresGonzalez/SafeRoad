<?php

namespace App\Http\Controllers;

use App\Mail\PremiumContactMail;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChatbotController extends Controller
{
    public function premium(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '⚠ Debes iniciar sesión para acceder a esta funcionalidad.',
            ], 401);
        }

        try {
            Mail::to($user->email)->send(new PremiumContactMail($user));

            AuditLog::registrar(
                accion:      'email_premium_enviado',
                entidad:     'User',
                entidadId:   $user->id,
                datos:       ['email' => $user->email, 'ip' => $request->ip()],
                descripcion: "Email premium enviado a {$user->email}",
            );

            Log::info('[CHATBOT] Email premium enviado', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => "✅ ¡Listo, {$user->name}! Te enviamos toda la información del plan premium a **{$user->email}**. Revisa tu bandeja de entrada.",
            ]);

        } catch (\Exception $e) {
            Log::error('[CHATBOT] Error enviando email premium', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '⚠ No pudimos enviar el email en este momento. Intenta de nuevo.',
            ], 500);
        }
    }
}
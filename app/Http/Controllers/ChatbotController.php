<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('MAILTRAP_API_TOKEN'),
                'Content-Type'  => 'application/json',
            ])->post('https://send.api.mailtrap.io/api/send', [
                'from' => [
                    'email' => 'hello@demomailtrap.co',
                    'name'  => 'SafeRoad SC',
                ],
                'to' => [
                    ['email' => $user->email, 'name' => $user->name],
                ],
                'subject'   => 'SafeRoad SC – Tu acceso al módulo de Predicción con IA',
                'html'      => view('emails.premium_contact', ['user' => $user])->render(),
            ]);

            if (!$response->successful()) {
                throw new \Exception('Mailtrap API error: ' . $response->body());
            }

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
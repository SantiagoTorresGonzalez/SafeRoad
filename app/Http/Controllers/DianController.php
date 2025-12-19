<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DianController extends Controller
{
    /**
     * Vista de envíos a la DIAN
     */
    public function envios(Request $request)
    {
        $query = CuentaCobro::query();

        // Filtros
        if ($request->filled('estado_dian')) {
            $query->where('estado_dian', $request->estado_dian);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        $cuentas = $query->orderBy('fecha_emision', 'desc')->paginate(20);

        // Stats
        $stats = [
            'aprobados' => CuentaCobro::where('estado_dian', 'aprobado')->count(),
            'pendientes' => CuentaCobro::where('estado_dian', 'sin_envio')->count(),
            'rechazados' => CuentaCobro::where('estado_dian', 'rechazado')->count(),
            'en_proceso' => CuentaCobro::where('estado_dian', 'enviado')->count(),
        ];

        return view('dian.envios', compact('cuentas', 'stats'));
    }

    /**
     * Vista de numeraciones DIAN
     */
    public function numeraciones()
    {
        $numeraciones = DB::table('dian_numerations')
            ->orderBy('active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener consecutivos para vincular
        $consecutivos = DB::table('consecutivos')
            ->select('consecutivos.*')
            ->leftJoin('dian_numerations', 'consecutivos.dian_numeration_id', '=', 'dian_numerations.id')
            ->get()
            ->map(function ($consecutivo) {
                return (object)[
                    'id' => $consecutivo->id,
                    'nombre' => $consecutivo->tipo_documento ?? 'Consecutivo #' . $consecutivo->id,
                    'prefijo' => $consecutivo->prefijo,
                    'dian_numeration_id' => $consecutivo->dian_numeration_id,
                ];
            });

        // Stats
        $stats = [
            'total' => $numeraciones->count(),
            'activas' => $numeraciones->where('active', true)->count(),
            'disponibles' => $numeraciones->sum(function ($n) {
                return $n->end_number - $n->current_number + 1;
            }),
            'consecutivos_vinculados' => $consecutivos->whereNotNull('dian_numeration_id')->count(),
        ];

        return view('dian.numeraciones', compact('numeraciones', 'consecutivos', 'stats'));
    }

    /**
     * Guardar nueva numeración
     */
    public function storeNumeracion(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'nullable|string|max:10',
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|gt:start_number',
            'resolution_number' => 'nullable|string|max:100',
            'authorized_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['current_number'] = $validated['start_number'];
        $validated['active'] = true;

        DB::table('dian_numerations')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('dian.numeraciones')
            ->with('success', 'Numeración creada exitosamente.');
    }

    /**
     * Vista de configuración DIAN
     */
    public function configuracion()
    {
        $config = DB::table('dian_configurations')->where('active', true)->first();

        return view('dian.configuracion', compact('config'));
    }

    /**
     * Actualizar configuración DIAN
     */
    public function updateConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|in:set,production',
            'api_url' => 'nullable|url|max:500',
            'token' => 'nullable|string|max:500',
            'email_contact' => 'nullable|email|max:255',
            'certificate_pass' => 'nullable|string|max:255',
        ]);

        // Handle certificate upload
        $certificatePath = null;
        if ($request->hasFile('certificate')) {
            $certificatePath = $request->file('certificate')->store('dian/certificates', 'local');
        }

        // Encrypt sensitive data
        if (!empty($validated['token'])) {
            $validated['token'] = encrypt($validated['token']);
        }
        if (!empty($validated['certificate_pass'])) {
            $validated['certificate_pass'] = encrypt($validated['certificate_pass']);
        }

        // Update or create config
        $existingConfig = DB::table('dian_configurations')->where('active', true)->first();

        $data = [
            'mode' => $validated['mode'],
            'api_url' => $validated['api_url'],
            'email_contact' => $validated['email_contact'],
            'active' => true,
            'updated_at' => now(),
        ];

        if (!empty($validated['token'])) {
            $data['token'] = $validated['token'];
        }
        if (!empty($validated['certificate_pass'])) {
            $data['certificate_pass'] = $validated['certificate_pass'];
        }
        if ($certificatePath) {
            $data['certificate_path'] = $certificatePath;
        }

        if ($existingConfig) {
            DB::table('dian_configurations')->where('id', $existingConfig->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('dian_configurations')->insert($data);
        }

        return redirect()->route('dian.configuracion')
            ->with('success', 'Configuración DIAN actualizada exitosamente.');
    }

    /**
     * Vincular numeración DIAN a un consecutivo
     */
    public function vincularConsecutivo(Request $request, $id)
    {
        $validated = $request->validate([
            'consecutivo_id' => 'required|exists:consecutivos,id',
        ]);

        $numeracion = DB::table('dian_numerations')->where('id', $id)->first();

        if (!$numeracion) {
            return redirect()->route('dian.numeraciones')
                ->with('error', 'Numeración no encontrada.');
        }

        // Actualizar el consecutivo con la numeración DIAN
        DB::table('consecutivos')
            ->where('id', $validated['consecutivo_id'])
            ->update([
                'dian_numeration_id' => $id,
                'updated_at' => now(),
            ]);

        return redirect()->route('dian.numeraciones')
            ->with('success', 'Numeración vinculada exitosamente al consecutivo.');
    }

    /**
     * Activar/desactivar numeración DIAN
     */
    public function toggleNumeracion($id)
    {
        $numeracion = DB::table('dian_numerations')->where('id', $id)->first();

        if (!$numeracion) {
            return redirect()->route('dian.numeraciones')
                ->with('error', 'Numeración no encontrada.');
        }

        // Si se activa, desactivar las demás
        if (!$numeracion->active) {
            DB::table('dian_numerations')->update(['active' => false]);
        }

        DB::table('dian_numerations')
            ->where('id', $id)
            ->update([
                'active' => !$numeracion->active,
                'updated_at' => now(),
            ]);

        $estado = !$numeracion->active ? 'activada' : 'desactivada';
        
        return redirect()->route('dian.numeraciones')
            ->with('success', "Numeración {$estado} exitosamente.");
    }

    /**
     * Eliminar numeración DIAN
     */
    public function destroyNumeracion($id)
    {
        $numeracion = DB::table('dian_numerations')->where('id', $id)->first();

        if (!$numeracion) {
            return redirect()->route('dian.numeraciones')
                ->with('error', 'Numeración no encontrada.');
        }

        // Verificar que no tenga consecutivos vinculados
        $consecutivosVinculados = DB::table('consecutivos')
            ->where('dian_numeration_id', $id)
            ->count();

        if ($consecutivosVinculados > 0) {
            return redirect()->route('dian.numeraciones')
                ->with('error', 'No se puede eliminar la numeración porque tiene consecutivos vinculados.');
        }

        DB::table('dian_numerations')->where('id', $id)->delete();

        return redirect()->route('dian.numeraciones')
            ->with('success', 'Numeración eliminada exitosamente.');
    }
}

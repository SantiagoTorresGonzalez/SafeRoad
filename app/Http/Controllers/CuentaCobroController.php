<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\ItemCuentaCobro;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Contrato;
use App\Models\Notificacion;
use App\Models\User;
use App\Models\Role;
use App\Models\Tercero;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Mail\CuentaCobroNotification;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuentaCobroController extends Controller
{
    /**
     * Mostrar todas las cuentas de cobro.
     */
    public function index()
    {
        $cuentas = CuentaCobro::with('items', 'contrato')
            ->whereNull('archived_at')
            ->latest()
            ->paginate(10);
        return view('cuentas_cobro.index', compact('cuentas'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Permitir crear a cualquier rol con permiso global o granular
        if (!($user->hasRole('super_admin')
            || $user->hasPermission('create_cuenta_cobro')
            || $user->puedeRealizarAccion('create_cuenta_cobro'))
        ) {
            return redirect()->route('cuentas_cobro.index')->with('error', 'No tienes permisos para crear cuentas de cobro.');
        }
        $contratos = Contrato::all();

        $departamentos = \App\Models\Departamento::with('municipios')->get();

        // Formatear datos para el formulario
        $departamentosFormateados = [];
        foreach ($departamentos as $dep) {
            $departamentosFormateados[$dep->nombre] = $dep->municipios->pluck('nombre')->toArray();
        }
        ksort($departamentosFormateados);
        
        // Obtener estado del consecutivo usando el nuevo método
        $estadoConsecutivo = \App\Models\Consecutivo::getEstadoConsecutivo('Cuenta de Cobro');
        
        $siguienteNumero = null;
        $consecutivoInfo = null;
        
        if ($estadoConsecutivo['valido']) {
            $consecutivo = $estadoConsecutivo['consecutivo'];
            $siguienteNumero = $estadoConsecutivo['siguiente_numero'];
            $consecutivoInfo = [
                'prefijo' => $consecutivo->prefijo,
                'disponibles' => $estadoConsecutivo['disponibles'],
                'porcentaje_uso' => $estadoConsecutivo['porcentaje_uso'],
                'dias_restantes' => $estadoConsecutivo['dias_restantes'],
                'vigencia_formato' => $estadoConsecutivo['vigencia_formato'] ?? '',
                'vigencia_fin' => $consecutivo->vigencia_fin->format('d/m/Y'),
                'resolucion' => $consecutivo->resolucion,
                'alertas' => $estadoConsecutivo['alertas'] ?? [],
            ];
        } else {
            // No hay consecutivo válido - redirigir con mensaje
            $mensaje = $estadoConsecutivo['mensaje'];
            
            // Si es admin, redirigir a crear consecutivo
            if ($user->hasRole('super_admin') || $user->hasRole('admin_programa')) {
                return redirect()->route('admin.consecutivos.create')
                    ->with('warning', $mensaje . ' Por favor configure un consecutivo antes de crear cuentas de cobro.');
            }
            
            // Si no es admin, mostrar error
            return redirect()->route('cuentas_cobro.index')
                ->with('error', $mensaje . ' Contacte al administrador para configurar un consecutivo.');
        }

        // Obtener terceros para búsqueda
        $terceros = Tercero::select('id', 'tipo_identificacion', 'identificacion', 'nombre_completo', 'razon_social', 'tipo_persona', 'telefono', 'email', 'direccion')
            ->orderBy('nombre_completo')
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'nombre' => $t->nombre,
                    'tipo_identificacion' => $t->tipo_identificacion,
                    'identificacion' => $t->identificacion,
                    'telefono' => $t->telefono,
                    'email' => $t->email,
                    'direccion' => $t->direccion,
                    'tipo' => $t->tipo_persona === 'juridica' ? 'Persona Jurídica' : 'Persona Natural'
                ];
            });

        // Cargar departamentos con municipios
        $departamentos = Departamento::with('municipios')->orderBy('nombre')->get();

        // Cargar catálogos para el formulario
        $paises = \Illuminate\Support\Facades\DB::table('paises')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $responsabilidadesFiscales = \Illuminate\Support\Facades\DB::table('responsabilidades_fiscales')->where('activo', true)->get();
        $pucCatalogo = \Illuminate\Support\Facades\DB::table('puc_catalogo')->where('activo', true)->orderBy('codigo')->get();
        $productosServicios = \Illuminate\Support\Facades\DB::table('productos_servicios')->where('activo', true)->orderBy('nombre')->get();
        $centrosCosto = \Illuminate\Support\Facades\DB::table('centros_costo')->where('activo', true)->orderBy('codigo')->get();

        return view('cuentas_cobro.create_v2', [
            'contratos' => $contratos,
            'departamentos' => $departamentos,
            'terceros' => $terceros,
            'siguienteNumero' => $siguienteNumero,
            'consecutivoInfo' => $consecutivoInfo,
            'paises' => $paises,
            'responsabilidadesFiscales' => $responsabilidadesFiscales,
            'pucCatalogo' => $pucCatalogo,
            'productos' => $productosServicios,
            'centrosCosto' => $centrosCosto,
        ]);
    }
    public function seguimiento($id)
    {
        $cuenta = CuentaCobro::with(['user.role', 'items'])->findOrFail($id);
        return view('cuentas_cobro.seguimiento', compact('cuenta'));
    }

    /**
     * Guardar una nueva cuenta de cobro.
     */
 public function store(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return redirect('/login');
    }

    if (!($user->hasRole('super_admin')
        || $user->hasPermission('create_cuenta_cobro')
        || $user->puedeRealizarAccion('create_cuenta_cobro'))
    ) {
        return redirect()->route('cuentas_cobro.index')->with('error', 'No tienes permisos para crear cuentas de cobro.');
    }

    $request->validate([
        // 'numero' => 'required|unique:cuentas_cobro', // Automático
        'fecha_emision' => 'required|date',
        'departamento' => 'required',
        'municipio' => 'required',
        'tipo_identificacion' => 'required',
        'tipo_cliente' => 'required',
        'nombre_beneficiario' => 'required',
        'plazo_pago' => 'nullable|integer|min:0|max:365',
        'items.*.item' => 'required|string',
        'items.*.cantidad' => 'required|integer|min:1',
        'items.*.precio_unitario' => 'required|numeric|min:0',

        // Nuevos campos legales
        'nombre_acreedor' => 'nullable|string|max:255',
        'tipo_documento_acreedor' => 'nullable|string|max:20',
        'numero_documento_acreedor' => 'nullable|string|max:50',
        'ciudad_expedicion_acreedor' => 'nullable|string|max:255',
        'direccion_acreedor' => 'nullable|string|max:255',
        'telefono_acreedor' => 'nullable|string|max:50',
        'email_acreedor' => 'nullable|email|max:255',

        'nombre_deudor' => 'nullable|string|max:255',
        'tipo_documento_deudor' => 'nullable|string|max:20',
        'numero_documento_deudor' => 'nullable|string|max:50',
        'ciudad_expedicion_deudor' => 'nullable|string|max:255',
        'direccion_deudor' => 'nullable|string|max:255',
        'telefono_deudor' => 'nullable|string|max:50',
        'email_deudor' => 'nullable|email|max:255',

        'concepto_cobro' => 'required|string|min:10',
        'descripcion_servicio' => 'nullable|string',
        'fecha_prestacion_servicio' => 'required|date',
        'fecha_inicio_servicio' => 'nullable|date',
        'fecha_fin_servicio' => 'nullable|date|after_or_equal:fecha_inicio_servicio',
        'lugar_prestacion_servicio' => 'nullable|string|max:255',

        'numero_contrato_referencia' => 'nullable|string|max:255',
        'fecha_contrato' => 'nullable|date',
        'tipo_contrato' => 'nullable|string|max:100',
        'objeto_contrato' => 'nullable|string|max:500',

        'numero_documento_soporte' => 'nullable|string|max:255',
        'fecha_documento_soporte' => 'nullable|date',
        'documento_soporte_url' => 'nullable|url',
        'requiere_validacion_previa' => 'nullable|boolean',

        'ciudad_expedicion_cuenta' => 'nullable|string|max:255',
        'prefijo_cuenta' => 'nullable|string|max:10',
        'serie_cuenta' => 'nullable|string|max:20',
        'consecutivo_cuenta' => 'nullable|integer|min:0',

        'condiciones_pago' => 'nullable|string',
        'forma_pago_acordada' => 'nullable|string|max:255',
        'penalidades_retraso' => 'nullable|string',
        'interes_mora_porcentaje' => 'nullable|numeric|min:0',
        'cobra_intereses_mora' => 'nullable|boolean',
        'dias_gracia' => 'nullable|integer|min:0|max:120',
        'fecha_vencimiento_real' => 'nullable|date',

        'observaciones_legales' => 'nullable|string',
        'notas_cobro' => 'nullable|string',
    ]);

    // Calcular subtotal de los ítems (servidor)
    $subtotal = collect($request->items)->sum(function($item) {
        return $item['cantidad'] * $item['precio_unitario'];
    });

    // Tomar impuestos/retenciones enviados (si existen)
    $iva = (float) $request->input('iva_valor', 0);
    $retFuente = (float) $request->input('retencion_fuente', 0);
    $retIca = (float) $request->input('retencion_ica', 0);
    $retIva = (float) $request->input('retencion_iva', 0);

    // Calcular total neto
    $valorTotal = round($subtotal + $iva - $retFuente - $retIca - $retIva, 2);

    // Obtener y consumir consecutivo usando el nuevo método
    $consecutivo = \App\Models\Consecutivo::getConsecutivoValido('Cuenta de Cobro');
    
    if (!$consecutivo) {
        // No hay consecutivo válido - esto no debería pasar si create() valida correctamente
        return redirect()->back()
            ->withInput()
            ->with('error', 'No hay consecutivo válido disponible. Por favor configure un consecutivo antes de crear cuentas de cobro.');
    }
    
    // Consumir el siguiente número del consecutivo
    $numeroConsumido = $consecutivo->consumirNumero();
    
    if (!$numeroConsumido) {
        // El consecutivo se agotó durante la transacción
        return redirect()->back()
            ->withInput()
            ->with('error', 'El consecutivo se ha agotado. Por favor configure un nuevo consecutivo.');
    }
    
    $numeroCuenta = $numeroConsumido['numero_formateado'];
    $nextConsecutive = $numeroConsumido['numero'];
    $prefijoConsecutivo = $numeroConsumido['prefijo'];

    // Preparar contrato_id: solo si es numérico y existe, sino null
    $contratoId = null;
    if ($request->filled('contrato_id') && is_numeric($request->contrato_id)) {
        $contratoId = (int) $request->contrato_id;
    }

    $legalData = $this->extractLegalPayload($request);
    $plazoDias = (int) $request->input('plazo_pago', 0);
    $legalData['dias_plazo_pago'] = $legalData['dias_plazo_pago'] ?? ($plazoDias ?: null);

    $fechaVencimientoCalculada = $legalData['fecha_vencimiento_real']
        ?? $this->calculateFechaVencimiento($request->fecha_emision, $plazoDias);
    $legalData['fecha_vencimiento_real'] = $fechaVencimientoCalculada;
    if (!empty($legalData['dias_gracia']) && $fechaVencimientoCalculada) {
        $legalData['fecha_vencimiento_con_gracia'] = Carbon::parse($fechaVencimientoCalculada)
            ->addDays((int) $legalData['dias_gracia'])
            ->format('Y-m-d');
    }

    $legalData['fecha_hora_emision'] = !empty($legalData['fecha_hora_emision'])
        ? Carbon::parse($legalData['fecha_hora_emision'])->toDateTimeString()
        : Carbon::parse($request->fecha_emision)
            ->setTimeFromTimeString(now()->format('H:i:s'))
            ->toDateTimeString();

    if (empty($legalData['ciudad_expedicion_cuenta'])) {
        $legalData['ciudad_expedicion_cuenta'] = $request->departamento;
    }

    if (empty($legalData['estado_cobro_judicial'])) {
        $legalData['estado_cobro_judicial'] = 'Sin proceso';
    }

    if (empty($legalData['nombre_deudor'])) {
        $legalData['nombre_deudor'] = $request->nombre_beneficiario;
    }
    if (empty($legalData['tipo_documento_deudor'])) {
        $legalData['tipo_documento_deudor'] = $request->tipo_identificacion;
    }
    if (empty($legalData['numero_documento_deudor'])) {
        $legalData['numero_documento_deudor'] = $request->identificacion;
    }

    if (empty($legalData['nombre_acreedor'])) {
        $legalData['nombre_acreedor'] = auth()->user()?->name ?? $request->nombre_beneficiario;
    }
    if (empty($legalData['tipo_documento_acreedor'])) {
        $legalData['tipo_documento_acreedor'] = $request->tipo_identificacion;
    }
    if (empty($legalData['numero_documento_acreedor'])) {
        $legalData['numero_documento_acreedor'] = $request->identificacion;
    }
    if (empty($legalData['email_acreedor'])) {
        $legalData['email_acreedor'] = auth()->user()?->email;
    }

    // El número ya fue obtenido del consecutivo arriba
    $numeroConsecutivo = $numeroCuenta;

    // Guardar información del consecutivo
    $legalData['prefijo_cuenta'] = $prefijoConsecutivo;
    $legalData['serie_cuenta'] = now()->format('Ym');
    $legalData['consecutivo_cuenta'] = $nextConsecutive;
    $legalData['subtotal'] = $subtotal;
    $legalData['iva_valor'] = $iva;
    $legalData['retencion_fuente_valor'] = $retFuente;
    $legalData['retencion_ica_valor'] = $retIca;
    $legalData['retencion_iva_valor'] = $retIva;
    $legalData['valor_pendiente_pago'] = $valorTotal;

    $baseData = [
        'numero' => $numeroConsecutivo,
        'fecha_emision' => $request->fecha_emision,
        'valor_total' => $valorTotal,
        'departamento' => $request->departamento,
        'municipio' => $request->municipio,
        'descripcion' => $request->descripcion,
        'tipo_identificacion' => $request->tipo_identificacion,
        'identificacion' => $request->identificacion,
        'tipo_cliente' => $request->tipo_cliente,
        'nombre_beneficiario' => $request->nombre_beneficiario,
        'plazo_pago' => $request->plazo_pago,
        'contrato_id' => $contratoId,
        'estado_aprobacion' => 'en_revision',
        // Flujo: Auxiliar -> Administrador -> Tesorería
        'etapa_aprobacion' => ($startStage = 'administrador'),
        'user_id' => Auth::id(),
        'estado_pago' => 'pending',
    ];

    // Crear la cuenta de cobro con datos legales extendidos
    $cuenta = CuentaCobro::create(array_merge($baseData, $legalData));

    // Guardar ítems
    foreach ($request->items as $item) {
        ItemCuentaCobro::create([
            'cuenta_cobro_id' => $cuenta->id,
            'item' => $item['item'],
            'detalle' => $item['detalle'] ?? null,
            'cantidad' => $item['cantidad'],
            'precio_unitario' => $item['precio_unitario'],
            'iva' => $item['iva'] ?? 0,
            'retefuente' => $item['retefuente'] ?? 0,
        ]);
    }

    // Generar PDF automáticamente al crear la cuenta de cobro
    try {
        $data = [
            'cuenta' => $cuenta->load('items', 'contrato'),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'retFuente' => $retFuente,
            'retIca' => $retIca,
            'retIva' => $retIva,
            'total' => $valorTotal,
            'appName' => config('app.name'),
        ];

        $pdf = Pdf::loadView('cuentas_cobro.pdf', $data)->setPaper('letter');
        $fileName = 'CuentaCobro_' . ($cuenta->numero ?? $cuenta->id) . '_' . \Carbon\Carbon::now()->format('Y-m-d') . '.pdf';
        // Asegurar carpeta
        Storage::disk('public')->makeDirectory('cuentas_cobro');
        Storage::disk('public')->put('cuentas_cobro/' . $fileName, $pdf->output());

        $pdfUrl = Storage::disk('public')->url('cuentas_cobro/' . $fileName);
    } catch (\Exception $e) {
        // Registrar error y continuar; no bloquear la creación por fallos en PDF
        \Log::error('Error generando PDF de cuenta de cobro: ' . $e->getMessage());
        $pdfUrl = null;
    }

    // Registrar historial: creado y enviado a revisión (etapa inicial)
    try {
        $cuenta->registrarHistorial(Auth::id(), 'creado', null, 'en_revision', 'Cuenta de cobro creada');
        $etiquetaInicial = ucfirst($startStage);
        $cuenta->registrarHistorial(Auth::id(), 'revisado', 'pendiente', 'en_revision', 'Enviada a revisión (' . $etiquetaInicial . ')');
    } catch (\Exception $e) {
        \Log::warning('No se pudo registrar historial al crear la cuenta: '.$e->getMessage());
    }

    $msg = 'Cuenta de cobro creada y enviada a revisión.';
    if ($pdfUrl) {
        $msg .= ' <span style="display:inline-flex;align-items:center;gap:6px;margin-left:8px;"><a href="' . $pdfUrl . '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;background:#007AFF;color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-weight:600;transition:all 0.2s;box-shadow:0 2px 8px rgba(0,122,255,0.3);" onmouseover="this.style.background=\'#0051D5\';this.style.transform=\'translateY(-2px)\';this.style.boxShadow=\'0 4px 12px rgba(0,122,255,0.4)\';" onmouseout="this.style.background=\'#007AFF\';this.style.transform=\'translateY(0)\';this.style.boxShadow=\'0 2px 8px rgba(0,122,255,0.3)\';"><span class="material-symbols-rounded" style="font-size:18px;">picture_as_pdf</span>Ver PDF</a></span>';
    }

    // Notificar SOLO al rol responsable de la etapa actual
    try {
        $rolesNotificar = [$startStage];
        $usuariosNotificar = User::whereHas('role', function($q) use ($rolesNotificar) {
            $q->whereIn('name', $rolesNotificar);
        })->get();

        foreach ($usuariosNotificar as $usuario) {
            Notificacion::create([
                'user_id' => $usuario->id,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Nueva cuenta para revisión (' . ucfirst($startStage) . ')',
                'mensaje' => 'Cuenta #' . $cuenta->numero . ' por $' . number_format($cuenta->valor_total, 2, ',', '.') . ' - Beneficiario: ' . $cuenta->nombre_beneficiario,
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }
    } catch (\Exception $e) {
        \Log::error('Error creando notificaciones de cuenta de cobro: ' . $e->getMessage());
    }

    return redirect()->route('cuentas_cobro.index')->with('success', $msg);
    }


    /**
     * Mostrar una cuenta de cobro específica.
     */
    public function show($id)
    {
        $cuenta = CuentaCobro::with(['items', 'contrato', 'historial.user', 'aprobadoPor'])->findOrFail($id);
        return view('cuentas_cobro.show', compact('cuenta'));
    }

    /**
     * Listado de aprobaciones asignadas a mi rol y pendientes.
     */
    public function misAprobaciones()
    {
        $role = Auth::user()?->role?->name;
        $roleToEtapa = [
            'administrador' => 'administrador',
            'tesoreria' => 'tesoreria',
        ];

        $etapa = $roleToEtapa[$role] ?? null;
        
        // Admin del programa ve todas las cuentas en revisión
        if ($role === 'admin_programa') {
            $cuentas = CuentaCobro::where('estado_aprobacion', 'en_revision')
                ->whereNull('archived_at')
                ->orderByDesc('created_at')
                ->paginate(15);
        } else {
            // Otros roles solo ven cuentas en su etapa
            $cuentas = CuentaCobro::when($etapa, function ($q) use ($etapa) {
                    $q->where('estado_aprobacion', 'en_revision')
                      ->where('etapa_aprobacion', $etapa);
                })
                ->whereNull('archived_at')
                ->orderByDesc('created_at')
                ->paginate(15);
        }

        return view('cuentas_cobro.aprobaciones', compact('cuentas', 'etapa', 'role'));
    }

    /**
     * Aprobar una cuenta en la etapa actual o avanzar etapa.
     */
    public function aprobar(Request $request, $id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if ($cuenta->estado_aprobacion !== 'en_revision') {
            return back()->with('error', 'La cuenta no está en revisión.');
        }

        if (!$user->puedeRealizarAccion('aprobar', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion)) {
            return back()->with('error', 'No tienes permisos para aprobar esta etapa.');
        }

        $comentario = $request->input('comentario');
        $estadoAnterior = $cuenta->estado_aprobacion;

        // 1) Administrador -> Tesorería
        if ($cuenta->etapa_aprobacion === 'administrador') {
            $cuenta->etapa_aprobacion = 'tesoreria';
            $cuenta->save();
            $cuenta->registrarHistorial(Auth::id(), 'revisado', $estadoAnterior, 'en_revision', $comentario ?: 'Administrador aprobó y envió a Tesorería.');
            $this->notificarRoles(['tesoreria'], 'Cuenta para revisión (Tesorería)', $cuenta);
            return back()->with('success', 'Cuenta enviada a Tesorería.');
        }

        // 2) Tesorería -> Aprobado (Listo para pago)
        if ($cuenta->etapa_aprobacion === 'tesoreria') {
            $cuenta->estado_aprobacion = 'aprobado';
            // Se mantiene en etapa tesoreria para que puedan registrar el pago
            $cuenta->aprobado_por_id = Auth::id();
            $cuenta->fecha_aprobacion = now();
            $cuenta->save();
            $cuenta->registrarHistorial(Auth::id(), 'aprobado', $estadoAnterior, 'aprobado', $comentario ?: 'Tesorería aprobó la cuenta.');
            
            if ($cuenta->user_id) {
                Notificacion::create([
                    'user_id' => $cuenta->user_id,
                    'tipo' => 'cuenta_cobro',
                    'titulo' => 'Tu cuenta fue aprobada',
                    'mensaje' => 'La cuenta #' . $cuenta->numero . ' fue aprobada por Tesorería.',
                    'cuenta_cobro_id' => $cuenta->id,
                ]);
            }
            return back()->with('success', 'Cuenta aprobada. Lista para registrar pago.');
        }

        return back()->with('error', 'No se pudo determinar la siguiente etapa.');
    }

    /**
     * Rechazar la cuenta en cualquier etapa.
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate(['motivo_rechazo' => 'required|string|min:5']);
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!in_array($cuenta->estado_aprobacion, ['en_revision'])) {
            return back()->with('error', 'La cuenta no está en estado válido para rechazo.');
        }

        if (!$user->puedeRealizarAccion('rechazar', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion)) {
            return back()->with('error', 'No tienes permisos para rechazar esta cuenta.');
        }

        $estadoAnterior = $cuenta->estado_aprobacion;
        $cuenta->update([
            'estado_aprobacion' => 'rechazado',
            'motivo_rechazo' => $request->motivo_rechazo,
            'fecha_rechazo' => now(),
            'etapa_aprobacion' => null,
        ]);

        $cuenta->registrarHistorial(Auth::id(), 'rechazado', $estadoAnterior, 'rechazado', $request->motivo_rechazo);

        // Notificar al creador
        if ($cuenta->user_id) {
            Notificacion::create([
                'user_id' => $cuenta->user_id,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Tu cuenta fue rechazada',
                'mensaje' => 'Cuenta #'.$cuenta->numero.' fue rechazada. Motivo: '.$request->motivo_rechazo,
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }
        return back()->with('success', 'Cuenta rechazada.');
    }

    /** Enviar al cliente (tras aprobado) */
    public function enviarCliente($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }
        if ($cuenta->estado_aprobacion !== 'aprobado') {
            return back()->with('error', 'La cuenta debe estar aprobada para enviarse al cliente.');
        }
        // Permitir a cualquier rol con permiso granular/global (o super_admin)
        if (!($user->hasRole('super_admin')
            || $user->puedeRealizarAccion('enviar_cliente', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion)
            || $user->hasPermission('enviar_cliente'))
        ) {
            return back()->with('error', 'No tienes permisos para enviar al cliente.');
        }
        $cuenta->update([
            'estado_aprobacion' => 'enviado_cliente',
            'fecha_envio_cliente' => now(),
        ]);
        $cuenta->registrarHistorial(Auth::id(), 'enviado_cliente', 'aprobado', 'enviado_cliente', 'Enviado al cliente');
        return back()->with('success', 'Cuenta enviada al cliente.');
    }

    /**
     * Registrar pago (Tesorería)
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'valor_pagado' => 'required|numeric|min:0',
            'medio_pago' => 'required|string',
            'referencia_pago' => 'nullable|string|max:255',
            'observacion_pago' => 'nullable|string',
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if (!($user->hasRole('super_admin')
            || $user->hasPermission('process_payment')
            || $user->puedeRealizarAccion('process_payment', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion))
        ) {
            return back()->with('error', 'No tienes permisos para registrar pagos.');
        }
        if ($cuenta->etapa_aprobacion !== 'tesoreria' || $cuenta->estado_aprobacion !== 'aprobado') {
            return back()->with('error', 'La cuenta no está lista para pago.');
        }

        // Actualizar campos de pago (usar columnas existentes)
        $cuenta->estado_pago = 'approved';
        $cuenta->fecha_pago = now();
        $cuenta->pagado_por = Auth::id();
        // Registrar detalle en observaciones
        $detallePago = "Pago: $" . number_format((float)$request->input('valor_pagado'), 2, ',', '.') .
            " | Medio: " . $request->input('medio_pago') .
            ( $request->filled('referencia_pago') ? (" | Ref: " . $request->input('referencia_pago')) : '' ) .
            ( $request->filled('observacion_pago') ? (" | Obs: " . $request->input('observacion_pago')) : '' );
        $cuenta->observaciones = trim(($cuenta->observaciones ? ($cuenta->observaciones . "
") : '') . $detallePago);
        // Al notificar pago, devolver la cuenta al auxiliar (para su control/seguimiento)
        $cuenta->etapa_aprobacion = 'auxiliar';
        $cuenta->save();

        // Historial y notificación
        $cuenta->registrarHistorial(Auth::id(), 'pagado', 'aprobado', 'pagado', 'Pago registrado por Tesorería.');
        if ($cuenta->user_id) {
            Notificacion::create([
                'user_id' => $cuenta->user_id,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Pago realizado',
                'mensaje' => 'Tu cuenta #' . $cuenta->numero . ' fue pagada.',
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }

        return back()->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Rechazar pago (Tesorería)
     */
    public function rechazarPago(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|min:5',
        ]);
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if (!($user->hasRole('super_admin')
            || $user->hasPermission('process_payment')
            || $user->puedeRealizarAccion('process_payment', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion))
        ) {
            return back()->with('error', 'No tienes permisos para rechazar pagos.');
        }
        if ($cuenta->etapa_aprobacion !== 'tesoreria' || $cuenta->estado_aprobacion !== 'aprobado') {
            return back()->with('error', 'La cuenta no está lista para pago.');
        }

    $cuenta->estado_pago = 'rejected';
    $cuenta->observaciones = trim(($cuenta->observaciones ? ($cuenta->observaciones . "
") : '') . 'Pago rechazado: ' . $request->input('motivo'));
    $cuenta->save();

        $cuenta->registrarHistorial(Auth::id(), 'pago_rechazado', 'aprobado', 'aprobado', 'Pago rechazado por Tesorería: '.$request->input('motivo'));
        if ($cuenta->user_id) {
            Notificacion::create([
                'user_id' => $cuenta->user_id,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Pago rechazado',
                'mensaje' => 'El pago de tu cuenta #'.$cuenta->numero.' fue rechazado: '.$request->input('motivo'),
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }

        return back()->with('success', 'Pago marcado como rechazado.');
    }

    private function notificarRoles(array $roles, string $titulo, CuentaCobro $cuenta): void
    {
        $usuarios = User::whereHas('role', fn($q) => $q->whereIn('name', $roles))->get();
        foreach ($usuarios as $usuario) {
            Notificacion::create([
                'user_id' => $usuario->id,
                'tipo' => 'cuenta_cobro',
                'titulo' => $titulo,
                'mensaje' => 'Cuenta #'.$cuenta->numero.' por $'.number_format($cuenta->valor_total, 2, ',', '.').' para revisión.',
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }
    }

    /**
     * Mostrar formulario de edición.
     */
   public function edit($id)
{
    $cuenta = CuentaCobro::with('items')->findOrFail($id);
    $contratos = Contrato::all();

    $departamentos = \App\Models\Departamento::with('municipios')->get();
    $departamentosFormateados = [];
    foreach ($departamentos as $dep) {
        $departamentosFormateados[$dep->nombre] = $dep->municipios->pluck('nombre')->toArray();
    }

    $user = Auth::user();
    if (!$user) {
        return redirect('/login');
    }

    // Restricciones de edición
    $readonly = false;

    $isOwnerInCorrection = ($cuenta->user_id === $user->id && $cuenta->estado_aprobacion === 'en_correccion');
    $hasEditPermission = ($user->hasPermission('edit_own_cuenta_cobro')
        || $user->puedeRealizarAccion('edit_own_cuenta_cobro', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion)
        || $user->puedeRealizarAccion('editar', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion));

    // Tesorería o roles con permiso de pago: solo lectura
    if ($user->hasPermission('process_payment') || $user->puedeRealizarAccion('process_payment', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion)) {
        $readonly = true;
    }

    if (!$isOwnerInCorrection && !$hasEditPermission && !$readonly) {
        return redirect()->route('cuentas_cobro.show', $cuenta->id)->with('error', 'No tienes permisos para editar esta cuenta.');
    }

    // Obtener terceros para búsqueda
    $terceros = Tercero::select('id', 'tipo_identificacion', 'identificacion', 'nombre_completo', 'razon_social', 'tipo_persona', 'telefono', 'email', 'direccion')
        ->orderBy('nombre_completo')
        ->get();

    // Cargar catálogos para el formulario
    $paises = \Illuminate\Support\Facades\DB::table('paises')->where('activo', true)->orderBy('nombre')->get();
    $responsabilidadesFiscales = \Illuminate\Support\Facades\DB::table('responsabilidades_fiscales')->where('activo', true)->get();
    $pucCatalogo = \Illuminate\Support\Facades\DB::table('puc_catalogo')->where('activo', true)->orderBy('codigo')->get();
    $productosServicios = \Illuminate\Support\Facades\DB::table('productos_servicios')->where('activo', true)->orderBy('nombre')->get();
    $centrosCosto = \Illuminate\Support\Facades\DB::table('centros_costo')->where('activo', true)->orderBy('codigo')->get();

    return view('cuentas_cobro.edit', [
        'cuenta' => $cuenta,
        'contratos' => $contratos,
        'departamentos' => $departamentosFormateados,
        'terceros' => $terceros,
        'readonly' => $readonly,
        'paises' => $paises,
        'responsabilidadesFiscales' => $responsabilidadesFiscales,
        'pucCatalogo' => $pucCatalogo,
        'productos' => $productosServicios,
        'centrosCosto' => $centrosCosto,
    ]);
}


    /**
     * Actualizar una cuenta de cobro existente.
     */
 public function update(Request $request, $id)
{
    $cuenta = CuentaCobro::findOrFail($id);
    $user = Auth::user();

    // Check permissions: Owner in correction OR User with 'editar' permission
    $isOwnerInCorrection = ($cuenta->user_id === $user->id && $cuenta->estado_aprobacion === 'en_correccion');
    $hasEditPermission = $user->puedeRealizarAccion('editar', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion);

    if (!$isOwnerInCorrection && !$hasEditPermission) {
        return redirect()->route('cuentas_cobro.show', $cuenta->id)->with('error', 'No tienes permisos para editar esta cuenta.');
    }

    $request->validate([
        'fecha_emision' => 'required|date',
        'departamento' => 'required',
        'municipio' => 'required',
        'tipo_identificacion' => 'required',
        'tipo_cliente' => 'required',
        'nombre_beneficiario' => 'required',
        'plazo_pago' => 'nullable|integer|min:0|max:365',
        'items.*.item' => 'required|string',
        'items.*.cantidad' => 'required|integer|min:1',
        'items.*.precio_unitario' => 'required|numeric|min:0',

        'nombre_acreedor' => 'nullable|string|max:255',
        'tipo_documento_acreedor' => 'nullable|string|max:20',
        'numero_documento_acreedor' => 'nullable|string|max:50',
        'ciudad_expedicion_acreedor' => 'nullable|string|max:255',
        'direccion_acreedor' => 'nullable|string|max:255',
        'telefono_acreedor' => 'nullable|string|max:50',
        'email_acreedor' => 'nullable|email|max:255',

        'nombre_deudor' => 'nullable|string|max:255',
        'tipo_documento_deudor' => 'nullable|string|max:20',
        'numero_documento_deudor' => 'nullable|string|max:50',
        'ciudad_expedicion_deudor' => 'nullable|string|max:255',
        'direccion_deudor' => 'nullable|string|max:255',
        'telefono_deudor' => 'nullable|string|max:50',
        'email_deudor' => 'nullable|email|max:255',

        'concepto_cobro' => 'required|string|min:10',
        'descripcion_servicio' => 'nullable|string',
        'fecha_prestacion_servicio' => 'required|date',
        'fecha_inicio_servicio' => 'nullable|date',
        'fecha_fin_servicio' => 'nullable|date|after_or_equal:fecha_inicio_servicio',
        'lugar_prestacion_servicio' => 'nullable|string|max:255',

        'numero_contrato_referencia' => 'nullable|string|max:255',
        'fecha_contrato' => 'nullable|date',
        'tipo_contrato' => 'nullable|string|max:100',
        'objeto_contrato' => 'nullable|string|max:500',

        'numero_documento_soporte' => 'nullable|string|max:255',
        'fecha_documento_soporte' => 'nullable|date',
        'documento_soporte_url' => 'nullable|url',
        'requiere_validacion_previa' => 'nullable|boolean',

        'ciudad_expedicion_cuenta' => 'nullable|string|max:255',
        'prefijo_cuenta' => 'nullable|string|max:10',
        'serie_cuenta' => 'nullable|string|max:20',
        'consecutivo_cuenta' => 'nullable|integer|min:0',

        'condiciones_pago' => 'nullable|string',
        'forma_pago_acordada' => 'nullable|string|max:255',
        'penalidades_retraso' => 'nullable|string',
        'interes_mora_porcentaje' => 'nullable|numeric|min:0',
        'cobra_intereses_mora' => 'nullable|boolean',
        'dias_gracia' => 'nullable|integer|min:0|max:120',
        'fecha_vencimiento_real' => 'nullable|date',

        'observaciones_legales' => 'nullable|string',
        'notas_cobro' => 'nullable|string',
    ]);

    $subtotal = collect($request->items)->sum(function($item) {
        return $item['cantidad'] * $item['precio_unitario'];
    });
    $iva = (float) $request->input('iva_valor', 0);
    $retFuente = (float) $request->input('retencion_fuente', 0);
    $retIca = (float) $request->input('retencion_ica', 0);
    $retIva = (float) $request->input('retencion_iva', 0);
    $valorTotal = round($subtotal + $iva - $retFuente - $retIca - $retIva, 2);

    $legalData = $this->extractLegalPayload($request);
    $plazoDias = (int) $request->input('plazo_pago', 0);
    $legalData['dias_plazo_pago'] = $legalData['dias_plazo_pago'] ?? ($plazoDias ?: null);

    $fechaVencimientoCalculada = $legalData['fecha_vencimiento_real']
        ?? $this->calculateFechaVencimiento($request->fecha_emision, $plazoDias);
    $legalData['fecha_vencimiento_real'] = $fechaVencimientoCalculada;
    if (!empty($legalData['dias_gracia']) && $fechaVencimientoCalculada) {
        $legalData['fecha_vencimiento_con_gracia'] = Carbon::parse($fechaVencimientoCalculada)
            ->addDays((int) $legalData['dias_gracia'])
            ->format('Y-m-d');
    }
    
    $legalData['subtotal'] = $subtotal;
    $legalData['iva_valor'] = $iva;
    $legalData['retencion_fuente_valor'] = $retFuente;
    $legalData['retencion_ica_valor'] = $retIca;
    $legalData['retencion_iva_valor'] = $retIva;
    $legalData['valor_pendiente_pago'] = $valorTotal;

    $cuenta->update(array_merge([
        'fecha_emision' => $request->fecha_emision,
        'valor_total' => $valorTotal,
        'departamento' => $request->departamento,
        'municipio' => $request->municipio,
        'descripcion' => $request->descripcion,
        'tipo_identificacion' => $request->tipo_identificacion,
        'identificacion' => $request->identificacion,
        'tipo_cliente' => $request->tipo_cliente,
        'nombre_beneficiario' => $request->nombre_beneficiario,
        'plazo_pago' => $request->plazo_pago,
        'contrato_id' => $request->contrato_id ?? null,
    ], $legalData));

    // Eliminar ítems anteriores y guardar los nuevos
    $cuenta->items()->delete();
    foreach ($request->items as $item) {
        ItemCuentaCobro::create([
            'cuenta_cobro_id' => $cuenta->id,
            'item' => $item['item'],
            'detalle' => $item['detalle'] ?? null,
            'cantidad' => $item['cantidad'],
            'precio_unitario' => $item['precio_unitario'],
            'iva' => $item['iva'] ?? 0,
            'retefuente' => $item['retefuente'] ?? 0,
        ]);
    }

    // Regenerar PDF (si aplica) después de la actualización
    try {
        $data = [
            'cuenta' => $cuenta->load('items', 'contrato'),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'retFuente' => $retFuente,
            'retIca' => $retIca,
            'retIva' => $retIva,
            'total' => $valorTotal,
            'appName' => config('app.name'),
        ];

        $pdf = Pdf::loadView('cuentas_cobro.pdf', $data)->setPaper('letter');
        $fileName = 'CuentaCobro_' . ($cuenta->numero ?? $cuenta->id) . '_' . \Carbon\Carbon::now()->format('Y-m-d') . '.pdf';
        Storage::disk('public')->makeDirectory('cuentas_cobro');
        Storage::disk('public')->put('cuentas_cobro/' . $fileName, $pdf->output());
    } catch (\Exception $e) {
        \Log::error('Error regenerando PDF de cuenta de cobro: ' . $e->getMessage());
    }

    return redirect()->route('cuentas_cobro.index')->with('success', 'Cuenta de cobro actualizada correctamente.');
}

    /**
     * Devolver para corrección (Administrador)
     */
    public function devolver(Request $request, $id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        $request->validate(['motivo' => 'required|string|min:5']);
        if ($cuenta->estado_aprobacion !== 'en_revision' || $cuenta->etapa_aprobacion !== 'administrador') {
            return back()->with('error', 'La cuenta no está en etapa de Administrador.');
        }

        if (!$user) {
            return redirect('/login');
        }

        if (!($user->hasRole('super_admin')
            || $user->hasPermission('request_corrections')
            || $user->puedeRealizarAccion('request_corrections', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion))
        ) {
            return back()->with('error', 'No tienes permisos para devolver esta cuenta.');
        }
        $estadoAnterior = $cuenta->estado_aprobacion;
        $cuenta->estado_aprobacion = 'en_correccion';
        $cuenta->etapa_aprobacion = 'auxiliar';
        // Guardar motivo de devolución específico
        $cuenta->motivo_devolucion = $request->input('motivo');
        $cuenta->save();
        $cuenta->registrarHistorial(Auth::id(), 'devuelto', $estadoAnterior, 'en_correccion', 'Administrador devolvió: '.$request->input('motivo'));
        // Notificar al creador
        if ($cuenta->user_id) {
            Notificacion::create([
                'user_id' => $cuenta->user_id,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Cuenta devuelta para corrección',
                'mensaje' => 'La cuenta #'.$cuenta->numero.' fue devuelta por Administrador: '.$request->input('motivo'),
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }
        return back()->with('success', 'Cuenta devuelta al auxiliar para corrección.');
    }

    /**
     * Reenviar a revisión (Auxiliar)
     */
    public function reenviar($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if ($cuenta->user_id !== Auth::id()) {
            return back()->with('error', 'No puedes reenviar esta cuenta.');
        }

        if (!($user->hasRole('super_admin')
            || $user->hasPermission('edit_own_cuenta_cobro')
            || $user->puedeRealizarAccion('edit_own_cuenta_cobro', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion))
        ) {
            return back()->with('error', 'No tienes permisos para reenviar esta cuenta.');
        }

        if ($cuenta->estado_aprobacion !== 'en_correccion') {
            return back()->with('error', 'La cuenta no está en corrección.');
        }
        $estadoAnterior = $cuenta->estado_aprobacion;
        $cuenta->estado_aprobacion = 'en_revision';
        $cuenta->etapa_aprobacion = 'administrador';
        $cuenta->motivo_devolucion = null; // limpiar motivo de devolución al reenviar
        $cuenta->save();
        $cuenta->registrarHistorial(Auth::id(), 'reenviado', $estadoAnterior, 'en_revision', 'Auxiliar realizó correcciones y reenvió.');
        $this->notificarRoles(['administrador'], 'Cuenta reenviada para revisión (Administrador)', $cuenta);
        return back()->with('success', 'Cuenta reenviada a revisión.');
    }
    /**
     * Eliminar una cuenta de cobro.
     */
    public function destroy($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $cuenta->delete();

        return redirect()->route('cuentas_cobro.index')->with('success', 'Cuenta de cobro eliminada correctamente.');
    }

    /**
     * Exportar pagos a CSV.
     */
    public function exportarPagos(Request $request)
    {
        $format = $request->query('format', 'csv');

        $cuentas = CuentaCobro::with(['user', 'items', 'contrato'])
            ->whereNull('archived_at')
            ->latest()
            ->get();

        if ($format === 'excel') {
            $filename = "pagos_" . date('Y-m-d_H-i-s') . ".xls";
            
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function() use ($cuentas) {
                echo "<html>";
                echo "<head><meta charset='UTF-8'></head>";
                echo "<body>";
                echo "<table border='1'>";
                echo "<thead><tr>
                        <th>ID</th>
                        <th>Número</th>
                        <th>Beneficiario</th>
                        <th>Identificación</th>
                        <th>Valor Total</th>
                        <th>Fecha Emisión</th>
                        <th>Estado Aprobación</th>
                        <th>Estado Pago</th>
                        <th>Fecha Pago</th>
                        <th>Observaciones / Detalles Pago</th>
                      </tr></thead>";
                echo "<tbody>";
                foreach ($cuentas as $cuenta) {
                    echo "<tr>";
                    echo "<td>" . $cuenta->id . "</td>";
                    echo "<td>" . $cuenta->numero . "</td>";
                    echo "<td>" . $cuenta->nombre_beneficiario . "</td>";
                    echo "<td>" . $cuenta->identificacion . "</td>";
                    echo "<td>" . number_format($cuenta->valor_total, 2, '.', '') . "</td>";
                    echo "<td>" . $cuenta->fecha_emision . "</td>";
                    echo "<td>" . ucfirst(str_replace('_', ' ', $cuenta->estado_aprobacion)) . "</td>";
                    echo "<td>" . ucfirst($cuenta->estado_pago) . "</td>";
                    echo "<td>" . $cuenta->fecha_pago . "</td>";
                    echo "<td>" . $cuenta->observaciones . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</body>";
                echo "</html>";
            };

            return response()->stream($callback, 200, $headers);
        }

        $filename = "pagos_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://memory', 'w');

        // Add BOM for Excel compatibility
        fputs($handle, "\xEF\xBB\xBF");

        // Headers
        fputcsv($handle, [
            'ID',
            'Número',
            'Beneficiario',
            'Identificación',
            'Valor Total',
            'Fecha Emisión',
            'Estado Aprobación',
            'Estado Pago',
            'Fecha Pago',
            'Observaciones / Detalles Pago'
        ]);

        foreach ($cuentas as $cuenta) {
            fputcsv($handle, [
                $cuenta->id,
                $cuenta->numero,
                $cuenta->nombre_beneficiario,
                $cuenta->identificacion,
                number_format($cuenta->valor_total, 2, '.', ''),
                $cuenta->fecha_emision,
                ucfirst(str_replace('_', ' ', $cuenta->estado_aprobacion)),
                ucfirst($cuenta->estado_pago),
                $cuenta->fecha_pago,
                $cuenta->observaciones
            ]);
        }

        fseek($handle, 0);

        return response()->stream(
            function () use ($handle) {
                fpassthru($handle);
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]
        );
    }

    /**
     * Mostrar vista de pagos (tesorería)
     */
    public function pagos()
    {
        // Obtener todas las cuentas con información relevante
        $cuentas = CuentaCobro::with(['user', 'items', 'contrato'])
            ->whereNull('archived_at')
            ->latest()
            ->get();

        // Calcular estadísticas
        $totalPagos = $cuentas->sum('valor_total');
        $pagosPendientes = $cuentas->where('estado_pago', 'pending')->count();
        $pagosAprobados = $cuentas->where('estado_pago', 'approved')->count();
        $pagosRechazados = $cuentas->where('estado_pago', 'rejected')->count();

        return view('cuentas_cobro.pagos', compact(
            'cuentas',
            'totalPagos',
            'pagosPendientes',
            'pagosAprobados',
            'pagosRechazados'
        ));
    }

    /**
     * Generar y visualizar el PDF de una cuenta de cobro.
     */
    public function pdf($id)
    {
        $cuenta = CuentaCobro::with(['items', 'contrato'])->findOrFail($id);

        // Asegurar cálculos en servidor
        $subtotal = $cuenta->items->sum(function ($it) {
            return ($it->cantidad ?? 0) * ($it->precio_unitario ?? 0);
        });
        // En este proyecto, valor_total ya es el neto; si no existe, usamos subtotal
        $total = $cuenta->valor_total ?? $subtotal;

        $data = [
            'cuenta' => $cuenta,
            'subtotal' => $subtotal,
            'iva' => $cuenta->iva_valor ?? 0,
            'retFuente' => $cuenta->retencion_fuente_valor ?? 0,
            'retIca' => $cuenta->retencion_ica_valor ?? 0,
            'retIva' => $cuenta->retencion_iva_valor ?? 0,
            'total' => $total,
            'appName' => config('app.name'),
        ];

        $pdf = Pdf::loadView('cuentas_cobro.pdf', $data)->setPaper('letter');
        $fileName = 'CuentaCobro_' . ($cuenta->numero ?? $cuenta->id) . '.pdf';
        return $pdf->stream($fileName);
    }

    /**
     * Notificar al cliente (Email / WhatsApp / SMS)
     */
    public function notificarCliente(Request $request, $id)
    {
        $request->validate([
            'canales' => 'required|array',
            'mensaje' => 'nullable|string',
        ]);

        $cuenta = CuentaCobro::findOrFail($id);
        $role = Auth::user()?->role?->name;

        // Solo Tesorería (o admin) puede enviar esta notificación final
        if (!in_array($role, ['tesoreria', 'admin_programa'])) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos.'], 403);
        }

        // Generar o recuperar PDF
        $fileName = 'CuentaCobro_' . ($cuenta->numero ?? $cuenta->id) . '.pdf';
        $pdfPath = 'cuentas_cobro/' . $fileName;
        
        if (!Storage::disk('public')->exists($pdfPath)) {
            // Regenerar si no existe
             $subtotal = $cuenta->items->sum(function ($it) {
                return ($it->cantidad ?? 0) * ($it->precio_unitario ?? 0);
            });
            $total = $cuenta->valor_total ?? $subtotal;
            $data = [
                'cuenta' => $cuenta,
                'subtotal' => $subtotal,
                'iva' => $cuenta->iva_valor ?? 0,
                'retFuente' => $cuenta->retencion_fuente_valor ?? 0,
                'retIca' => $cuenta->retencion_ica_valor ?? 0,
                'retIva' => $cuenta->retencion_iva_valor ?? 0,
                'total' => $total,
                'appName' => config('app.name'),
            ];
            $pdf = Pdf::loadView('cuentas_cobro.pdf', $data)->setPaper('letter');
            Storage::disk('public')->put($pdfPath, $pdf->output());
        }
        
        $fullPdfPath = Storage::disk('public')->path($pdfPath);
        $publicPdfUrl = Storage::disk('public')->url($pdfPath);
        $mensaje = $request->input('mensaje', 'Adjunto encontrará su cuenta de cobro aprobada.');

        $results = [];

        // 1. Email
        if (in_array('email', $request->canales)) {
            $emailCliente = $cuenta->email_deudor; // Asumiendo que el deudor es el cliente
            if ($emailCliente) {
                try {
                    Mail::to($emailCliente)->send(new CuentaCobroNotification($cuenta, $mensaje, $fullPdfPath));
                    $results['email'] = 'Enviado correctamente a ' . $emailCliente;
                } catch (\Exception $e) {
                    $results['email'] = 'Error al enviar: ' . $e->getMessage();
                    \Log::error('Error enviando email cliente: ' . $e->getMessage());
                }
            } else {
                $results['email'] = 'No se encontró email del cliente (Deudor).';
            }
        }

        // 2. WhatsApp (Generar Link)
        if (in_array('whatsapp', $request->canales)) {
            $telefono = $cuenta->telefono_deudor; // Asumiendo teléfono del deudor
            if ($telefono) {
                // Limpiar teléfono
                $telefono = preg_replace('/[^0-9]/', '', $telefono);
                // Agregar código país si falta (asumiendo Colombia +57)
                if (strlen($telefono) == 10) {
                    $telefono = '57' . $telefono;
                }
                
                $texto = urlencode($mensaje . " Puede descargarla aquí: " . $publicPdfUrl);
                $link = "https://wa.me/{$telefono}?text={$texto}";
                $results['whatsapp_link'] = $link;
            } else {
                $results['whatsapp'] = 'No se encontró teléfono del cliente.';
            }
        }

        // 3. SMS (Simulado o implementación futura)
        if (in_array('sms', $request->canales)) {
             $results['sms'] = 'Envío de SMS no configurado en esta versión.';
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Archivar una cuenta (solo auxiliar dueño)
     */
    public function archivar($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if ($cuenta->user_id !== Auth::id()) {
            return back()->with('error', 'No puedes archivar esta cuenta.');
        }

        if (!($user->hasRole('super_admin')
            || $user->hasPermission('edit_own_cuenta_cobro')
            || $user->puedeRealizarAccion('archivar', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion))
        ) {
            return back()->with('error', 'No tienes permisos para archivar esta cuenta.');
        }
        if ($cuenta->archived_at) {
            return back()->with('info', 'La cuenta ya está archivada.');
        }
        $cuenta->archived_at = now();
        $cuenta->save();
        $cuenta->registrarHistorial(Auth::id(), 'archivado', $cuenta->estado_aprobacion, $cuenta->estado_aprobacion, 'Cuenta archivada por el auxiliar.');
        return redirect()->route('cuentas_cobro.index')->with('success', 'Cuenta archivada.');
    }

    /**
     * Extrae y normaliza los campos legales adicionales del request.
     */
    private function extractLegalPayload(Request $request): array
    {
        $fields = [
            'nombre_acreedor', 'tipo_documento_acreedor', 'numero_documento_acreedor',
            'ciudad_expedicion_acreedor', 'direccion_acreedor', 'telefono_acreedor', 'email_acreedor',
            'nombre_deudor', 'tipo_documento_deudor', 'numero_documento_deudor',
            'ciudad_expedicion_deudor', 'direccion_deudor', 'telefono_deudor', 'email_deudor',
            'concepto_cobro', 'descripcion_servicio', 'fecha_prestacion_servicio',
            'fecha_inicio_servicio', 'fecha_fin_servicio', 'lugar_prestacion_servicio',
            'numero_contrato_referencia', 'fecha_contrato', 'tipo_contrato', 'objeto_contrato',
            'numero_documento_soporte', 'fecha_documento_soporte', 'documento_soporte_url',
            'ciudad_expedicion_cuenta', 'prefijo_cuenta', 'serie_cuenta', 'consecutivo_cuenta',
            'condiciones_pago', 'forma_pago_acordada', 'penalidades_retraso', 'interes_mora_porcentaje',
            'cobra_intereses_mora', 'dias_gracia', 'fecha_vencimiento_real', 'observaciones_legales', 'notas_cobro',
            'estado_cobro_judicial', 'numero_proceso_judicial', 'fecha_inicio_proceso', 'juzgado',
            'radicado_judicial', 'tiene_merito_ejecutivo', 'deuda_reconocida_deudor',
            'evidencias_obligacion', 'testigos', 'clausulas_especiales', 'fecha_hora_emision',
            'dias_plazo_pago', 'fecha_vencimiento_con_gracia', 'valor_pendiente_pago',
            'iva_valor', 'retencion_fuente_valor', 'retencion_ica_valor', 'retencion_iva_valor',
        ];

        $payload = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $payload[$field] = $request->input($field);
            }
        }

        // Ensure non-nullable fields with defaults in DB are not null in payload
        $payload['interes_mora_porcentaje'] = $payload['interes_mora_porcentaje'] ?? 0;
        $payload['dias_gracia'] = $payload['dias_gracia'] ?? 0;

        $booleanFields = [
            'requiere_validacion_previa',
            'cobra_intereses_mora',
            'tiene_merito_ejecutivo',
            'deuda_reconocida_deudor',
            'recordatorio_habilitado',
        ];
        foreach ($booleanFields as $field) {
            $payload[$field] = $request->boolean($field);
        }

        return $payload;
    }

    /**
     * Calcula la fecha de vencimiento sumando el plazo configurado.
     */
    private function calculateFechaVencimiento(?string $fechaEmision, int $plazoDias): ?string
    {
        if (!$fechaEmision) {
            return null;
        }

        try {
            $fecha = Carbon::parse($fechaEmision);
        } catch (\Exception $e) {
            return null;
        }

        if ($plazoDias <= 0) {
            return $fecha->format('Y-m-d');
        }

        return $fecha->copy()->addDays($plazoDias)->format('Y-m-d');
    }

    /**
     * Divide el número de la cuenta en prefijo, serie y consecutivo.
     */
    private function splitNumeroCuenta(?string $numero): array
    {
        if (!$numero) {
            return [null, null, null];
        }

        $prefijo = $serie = null;
        $consecutivo = null;
        $parts = preg_split('/[-\s]+/', trim($numero));

        if (count($parts) >= 3) {
            $prefijo = $parts[0] ?: null;
            $serie = $parts[1] ?: null;
            $consecutivo = $parts[2] ?? null;
        } elseif (count($parts) === 2) {
            $prefijo = $parts[0] ?: null;
            $consecutivo = $parts[1] ?? null;
        } else {
            $consecutivo = $parts[0] ?? null;
        }

        if ($consecutivo !== null) {
            $soloDigitos = preg_replace('/\D+/', '', (string) $consecutivo);
            $consecutivo = $soloDigitos === '' ? $consecutivo : (int) ltrim($soloDigitos, '0');
        }

        return [$prefijo, $serie, $consecutivo];
    }

    /**
     * Desarchivar una cuenta (solo contratista dueño)
     */
    public function desarchivar($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if ($cuenta->user_id !== Auth::id()) {
            return back()->with('error', 'No puedes desarchivar esta cuenta.');
        }

        if (!($user->hasRole('super_admin')
            || $user->hasPermission('edit_own_cuenta_cobro')
            || $user->puedeRealizarAccion('archivar', $cuenta->etapa_aprobacion, $cuenta->estado_aprobacion))
        ) {
            return back()->with('error', 'No tienes permisos para desarchivar esta cuenta.');
        }
        if (!$cuenta->archived_at) {
            return back()->with('info', 'La cuenta no está archivada.');
        }
        $cuenta->archived_at = null;
        $cuenta->save();
        $cuenta->registrarHistorial(Auth::id(), 'desarchivado', $cuenta->estado_aprobacion, $cuenta->estado_aprobacion, 'Cuenta desarchivada por el contratista.');
        return redirect()->route('cuentas_cobro.show', $cuenta->id)->with('success', 'Cuenta desarchivada.');
    }

    /**
     * Vista de seguimiento general del pipeline
     */
    public function seguimientoGeneral()
    {
        // Estadísticas por estado
        $stats = [
            'pendiente' => CuentaCobro::where('estado_aprobacion', 'pendiente')->count(),
            'en_revision' => CuentaCobro::where('estado_aprobacion', 'en_revision')->count(),
            'aprobado' => CuentaCobro::where('estado_aprobacion', 'aprobado')->count(),
            'rechazado' => CuentaCobro::where('estado_aprobacion', 'rechazado')->count(),
            'pagado' => CuentaCobro::where('estado_aprobacion', 'pagado')->count(),
        ];

        // Conteo por etapa
        $porEtapa = [
            'auxiliar' => CuentaCobro::where('etapa_aprobacion', 'auxiliar')->whereIn('estado_aprobacion', ['pendiente', 'en_revision'])->count(),
            'administrador' => CuentaCobro::where('etapa_aprobacion', 'administrador')->whereIn('estado_aprobacion', ['pendiente', 'en_revision'])->count(),
            'tesoreria' => CuentaCobro::where('etapa_aprobacion', 'tesoreria')->whereIn('estado_aprobacion', ['pendiente', 'en_revision'])->count(),
        ];

        // Actividades recientes (usando historial o interacciones)
        $actividades = \App\Models\CuentaCobroHistorial::with('usuario', 'cuentaCobro')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($h) {
                return (object) [
                    'tipo' => $h->accion ?? 'info',
                    'descripcion' => $h->cuentaCobro ? "Cuenta #{$h->cuentaCobro->numero}: {$h->accion}" : $h->accion,
                    'usuario' => $h->usuario->name ?? 'Sistema',
                    'cuenta_cobro_id' => $h->cuenta_cobro_id,
                    'created_at' => $h->created_at,
                ];
            });

        return view('cuentas_cobro.seguimiento_general', compact('stats', 'porEtapa', 'actividades'));
    }

    /**
     * Vista de PDFs generados
     */
    public function pdfs(Request $request)
    {
        $query = CuentaCobro::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('nombre_beneficiario', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        $cuentas = $query->orderBy('fecha_emision', 'desc')->paginate(12);

        return view('cuentas_cobro.pdfs', compact('cuentas'));
    }

    /**
     * Movimientos General - Vista tipo Excel de todas las cuentas de cobro
     */
    public function movimientosGeneral(Request $request)
    {
        $query = CuentaCobro::with(['items', 'user', 'user.role'])
            ->select('cuentas_cobro.*');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('nombre_beneficiario', 'like', "%{$search}%")
                  ->orWhere('nombre_comprador', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        // Ordenar por fecha de emisión descendente por defecto
        $orderBy = $request->get('order_by', 'fecha_emision');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Obtener estadísticas
        $stats = [
            'total_cuentas' => CuentaCobro::count(),
            'monto_total' => CuentaCobro::sum('monto_total'),
            'pendientes' => CuentaCobro::where('estado', 'enviado')->count(),
            'pagadas' => CuentaCobro::where('estado', 'pagado')->count(),
        ];

        $cuentas = $query->paginate(50)->withQueryString();

        return view('cuentas_cobro.movimientos', compact('cuentas', 'stats'));
    }

    /**
     * Exportar movimientos a Excel con formato estandarizado
     */
    public function exportMovimientos(Request $request): StreamedResponse
    {
        $query = CuentaCobro::with(['items', 'user', 'user.role'])
            ->select('cuentas_cobro.*');

        // Aplicar los mismos filtros que en la vista
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('nombre_beneficiario', 'like', "%{$search}%")
                  ->orWhere('nombre_comprador', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        $query->orderBy('fecha_emision', 'desc');
        $cuentas = $query->get();

        // Crear el spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Movimientos');

        // Configurar encabezado de la empresa
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'REPORTE DE MOVIMIENTOS GENERAL - CUENTAS DE COBRO');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Información del reporte
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Generado: ' . Carbon::now()->format('d/m/Y H:i:s') . ' | Total registros: ' . $cuentas->count());
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Encabezados de columnas (fila 4)
        $headers = [
            'A4' => 'N°',
            'B4' => 'CONSECUTIVO',
            'C4' => 'FECHA EMISIÓN',
            'D4' => 'EMITIDO POR',
            'E4' => 'CARGO',
            'F4' => 'BENEFICIARIO',
            'G4' => 'ID BENEFICIARIO',
            'H4' => 'COMPRADOR',
            'I4' => 'CONCEPTO',
            'J4' => 'SUBTOTAL',
            'K4' => 'IVA',
            'L4' => 'TOTAL',
            'M4' => 'ESTADO',
            'N4' => 'VENCIMIENTO',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Estilo de encabezados
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ];
        $sheet->getStyle('A4:N4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(25);

        // Llenar datos
        $row = 5;
        $index = 1;
        foreach ($cuentas as $cuenta) {
            $sheet->setCellValue('A' . $row, $index);
            $sheet->setCellValue('B' . $row, $cuenta->numero ?? 'Sin número');
            $sheet->setCellValue('C' . $row, $cuenta->fecha_emision ? Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') : '-');
            $sheet->setCellValue('D' . $row, $cuenta->user->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $cuenta->user->role->name ?? '');
            $sheet->setCellValue('F' . $row, $cuenta->nombre_beneficiario ?? '-');
            $sheet->setCellValue('G' . $row, ($cuenta->tipo_identificacion_beneficiario ?? '') . ' ' . ($cuenta->identificacion_beneficiario ?? ''));
            $sheet->setCellValue('H' . $row, $cuenta->nombre_comprador ?? '-');
            $sheet->setCellValue('I' . $row, $cuenta->concepto_cobro ?? '');
            $sheet->setCellValue('J' . $row, $cuenta->subtotal ?? 0);
            $sheet->setCellValue('K' . $row, $cuenta->valor_iva ?? 0);
            $sheet->setCellValue('L' . $row, $cuenta->monto_total ?? 0);
            $sheet->setCellValue('M' . $row, ucfirst($cuenta->estado ?? 'borrador'));
            $sheet->setCellValue('N' . $row, $cuenta->fecha_vencimiento ? Carbon::parse($cuenta->fecha_vencimiento)->format('d/m/Y') : '-');

            // Alternar colores de filas
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                ]);
            }

            $index++;
            $row++;
        }

        // Aplicar bordes a todas las celdas de datos
        $lastRow = $row - 1;
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:N' . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);

            // Formato de moneda para columnas J, K, L
            $sheet->getStyle('J5:L' . $lastRow)->getNumberFormat()->setFormatCode('"$"#,##0');
            $sheet->getStyle('J5:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Fila de totales
        $totalRow = $row + 1;
        $sheet->mergeCells('A' . $totalRow . ':I' . $totalRow);
        $sheet->setCellValue('A' . $totalRow, 'TOTALES');
        $sheet->setCellValue('J' . $totalRow, '=SUM(J5:J' . $lastRow . ')');
        $sheet->setCellValue('K' . $totalRow, '=SUM(K5:K' . $lastRow . ')');
        $sheet->setCellValue('L' . $totalRow, '=SUM(L5:L' . $lastRow . ')');

        $sheet->getStyle('A' . $totalRow . ':N' . $totalRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A5F']]],
        ]);
        $sheet->getStyle('J' . $totalRow . ':L' . $totalRow)->getNumberFormat()->setFormatCode('"$"#,##0');
        $sheet->getStyle('J' . $totalRow . ':L' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Ajustar ancho de columnas
        $columnWidths = [
            'A' => 6, 'B' => 18, 'C' => 14, 'D' => 22, 'E' => 18,
            'F' => 28, 'G' => 18, 'H' => 28, 'I' => 40,
            'J' => 15, 'K' => 12, 'L' => 16, 'M' => 12, 'N' => 14,
        ];
        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Congelar fila de encabezados
        $sheet->freezePane('A5');

        // Generar archivo
        $filename = 'movimientos_cuentas_cobro_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Devolver cuenta de cobro a cualquier etapa (Admin Programa / Tesorería)
     * Permite devolver una cuenta incluso después de aprobada para ajustar plazos, montos, etc.
     */
    public function devolverGeneral(Request $request, $id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $request->validate([
            'motivo' => 'required|string|min:5|max:1000',
            'devolver_a' => 'nullable|in:auxiliar,administrador,tesoreria',
        ]);

        // Verificar permisos: solo admin_programa, super_admin o tesoreria pueden devolver
        if (!($user->hasRole('super_admin')
            || $user->hasRole('admin_programa')
            || $user->hasRole('tesoreria')
            || $user->hasRole('administrador')
            || $user->hasPermission('request_corrections')
        )) {
            return back()->with('error', 'No tienes permisos para devolver esta cuenta.');
        }

        // No permitir devolver cuentas ya anuladas o pagadas completamente
        if ($cuenta->estado_aprobacion === 'anulado') {
            return back()->with('error', 'No se puede devolver una cuenta anulada.');
        }

        if ($cuenta->estado_pago === 'paid' && $cuenta->estado_aprobacion === 'pagado') {
            return back()->with('warning', 'Esta cuenta ya fue pagada. Si necesita ajustes, considere anularla y crear una nueva.');
        }

        $estadoAnterior = $cuenta->estado_aprobacion;
        $etapaAnterior = $cuenta->etapa_aprobacion;
        $devolverA = $request->input('devolver_a', 'auxiliar');

        // Actualizar estado
        $cuenta->estado_aprobacion = 'en_correccion';
        $cuenta->etapa_aprobacion = $devolverA;
        $cuenta->motivo_devolucion = $request->input('motivo');
        $cuenta->fecha_ultima_modificacion = now();
        $cuenta->modificado_por = $user->id;
        $cuenta->save();

        // Registrar en historial
        $cuenta->registrarHistorial(
            $user->id,
            'devuelto_general',
            $estadoAnterior,
            'en_correccion',
            "Devuelto a {$devolverA} por {$user->name}. Motivo: " . $request->input('motivo')
        );

        // Notificar al destinatario
        $notificarUsuarios = [];
        
        if ($devolverA === 'auxiliar' && $cuenta->user_id) {
            $notificarUsuarios[] = $cuenta->user_id;
        } else {
            // Notificar a usuarios del rol correspondiente
            $usuariosRol = User::whereHas('role', function($q) use ($devolverA) {
                $q->where('name', $devolverA);
            })->pluck('id')->toArray();
            $notificarUsuarios = $usuariosRol;
        }

        foreach ($notificarUsuarios as $userId) {
            Notificacion::create([
                'user_id' => $userId,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Cuenta devuelta para ajuste',
                'mensaje' => "La cuenta #{$cuenta->numero} ha sido devuelta. Motivo: " . $request->input('motivo'),
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }

        return back()->with('success', "Cuenta devuelta a {$devolverA} correctamente.");
    }

    /**
     * Anular cuenta de cobro (no elimina, marca como anulada)
     */
    public function anular(Request $request, $id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|min:10|max:1000',
        ]);

        // Solo admin_programa o super_admin pueden anular
        if (!($user->hasRole('super_admin') || $user->hasRole('admin_programa'))) {
            return back()->with('error', 'Solo el administrador del programa puede anular cuentas de cobro.');
        }

        if ($cuenta->estado_aprobacion === 'anulado') {
            return back()->with('error', 'Esta cuenta ya está anulada.');
        }

        $estadoAnterior = $cuenta->estado_aprobacion;

        // Marcar como anulada
        $cuenta->estado_aprobacion = 'anulado';
        $cuenta->motivo_rechazo = 'ANULADO: ' . $request->input('motivo_anulacion');
        $cuenta->fecha_ultima_modificacion = now();
        $cuenta->modificado_por = $user->id;
        $cuenta->archived_at = now(); // Archivar automáticamente
        $cuenta->save();

        // Registrar en historial
        $cuenta->registrarHistorial(
            $user->id,
            'anulado',
            $estadoAnterior,
            'anulado',
            "Cuenta anulada por {$user->name}. Motivo: " . $request->input('motivo_anulacion')
        );

        // Notificar al creador
        if ($cuenta->user_id) {
            Notificacion::create([
                'user_id' => $cuenta->user_id,
                'tipo' => 'cuenta_cobro',
                'titulo' => 'Cuenta de cobro anulada',
                'mensaje' => "La cuenta #{$cuenta->numero} ha sido anulada. Motivo: " . $request->input('motivo_anulacion'),
                'cuenta_cobro_id' => $cuenta->id,
            ]);
        }

        return redirect()->route('cuentas_cobro.index')->with('success', 'Cuenta de cobro anulada correctamente.');
    }

    /**
     * Mostrar historial completo de una cuenta
     */
    public function historialCompleto($id)
    {
        $cuenta = CuentaCobro::with([
            'historial.user',
            'interacciones.user',
            'user',
            'items',
            'soportes',
            'documentos'
        ])->findOrFail($id);

        $user = Auth::user();

        // Verificar permisos de visualización
        $canView = $cuenta->user_id === $user->id
            || $user->hasRole('super_admin')
            || $user->hasRole('admin_programa')
            || $user->hasRole('administrador')
            || $user->hasRole('tesoreria')
            || $user->hasPermission('view_cuenta_cobro');

        if (!$canView) {
            return back()->with('error', 'No tienes permiso para ver el historial de esta cuenta.');
        }

        return view('cuentas_cobro.historial', compact('cuenta'));
    }

    /**
     * Obtener historial de todas las devoluciones del sistema (para reportes)
     */
    public function reporteDevoluciones(Request $request)
    {
        $user = Auth::user();

        if (!($user->hasRole('super_admin') || $user->hasRole('admin_programa') || $user->hasRole('tesoreria'))) {
            return back()->with('error', 'No tienes permisos para ver este reporte.');
        }

        $query = \App\Models\CuentaCobroHistorial::with(['cuenta', 'user'])
            ->whereIn('accion', ['devuelto', 'devuelto_general', 'rechazado', 'anulado'])
            ->orderByDesc('created_at');

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        $devoluciones = $query->paginate(50);

        // Estadísticas
        $stats = [
            'total_devoluciones' => \App\Models\CuentaCobroHistorial::whereIn('accion', ['devuelto', 'devuelto_general'])->count(),
            'total_rechazos' => \App\Models\CuentaCobroHistorial::where('accion', 'rechazado')->count(),
            'total_anulaciones' => \App\Models\CuentaCobroHistorial::where('accion', 'anulado')->count(),
        ];

        $usuarios = User::whereHas('role', function($q) {
            $q->whereIn('name', ['admin_programa', 'administrador', 'tesoreria']);
        })->get();

        return view('cuentas_cobro.reporte_devoluciones', compact('devoluciones', 'stats', 'usuarios'));
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\PermisoGranular;
use App\Models\User;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Mostrar todos los roles
     */
    public function index()
    {
        $roles = Role::with('users')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear un nuevo rol
     */
    public function create()
    {
        $availablePermissions = Permission::all();
        return view('roles.create', compact('availablePermissions'));
    }

    /**
     * Guardar un nuevo rol en la base de datos
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'accion_cumplimiento' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description ?? null,
            'accion_cumplimiento' => $request->accion_cumplimiento ?? null,
        ]);

        // Asignar permisos: convertir nombres a IDs
        if ($request->has('permissions')) {
            // Accept both numeric IDs and permission names
            $permissionValues = $request->permissions ?? [];
            $permissionIds = Permission::whereIn('id', $permissionValues)
                ->orWhereIn('name', $permissionValues)
                ->pluck('id')
                ->toArray();
            $role->permissions()->sync($permissionIds);
        }

        // Crear permisos granulares basados en los permisos asignados
        $this->createGranularPermissions($role, $request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente con sus permisos.');
    }

    /**
     * Crear permisos granulares automáticamente basados en los permisos asignados
     */
    protected function createGranularPermissions(Role $role, array $permissionIds = [])
    {
        // Obtener nombres de permisos asignados
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        // Mapear permisos a acciones granulares
        $granularData = [
            'role_id' => $role->id,
            'descripcion' => 'Permisos automáticos para ' . ucfirst(str_replace('_', ' ', $role->name)),
            'activo' => true,
            'puede_crear' => in_array('create_cuenta_cobro', $permissionNames),
            'puede_leer' => in_array('view_cuenta_cobro', $permissionNames) || in_array('view_own_cuenta_cobro', $permissionNames) || in_array('view_all_cuenta_cobro', $permissionNames),
            'puede_editar' => in_array('edit_own_cuenta_cobro', $permissionNames),
            'puede_eliminar' => in_array('system_admin', $permissionNames),
            'puede_aprobar' => in_array('approve_cuenta_cobro', $permissionNames) || in_array('final_approval', $permissionNames),
            'puede_rechazar' => in_array('reject_cuenta_cobro', $permissionNames),
            'puede_devolver' => in_array('request_corrections', $permissionNames),
            'puede_devolver_correccion' => in_array('request_corrections', $permissionNames),
            'puede_comentar' => in_array('add_comments', $permissionNames),
            'puede_subir_documentos' => in_array('upload_documents', $permissionNames),
            'puede_descargar_documentos' => in_array('view_documents', $permissionNames),
            'puede_registrar_pago' => in_array('process_payment', $permissionNames) || in_array('payment_confirmation', $permissionNames),
            'puede_enviar_cliente' => in_array('approve_cuenta_cobro', $permissionNames),
            'puede_archivar' => in_array('system_admin', $permissionNames),
            'puede_ver_todas_cuentas' => in_array('view_all_cuenta_cobro', $permissionNames),
            'puede_ver_reportes' => in_array('view_reports', $permissionNames) || in_array('financial_reports', $permissionNames),
            'puede_gestionar_usuarios' => in_array('manage_users', $permissionNames),
            'puede_gestionar_contratos' => in_array('manage_contracts', $permissionNames),
        ];

        // Verificar si ya existe un permiso granular para este rol
        $existing = PermisoGranular::where('role_id', $role->id)->first();
        
        if ($existing) {
            $existing->update($granularData);
        } else {
            PermisoGranular::create($granularData);
        }
    }

    /**
     * Mostrar formulario para editar un rol existente
     */
    public function edit(Role $role)
    {
        $availablePermissions = Permission::all();
        return view('roles.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Actualizar un rol existente
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'accion_cumplimiento' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description ?? null,
            'accion_cumplimiento' => $request->accion_cumplimiento ?? null,
        ]);

        // Sincronizar permisos: esto actualiza correctamente la tabla pivot
        if ($request->has('permissions')) {
            $permissionValues = $request->permissions ?? [];
            $permissionIds = Permission::whereIn('id', $permissionValues)
                ->orWhereIn('name', $permissionValues)
                ->pluck('id')
                ->toArray();
            $role->permissions()->sync($permissionIds);
            
            // Actualizar permisos granulares
            $this->createGranularPermissions($role, $permissionIds);
        } else {
            // Si no hay permisos seleccionados, remover todos
            $role->permissions()->sync([]);
            
            // Desactivar permisos granulares
            PermisoGranular::where('role_id', $role->id)->update(['activo' => false]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol y permisos actualizados correctamente.');
    }

    /**
     * Eliminar un rol
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    /**
     * Mostrar un rol (opcional, si lo usas)
     */
    public function show(Role $role)
    {
        $users = $role->users()->paginate(10);
        return view('roles.show', compact('role', 'users'));
    }

    /**
     * Asignar un rol a un usuario (AJAX)
     */
    public function assignRole(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);
        $user->role()->associate($role);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Rol asignado correctamente.']);
    }

    /**
     * Remover rol de un usuario (AJAX)
     */
    public function removeRole(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->role()->dissociate();
        $user->save();

        return response()->json(['success' => true, 'message' => 'Rol removido correctamente.']);
    }

    /**
     * Obtener usuarios sin rol (AJAX)
     */
    public function getUsersWithoutRole()
    {
        $users = User::whereNull('role_id')->get();
        return response()->json($users);
    }
}

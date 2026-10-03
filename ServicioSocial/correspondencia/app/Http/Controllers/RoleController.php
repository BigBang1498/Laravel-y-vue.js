<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('id')->get();
        $allPermissions = Permission::orderBy('module')->orderBy('label')->get()->groupBy('module');

        return view('roles.index', compact('roles', 'allPermissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $role = Role::create([
            'label' => $data['label'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()->route('roles.index')->with('success', "El rol '{$role->label}' ha sido creado exitosamente.");
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $allPermissions = Permission::orderBy('module')->orderBy('label')->get()->groupBy('module');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'allPermissions', 'rolePermissionIds'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($role->isProtected() && !$data['is_active']) {
            return back()->withErrors(['is_active' => 'El rol de Administrador no puede ser deshabilitado.']);
        }

        $role->update([
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        $permissionIds = $request->input('permissions', []);
        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.index')->with('success', "Rol y permisos de '{$role->label}' actualizados correctamente.");
    }

    public function toggleStatus(Role $role)
    {
        if ($role->isProtected()) {
            return back()->with('error', 'El rol de Administrador no puede ser deshabilitado.');
        }

        $role->is_active = !$role->is_active;
        $role->save();

        $estado = $role->is_active ? 'activado' : 'desactivado';
        return redirect()->route('roles.index')->with('success', "El rol '{$role->label}' ha sido {$estado}.");
    }

    public function destroy(Role $role)
    {
        if ($role->isProtected()) {
            return back()->with('error', 'El rol de Administrador no puede ser eliminado.');
        }

        if ($role->users()->count() > 0) {
            $count = $role->users()->count();
            return back()->with('error', "No se puede eliminar el rol '{$role->label}' porque está asignado a {$count} usuario(s). Reasigne los usuarios a otro rol antes de eliminarlo.");
        }

        $label = $role->label;
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', "El rol '{$label}' ha sido eliminado exitosamente.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'department']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->input('role_id')) {
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        if ($departmentId = $request->input('department_id')) {
            $query->where('department_id', $departmentId);
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', (bool)$request->input('status'));
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        $roles = Role::orderBy('label')->get();
        $departments = Department::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    public function create()
    {
        $roles = Role::orderBy('label')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('users.create', compact('roles', 'departments'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index')->with('success', "El usuario {$user->name} fue registrado exitosamente.");
    }

    public function show(User $user)
    {
        $user->load(['roles.permissions', 'department']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('label')->get();
        $departments = Department::active()->orderBy('name')->get();
        $user->load(['roles', 'department']);

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        if ($user->id === Auth::id() && !$data['is_active']) {
            return back()->withErrors(['is_active' => 'No puede desactivar su propia cuenta.']);
        }

        $user->update($data);
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index')->with('success', "Usuario {$user->name} actualizado correctamente.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puede eliminar su propia cuenta de usuario.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "El usuario {$name} ha sido eliminado.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puede modificar el estado de su propia cuenta.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $estado = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->route('users.index')->with('success', "El usuario {$user->name} ha sido {$estado}.");
    }
}

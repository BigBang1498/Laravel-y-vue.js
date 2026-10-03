<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('users');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', (bool)$request->input('status'));
        }

        $departments = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code'] = strtoupper($data['code']);

        $department = Department::create($data);

        return redirect()->route('departments.index')->with('success', "Departamento '{$department->name}' registrado exitosamente.");
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code'] = strtoupper($data['code']);

        $department->update($data);

        return redirect()->route('departments.index')->with('success', "Departamento '{$department->name}' actualizado correctamente.");
    }

    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return back()->with('error', "No puede eliminar el departamento '{$department->name}' porque tiene usuarios asignados.");
        }

        $name = $department->name;
        $department->delete();

        return redirect()->route('departments.index')->with('success', "El departamento '{$name}' ha sido eliminado.");
    }

    public function toggleStatus(Department $department)
    {
        $department->is_active = !$department->is_active;
        $department->save();

        $estado = $department->is_active ? 'activado' : 'desactivado';
        return redirect()->route('departments.index')->with('success', "El departamento '{$department->name}' ha sido {$estado}.");
    }
}

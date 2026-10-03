<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rectoria = Department::where('code', 'REC')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $finanzas = Department::where('code', 'FIN')->first();
        $servicios = Department::where('code', 'SG')->first();

        $usersData = [
            [
                'name' => 'Lic. Roberto Garza (Admin)',
                'email' => 'admin@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $rectoria?->id,
                'position' => 'Administrador General del Sistema',
                'phone' => 'Ext. 1001',
                'is_active' => true,
                'role' => 'admin',
            ],
            [
                'name' => 'Lic. María Elena López',
                'email' => 'secretaria@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $rectoria?->id,
                'position' => 'Secretaria de Rectoría',
                'phone' => 'Ext. 1002',
                'is_active' => true,
                'role' => 'secretaria',
            ],
            [
                'name' => 'C. Patricia Domínguez',
                'email' => 'recepcion@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $rectoria?->id,
                'position' => 'Encargada de Ventanilla Única',
                'phone' => 'Ext. 1003',
                'is_active' => true,
                'role' => 'recepcion',
            ],
            [
                'name' => 'Ing. Juan Carlos Morales',
                'email' => 'jefe_sistemas@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $sistemas?->id,
                'position' => 'Jefe de Departamento de Tecnologías',
                'phone' => 'Ext. 2001',
                'is_active' => true,
                'role' => 'jefe_departamento',
            ],
            [
                'name' => 'Ing. Ana Sofía Ruiz',
                'email' => 'empleado_sistemas@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $sistemas?->id,
                'position' => 'Analista Desarrolladora',
                'phone' => 'Ext. 2002',
                'is_active' => true,
                'role' => 'empleado',
            ],
            [
                'name' => 'C.P. Fernando Valenzuela',
                'email' => 'empleado_finanzas@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $finanzas?->id,
                'position' => 'Auxiliar Contable y Presupuestal',
                'phone' => 'Ext. 3001',
                'is_active' => true,
                'role' => 'empleado',
            ],
            [
                'name' => 'Lic. Claudia Hernández',
                'email' => 'consulta@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $servicios?->id,
                'position' => 'Auditora Externa de Consulta',
                'phone' => 'Ext. 9001',
                'is_active' => true,
                'role' => 'consulta',
            ],
            [
                'name' => 'Cristian Martin Orozco',
                'email' => 'cristian.martin@universidad.edu',
                'password' => Hash::make('password123'),
                'department_id' => $sistemas?->id,
                'position' => 'Analista Desarrollador',
                'phone' => '3324923530',
                'is_active' => true,
                'role' => 'Servicio Social',
            ],
        ];

        foreach ($usersData as $data) {
            $roleName = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(['email' => $data['email']], $data);

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }
    }
}

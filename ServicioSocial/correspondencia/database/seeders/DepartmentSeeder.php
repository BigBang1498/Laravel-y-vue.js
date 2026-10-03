<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Rectoría',
                'code' => 'REC',
                'description' => 'Despacho principal de la máxima autoridad ejecutiva universitaria.',
                'phone' => 'Ext. 1000',
                'email' => 'rectoria@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Secretaría Académica',
                'code' => 'SEC-ACAD',
                'description' => 'Coordinación docente, planes de estudio e investigación académica.',
                'phone' => 'Ext. 1100',
                'email' => 'academica@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Secretaría Administrativa',
                'code' => 'SEC-ADM',
                'description' => 'Gestión operativa, patrimonio y administración de recursos universitarios.',
                'phone' => 'Ext. 1200',
                'email' => 'administrativa@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Sistemas y Tecnologías',
                'code' => 'SIS',
                'description' => 'Infraestructura tecnológica, desarrollo de sistemas y telecomunicaciones.',
                'phone' => 'Ext. 2000',
                'email' => 'sistemas@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Recursos Humanos',
                'code' => 'RH',
                'description' => 'Administración de personal docente y administrativo, nóminas e incidencias.',
                'phone' => 'Ext. 2100',
                'email' => 'rh@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Finanzas y Contabilidad',
                'code' => 'FIN',
                'description' => 'Control presupuestal, contabilidad, pagos y rendición de cuentas institucional.',
                'phone' => 'Ext. 3000',
                'email' => 'finanzas@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Servicios Generales',
                'code' => 'SG',
                'description' => 'Mantenimiento de instalaciones, transporte, intendencia y suministros.',
                'phone' => 'Ext. 3100',
                'email' => 'servicios@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Control Escolar',
                'code' => 'CE',
                'description' => 'Inscripciones, expedición de títulos, certificados y registros de estudiantes.',
                'phone' => 'Ext. 4000',
                'email' => 'controlescolar@universidad.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Biblioteca Universitaria',
                'code' => 'BIB',
                'description' => 'Acervo bibliográfico físico y digital, salas de estudio y consulta académica.',
                'phone' => 'Ext. 5000',
                'email' => 'biblioteca@universidad.edu',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}

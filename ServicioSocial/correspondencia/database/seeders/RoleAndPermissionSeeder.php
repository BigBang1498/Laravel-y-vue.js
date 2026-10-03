<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rolesData = [
            [
                'name' => 'admin',
                'label' => 'Administrador',
                'description' => 'Acceso y control total del sistema, administración de usuarios, dependencias y auditoría.',
            ],
            [
                'name' => 'recepcion',
                'label' => 'Recepción / Correspondencia',
                'description' => 'Ventanilla única para radicación de correspondencia recibida, enviada y distribución inicial.',
            ],
            [
                'name' => 'secretaria',
                'label' => 'Secretaría',
                'description' => 'Revisión, validación de competencia institucional, asignación y seguimiento de términos.',
            ],
            [
                'name' => 'jefe_departamento',
                'label' => 'Jefe de Departamento',
                'description' => 'Gestión de correspondencia de su área, delegación a empleados y validación de respuestas.',
            ],
            [
                'name' => 'empleado',
                'label' => 'Empleado',
                'description' => 'Atención de documentos asignados, elaboración de proyectos de respuesta y anexos.',
            ],
            [
                'name' => 'consulta',
                'label' => 'Usuario de Consulta',
                'description' => 'Consulta y visualización de documentos permitidos sin facultades de modificación.',
            ],
        ];

        $createdRoles = [];
        foreach ($rolesData as $role) {
            $createdRoles[$role['name']] = Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $permissionsData = [
            ['name' => 'documents.view_all', 'label' => 'Ver toda la correspondencia', 'module' => 'Correspondencia', 'description' => 'Visualizar documentos de cualquier departamento.'],
            ['name' => 'documents.view_department', 'label' => 'Ver correspondencia de su departamento', 'module' => 'Correspondencia', 'description' => 'Visualizar documentos dirigidos o creados en su departamento.'],
            ['name' => 'documents.view_assigned', 'label' => 'Ver correspondencia asignada', 'module' => 'Correspondencia', 'description' => 'Visualizar únicamente documentos asignados directamente al usuario.'],
            ['name' => 'documents.create_received', 'label' => 'Radicar correspondencia recibida', 'module' => 'Correspondencia', 'description' => 'Registrar documentos entrantes con folio automático.'],
            ['name' => 'documents.create_sent', 'label' => 'Radicar correspondencia enviada', 'module' => 'Correspondencia', 'description' => 'Registrar documentos de salida oficial.'],
            ['name' => 'documents.edit', 'label' => 'Editar metadatos de documentos', 'module' => 'Correspondencia', 'description' => 'Modificar información básica de documentos radicados.'],
            ['name' => 'documents.change_status', 'label' => 'Modificar estados de documentos', 'module' => 'Correspondencia', 'description' => 'Avanzar o retroceder el estado del flujo de correspondencia.'],
            ['name' => 'documents.close', 'label' => 'Cerrar correspondencia', 'module' => 'Correspondencia', 'description' => 'Finalizar y dar por concluido el trámite de un documento.'],
            ['name' => 'documents.archive', 'label' => 'Archivar correspondencia', 'module' => 'Correspondencia', 'description' => 'Enviar documento a la bandeja de archivo histórico.'],

            ['name' => 'assignments.create', 'label' => 'Asignar documentos', 'module' => 'Asignaciones', 'description' => 'Asignar correspondencia a un departamento o funcionario.'],
            ['name' => 'assignments.reassign', 'label' => 'Reasignar correspondencia', 'module' => 'Asignaciones', 'description' => 'Turnar correspondencia ya asignada a otro destinatario.'],

            ['name' => 'responses.create', 'label' => 'Registrar respuestas oficiales', 'module' => 'Respuestas', 'description' => 'Emitir y vincular oficios de respuesta a un documento recibido.'],
            ['name' => 'attachments.upload', 'label' => 'Subir archivos adjuntos', 'module' => 'Adjuntos', 'description' => 'Adjuntar archivos digitales y evidencias al documento.'],
            ['name' => 'attachments.download', 'label' => 'Descargar archivos adjuntos', 'module' => 'Adjuntos', 'description' => 'Descargar y consultar los documentos digitalizados.'],
            ['name' => 'attachments.delete', 'label' => 'Eliminar archivos adjuntos', 'module' => 'Adjuntos', 'description' => 'Eliminar archivos adjuntos autorizados.'],

            ['name' => 'expedients.view', 'label' => 'Consultar expedientes', 'module' => 'Expedientes', 'description' => 'Visualizar expedientes temáticos y sus documentos agrupados.'],
            ['name' => 'expedients.manage', 'label' => 'Administrar expedientes', 'module' => 'Expedientes', 'description' => 'Crear, vincular documentos y cerrar expedientes.'],

            ['name' => 'reports.view', 'label' => 'Consultar reportes y dashboard', 'module' => 'Reportes', 'description' => 'Acceso a estadísticas e indicadores de gestión.'],
            ['name' => 'reports.export', 'label' => 'Exportar a PDF y Excel', 'module' => 'Reportes', 'description' => 'Descargar sábanas de correspondencia e informes analíticos.'],
            ['name' => 'audit.view', 'label' => 'Consultar bitácora de auditoría', 'module' => 'Auditoría', 'description' => 'Visualizar el registro forense de todas las operaciones realizadas.'],

            ['name' => 'users.manage', 'label' => 'Administrar usuarios', 'module' => 'Administración', 'description' => 'Crear, editar, activar y desactivar cuentas de usuario.'],
            ['name' => 'roles.manage', 'label' => 'Administrar roles y permisos', 'module' => 'Administración', 'description' => 'Configurar privilegios de los perfiles institucionales.'],
            ['name' => 'departments.manage', 'label' => 'Administrar departamentos', 'module' => 'Administración', 'description' => 'Crear y editar dependencias y facultades universitarias.'],
            ['name' => 'catalogs.manage', 'label' => 'Administrar catálogos', 'module' => 'Administración', 'description' => 'Gestionar tipos de documentos, prioridades y remitentes.'],
        ];

        $permissionInstances = [];
        foreach ($permissionsData as $perm) {
            $permissionInstances[$perm['name']] = Permission::updateOrCreate(['name' => $perm['name']], $perm);
        }

        $allPermissionIds = array_map(fn($p) => $p->id, $permissionInstances);
        $createdRoles['admin']->permissions()->sync($allPermissionIds);

        $createdRoles['recepcion']->permissions()->sync([
            $permissionInstances['documents.view_all']->id,
            $permissionInstances['documents.create_received']->id,
            $permissionInstances['documents.create_sent']->id,
            $permissionInstances['documents.edit']->id,
            $permissionInstances['documents.change_status']->id,
            $permissionInstances['assignments.create']->id,
            $permissionInstances['attachments.upload']->id,
            $permissionInstances['attachments.download']->id,
            $permissionInstances['expedients.view']->id,
            $permissionInstances['reports.view']->id,
        ]);

        $createdRoles['secretaria']->permissions()->sync([
            $permissionInstances['documents.view_all']->id,
            $permissionInstances['documents.create_received']->id,
            $permissionInstances['documents.create_sent']->id,
            $permissionInstances['documents.edit']->id,
            $permissionInstances['documents.change_status']->id,
            $permissionInstances['documents.close']->id,
            $permissionInstances['assignments.create']->id,
            $permissionInstances['assignments.reassign']->id,
            $permissionInstances['responses.create']->id,
            $permissionInstances['attachments.upload']->id,
            $permissionInstances['attachments.download']->id,
            $permissionInstances['expedients.view']->id,
            $permissionInstances['expedients.manage']->id,
            $permissionInstances['reports.view']->id,
            $permissionInstances['reports.export']->id,
        ]);

        $createdRoles['jefe_departamento']->permissions()->sync([
            $permissionInstances['documents.view_department']->id,
            $permissionInstances['documents.create_sent']->id,
            $permissionInstances['documents.change_status']->id,
            $permissionInstances['documents.close']->id,
            $permissionInstances['assignments.create']->id,
            $permissionInstances['assignments.reassign']->id,
            $permissionInstances['responses.create']->id,
            $permissionInstances['attachments.upload']->id,
            $permissionInstances['attachments.download']->id,
            $permissionInstances['expedients.view']->id,
            $permissionInstances['expedients.manage']->id,
            $permissionInstances['reports.view']->id,
        ]);

        $createdRoles['empleado']->permissions()->sync([
            $permissionInstances['documents.view_assigned']->id,
            $permissionInstances['documents.change_status']->id,
            $permissionInstances['responses.create']->id,
            $permissionInstances['attachments.upload']->id,
            $permissionInstances['attachments.download']->id,
            $permissionInstances['expedients.view']->id,
        ]);

        $createdRoles['consulta']->permissions()->sync([
            $permissionInstances['documents.view_assigned']->id,
            $permissionInstances['attachments.download']->id,
            $permissionInstances['expedients.view']->id,
        ]);
    }
}

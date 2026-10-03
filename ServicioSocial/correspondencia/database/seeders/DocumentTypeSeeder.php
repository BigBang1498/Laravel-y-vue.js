<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Oficio',
                'code' => 'OF',
                'description' => 'Comunicación oficial para tratar asuntos de competencia institucional entre autoridades o dependencias.',
                'default_days_to_expire' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Memorándum',
                'code' => 'MEM',
                'description' => 'Comunicación interna breve para transmitir instrucciones o recordatorios entre funcionarios.',
                'default_days_to_expire' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Circular',
                'code' => 'CIR',
                'description' => 'Aviso o disposición de carácter general dirigido a múltiples dependencias o a la comunidad universitaria.',
                'default_days_to_expire' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Solicitud',
                'code' => 'SOL',
                'description' => 'Petición formal formulada por un área, docente, estudiante o entidad externa.',
                'default_days_to_expire' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Invitación',
                'code' => 'INV',
                'description' => 'Convocatoria o invitación protocolaria a eventos, ceremonias, foros y actos académicos.',
                'default_days_to_expire' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Constancia',
                'code' => 'CON',
                'description' => 'Documento acreditativo de un hecho, participación o situación jurídica institucional.',
                'default_days_to_expire' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Informe',
                'code' => 'INF',
                'description' => 'Rendición de cuentas, balance de actividades o dictamen técnico de comisiones universitarias.',
                'default_days_to_expire' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Acta',
                'code' => 'ACT',
                'description' => 'Registro testimonial de deliberaciones y acuerdos tomados en sesiones del Consejo o comités.',
                'default_days_to_expire' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Factura',
                'code' => 'FAC',
                'description' => 'Comprobante fiscal digital relativo a adquisición de bienes o prestación de servicios contratados.',
                'default_days_to_expire' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Notificación',
                'code' => 'NOT',
                'description' => 'Comunicación formal de un acto administrativo o requerimiento con efectos jurídicos inmediatos.',
                'default_days_to_expire' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Otro',
                'code' => 'OTR',
                'description' => 'Documento no tipificado en los formatos convencionales institucionales.',
                'default_days_to_expire' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            DocumentType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}

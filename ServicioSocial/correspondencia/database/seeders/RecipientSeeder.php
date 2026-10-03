<?php

namespace Database\Seeders;

use App\Models\Recipient;
use Illuminate\Database\Seeder;

class RecipientSeeder extends Seeder
{
    public function run(): void
    {
        $recipients = [
            [
                'name' => 'Mtro. Héctor Salgado Banda',
                'institution' => 'Secretaría de Finanzas del Estado',
                'position' => 'Secretario de Finanzas, Inversión y Administración',
                'email' => 'hsalgado@finanzas.gob.mx',
                'phone' => '473-735-1200',
                'address' => 'Paseo de la Presa 103, Guanajuato, Gto.',
                'is_active' => true,
            ],
            [
                'name' => 'Dra. Carmen Enedina Rodríguez Armenta',
                'institution' => 'Subsecretaría de Educación Superior (SES)',
                'position' => 'Subsecretaria de Educación Superior',
                'email' => 'carmen.rodriguez@nube.sep.gob.mx',
                'phone' => '55-3601-3800',
                'address' => 'Brasil 31, Centro Histórico, CDMX',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Luis Armando González Placencia',
                'institution' => 'Asociación Nacional de Universidades e Instituciones de Educación Superior (ANUIES)',
                'position' => 'Secretario General Ejecutivo',
                'email' => 'secretaria.general@anuies.mx',
                'phone' => '55-5420-4900',
                'address' => 'Tenayuca 200, Sta Cruz Atoyac, Benito Juárez, CDMX',
                'is_active' => true,
            ],
            [
                'name' => 'Lic. Brenda Morales Díaz',
                'institution' => 'Órgano de Fiscalización Superior',
                'position' => 'Auditora Especial de Cumplimiento Financiero',
                'email' => 'bmorales@fiscalizacion.gob.mx',
                'phone' => '33-3679-4500',
                'address' => 'Av. Niños Héroes 2409, Guadalajara, Jal.',
                'is_active' => true,
            ],
        ];

        foreach ($recipients as $recipient) {
            Recipient::updateOrCreate(['email' => $recipient['email']], $recipient);
        }
    }
}

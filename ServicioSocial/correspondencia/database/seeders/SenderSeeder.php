<?php

namespace Database\Seeders;

use App\Models\Sender;
use Illuminate\Database\Seeder;

class SenderSeeder extends Seeder
{
    public function run(): void
    {
        $senders = [
            [
                'name' => 'Dra. María Antonieta Rosas',
                'institution' => 'Secretaría de Educación Pública',
                'position' => 'Directora General de Educación Superior Universitaria',
                'email' => 'mrosas@sep.gob.mx',
                'phone' => '55-3601-1000',
                'address' => 'Av. Universidad 1200, Col. Xoco, CDMX',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Alejandro Villar Morales',
                'institution' => 'Universidad Nacional Autónoma de México',
                'position' => 'Secretario General',
                'email' => 'avillar@unam.mx',
                'phone' => '55-5622-0000',
                'address' => 'Ciudad Universitaria, Coyoacán, CDMX',
                'is_active' => true,
            ],
            [
                'name' => 'Lic. Gerardo Ortiz Castillo',
                'institution' => 'Gobierno del Estado',
                'position' => 'Subsecretario de Enlace Institucional',
                'email' => 'gortiz@gobierno.gob.mx',
                'phone' => '33-3818-2800',
                'address' => 'Palacio de Gobierno, Centro Histórico',
                'is_active' => true,
            ],
            [
                'name' => 'Dra. Sofía Valdés Noriega',
                'institution' => 'Consejo Nacional de Humanidades, Ciencias y Tecnologías (CONAHCYT)',
                'position' => 'Coordinadora de Becas y Posgrados',
                'email' => 'svaldes@conahcyt.mx',
                'phone' => '55-5322-7700',
                'address' => 'Av. Insurgentes Sur 1582, CDMX',
                'is_active' => true,
            ],
        ];

        foreach ($senders as $sender) {
            Sender::updateOrCreate(['email' => $sender['email']], $sender);
        }
    }
}

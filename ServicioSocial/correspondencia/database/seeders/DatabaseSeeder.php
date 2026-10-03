<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            DepartmentSeeder::class,
            DocumentTypeSeeder::class,
            SenderSeeder::class,
            RecipientSeeder::class,
            UserSeeder::class,
            DocumentSeeder::class,
            AssignmentAndHistorySeeder::class,
            Phase5Seeder::class,
            Phase6Seeder::class,
            Phase7Seeder::class,
        ]);
    }
}

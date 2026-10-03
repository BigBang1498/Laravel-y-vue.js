<?php

namespace Database\Seeders;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentAssignedNotification;
use App\Notifications\DocumentDueSoonNotification;
use App\Notifications\DocumentStatusChangedNotification;
use Illuminate\Database\Seeder;

class Phase6Seeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $jefeSistemas = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $empleadoSistemas = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $secretaria = User::where('email', 'secretaria@universidad.edu')->first();

        $documents = Document::take(5)->get();

        if ($documents->count() >= 3 && $jefeSistemas && $empleadoSistemas && $admin) {
            $empleadoSistemas->notify(new DocumentAssignedNotification($documents[0], $jefeSistemas));
            $empleadoSistemas->notify(new DocumentStatusChangedNotification($documents[0], DocumentStatus::IN_PROCESS));
            $empleadoSistemas->notify(new DocumentDueSoonNotification($documents[1]));

            $jefeSistemas->notify(new DocumentAssignedNotification($documents[1], $admin));
            $jefeSistemas->notify(new DocumentStatusChangedNotification($documents[2], DocumentStatus::ATTENDED));

            if ($secretaria) {
                $secretaria->notify(new DocumentDueSoonNotification($documents[2]));
            }

            $admin->notify(new DocumentStatusChangedNotification($documents[0], DocumentStatus::IN_PROCESS));

            $readNotif = $empleadoSistemas->notifications()->latest()->first();
            if ($readNotif) {
                $readNotif->markAsRead();
            }
        }
    }
}

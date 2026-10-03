<?php

namespace App\Policies;

use App\Models\Expedient;
use App\Models\User;

class ExpedientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Expedient $expedient): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            return true;
        }

        return $expedient->department_id === $user->department_id;
    }

    public function create(User $user): bool
    {
        return $user->isActive() && ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria', 'jefe_departamento']));
    }

    public function update(User $user, Expedient $expedient): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isAdmin() || $user->hasRole('secretaria')) {
            return true;
        }

        if ($user->hasRole('jefe_departamento')) {
            return $expedient->department_id === $user->department_id;
        }

        return false;
    }

    public function manageDocuments(User $user, Expedient $expedient): bool
    {
        return $this->update($user, $expedient);
    }

    public function delete(User $user, Expedient $expedient): bool
    {
        return $user->isActive() && $user->isAdmin();
    }
}

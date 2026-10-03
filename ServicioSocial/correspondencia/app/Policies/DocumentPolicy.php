<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Document $document): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            return true;
        }

        if ($user->hasRole('jefe_departamento')) {
            return $document->department_id === $user->department_id;
        }

        if ($user->hasRole('empleado')) {
            return $document->current_assigned_user_id === $user->id || $document->department_id === $user->department_id;
        }

        if ($user->hasRole('consulta')) {
            return $document->security_level === 'public';
        }

        return false;
    }

    public function createReceived(User $user): bool
    {
        return $user->isActive() && ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria']));
    }

    public function createSent(User $user): bool
    {
        return $user->isActive() && ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria', 'jefe_departamento']));
    }

    public function update(User $user, Document $document): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasAnyRole(['recepcion', 'secretaria'])) {
            return in_array($document->status->value, ['received', 'under_review', 'assigned']);
        }

        return false;
    }

    public function changeStatus(User $user, Document $document): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            return true;
        }

        if ($user->hasRole('jefe_departamento')) {
            return $document->department_id === $user->department_id;
        }

        if ($user->hasRole('empleado')) {
            return $document->current_assigned_user_id === $user->id;
        }

        return false;
    }

    public function assign(User $user, Document $document): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if (in_array($document->status, [\App\Enums\DocumentStatus::CLOSED, \App\Enums\DocumentStatus::ARCHIVED, \App\Enums\DocumentStatus::CANCELLED, \App\Enums\DocumentStatus::REJECTED])) {
            return false;
        }

        if ($user->isAdmin() || $user->hasAnyRole(['recepcion', 'secretaria'])) {
            return true;
        }

        if ($user->hasRole('jefe_departamento')) {
            return $document->department_id === $user->department_id;
        }

        return false;
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->isActive() && $user->isAdmin();
    }
}

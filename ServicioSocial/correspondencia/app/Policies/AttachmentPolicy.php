<?php

namespace App\Policies;

use App\Enums\DocumentStatus;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\User;

class AttachmentPolicy
{
    public function upload(User $user, Document $document): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if (in_array($document->status, [DocumentStatus::CLOSED, DocumentStatus::ARCHIVED, DocumentStatus::CANCELLED, DocumentStatus::REJECTED])) {
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

        return false;
    }

    public function download(User $user, Attachment $attachment): bool
    {
        return (new DocumentPolicy())->view($user, $attachment->document);
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        $document = $attachment->document;
        if (in_array($document->status, [DocumentStatus::CLOSED, DocumentStatus::ARCHIVED, DocumentStatus::CANCELLED, DocumentStatus::REJECTED])) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $attachment->uploaded_by === $user->id;
    }
}

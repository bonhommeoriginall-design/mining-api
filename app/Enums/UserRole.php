<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Utilisateur = 'utilisateur';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Editor => 'Éditeur',
            self::Utilisateur => 'Utilisateur',
        };
    }

    public function canManageDocuments(): bool
    {
        return $this === self::Admin || $this === self::Editor;
    }

    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }
}

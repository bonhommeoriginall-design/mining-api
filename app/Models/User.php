<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'username', 'avatar_path', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isEditor(): bool
    {
        return $this->role === UserRole::Editor;
    }

    public function isUtilisateur(): bool
    {
        return $this->role === UserRole::Utilisateur;
    }

    public function initials(): string
    {
        $nameParts = preg_split('/\s+/u', trim($this->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($nameParts)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->join('');

        if ($initials !== '') {
            return $initials;
        }

        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }

    public function avatarColor(): string
    {
        $colors = ['#14b8a6', '#6366f1', '#ec4899', '#f59e0b', '#8b5cf6', '#10b981', '#0ea5e9'];

        return $colors[$this->id % count($colors)];
    }

    public function hasAvatar(): bool
    {
        return filled($this->avatar_path)
            && Storage::disk('public')->exists($this->avatar_path);
    }

    public function avatarUrl(): ?string
    {
        if (! $this->hasAvatar()) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path).'?v='.$this->updated_at?->timestamp;
    }
}

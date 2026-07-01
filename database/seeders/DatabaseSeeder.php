<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@mining.local'],
            [
                'name' => 'Administrateur MINING IA',
                'password' => Hash::make('Mining2026!'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'editeur@mining.local'],
            [
                'name' => 'Éditeur MINING IA',
                'password' => Hash::make('Mining2026!'),
                'role' => UserRole::Editor,
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'utilisateur@mining.local'],
            [
                'name' => 'Utilisateur MINING IA',
                'password' => Hash::make('Mining2026!'),
                'role' => UserRole::Utilisateur,
                'is_active' => true,
            ],
        );
    }
}

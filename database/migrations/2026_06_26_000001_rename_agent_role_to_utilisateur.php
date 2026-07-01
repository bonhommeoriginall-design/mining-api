<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'agent')->update(['role' => 'utilisateur']);

        DB::table('users')->where('email', 'agent@mining.local')->update([
            'email' => 'utilisateur@mining.local',
            'name' => 'Utilisateur MINING',
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'utilisateur')->update(['role' => 'agent']);

        DB::table('users')->where('email', 'utilisateur@mining.local')->update([
            'email' => 'agent@mining.local',
            'name' => 'Agent MINING',
        ]);
    }
};

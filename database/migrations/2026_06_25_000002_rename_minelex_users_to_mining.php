<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'admin@minelex.local' => 'admin@mining.local',
            'editeur@minelex.local' => 'editeur@mining.local',
            'agent@minelex.local' => 'agent@mining.local',
        ];

        foreach ($map as $from => $to) {
            DB::table('users')->where('email', $from)->update(['email' => $to]);
        }
    }

    public function down(): void
    {
        $map = [
            'admin@mining.local' => 'admin@minelex.local',
            'editeur@mining.local' => 'editeur@minelex.local',
            'agent@mining.local' => 'agent@minelex.local',
        ];

        foreach ($map as $from => $to) {
            DB::table('users')->where('email', $from)->update(['email' => $to]);
        }
    }
};

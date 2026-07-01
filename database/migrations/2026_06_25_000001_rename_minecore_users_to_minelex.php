<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'admin@minecore.local' => 'admin@minelex.local',
            'editeur@minecore.local' => 'editeur@minelex.local',
            'agent@minecore.local' => 'agent@minelex.local',
        ];

        foreach ($map as $from => $to) {
            DB::table('users')->where('email', $from)->update(['email' => $to]);
        }
    }

    public function down(): void
    {
        $map = [
            'admin@minelex.local' => 'admin@minecore.local',
            'editeur@minelex.local' => 'editeur@minecore.local',
            'agent@minelex.local' => 'agent@minecore.local',
        ];

        foreach ($map as $from => $to) {
            DB::table('users')->where('email', $from)->update(['email' => $to]);
        }
    }
};

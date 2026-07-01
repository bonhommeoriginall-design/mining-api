<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'admin@saemape.local' => 'admin@mining.local',
            'editeur@saemape.local' => 'editeur@mining.local',
            'agent@saemape.local' => 'agent@mining.local',
        ];

        foreach ($map as $from => $to) {
            DB::table('users')->where('email', $from)->update(['email' => $to]);
        }
    }

    public function down(): void
    {
        $map = [
            'admin@mining.local' => 'admin@saemape.local',
            'editeur@mining.local' => 'editeur@saemape.local',
            'agent@mining.local' => 'agent@saemape.local',
        ];

        foreach ($map as $from => $to) {
            DB::table('users')->where('email', $from)->update(['email' => $to]);
        }
    }
};

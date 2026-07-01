<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
        });

        User::query()->each(function (User $user): void {
            $base = Str::slug(Str::before($user->email, '@'), '');
            if ($base === '') {
                $base = 'user'.$user->id;
            }

            $candidate = $base;
            $suffix = 1;

            while (
                User::query()
                    ->where('username', $candidate)
                    ->whereKeyNot($user->id)
                    ->exists()
            ) {
                $candidate = $base.$suffix;
                $suffix++;
            }

            $user->update(['username' => $candidate]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};

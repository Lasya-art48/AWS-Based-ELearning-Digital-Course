<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learn_sphere_id_sequences', function (Blueprint $table): void {
            $table->string('role')->primary();
            $table->unsignedBigInteger('next_id');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('learn_sphere_id')->nullable()->after('id');
        });

        $roleCodes = [
            'student' => 'STU',
            'teacher' => 'TCH',
            'admin' => 'ADM',
        ];
        $nextIds = array_fill_keys(array_keys($roleCodes), 1001);

        foreach ($nextIds as $role => $nextId) {
            DB::table('learn_sphere_id_sequences')->insert([
                'role' => $role,
                'next_id' => $nextId,
            ]);
        }

        DB::table('users')->orderBy('id')->chunkById(500, function ($users) use ($roleCodes, &$nextIds): void {
            foreach ($users as $user) {
                if (! isset($roleCodes[$user->role])) {
                    throw new RuntimeException("Cannot assign a LearnSphere ID to role: {$user->role}");
                }

                $sequence = $nextIds[$user->role]++;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'learn_sphere_id' => sprintf('LS-%s-%04d', $roleCodes[$user->role], $sequence),
                    ]);
            }
        });

        foreach ($nextIds as $role => $nextId) {
            DB::table('learn_sphere_id_sequences')
                ->where('role', $role)
                ->update(['next_id' => $nextId]);
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->string('learn_sphere_id')->nullable(false)->change();
            $table->unique('learn_sphere_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['learn_sphere_id']);
            $table->dropColumn('learn_sphere_id');
        });

        Schema::dropIfExists('learn_sphere_id_sequences');
    }
};

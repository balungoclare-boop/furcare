<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments')
            || ! Schema::hasColumn('appointments', 'status')
            || ! Schema::hasColumn('appointments', 'accepted_at')) {
            return;
        }

        DB::table('appointments')
            ->where('status', 'approved')
            ->whereNull('accepted_at')
            ->update([
                'status' => 'pending',
                'accepted_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Acceptance is represented by accepted_at and cannot be safely reversed.
    }
};

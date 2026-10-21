<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('appointments') && ! Schema::hasColumn('appointments', 'accepted_at')) {
            Schema::table('appointments', function (Blueprint $table): void {
                $table->timestamp('accepted_at')->nullable()->after('status');
            });
        }

        if (Schema::hasColumn('appointments', 'accepted_at') && Schema::hasColumn('appointments', 'status')) {
            DB::table('appointments')
                ->where('status', 'approved')
                ->update([
                    'status' => 'pending',
                    'accepted_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'accepted_at')) {
            Schema::table('appointments', function (Blueprint $table): void {
                $table->dropColumn('accepted_at');
            });
        }
    }
};

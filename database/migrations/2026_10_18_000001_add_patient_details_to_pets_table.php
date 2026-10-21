<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table): void {
            if (! Schema::hasColumn('pets', 'weight')) {
                $table->decimal('weight', 6, 2)->nullable();
            }

            if (! Schema::hasColumn('pets', 'color')) {
                $table->string('color', 150)->nullable();
            }

            if (! Schema::hasColumn('pets', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }

            if (! Schema::hasColumn('pets', 'is_neutered')) {
                $table->boolean('is_neutered')->default(false);
            }

            if (! Schema::hasColumn('pets', 'microchip_number')) {
                $table->string('microchip_number', 100)->nullable();
            }

            if (! Schema::hasColumn('pets', 'allergies')) {
                $table->text('allergies')->nullable();
            }

            if (! Schema::hasColumn('pets', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        if (Schema::hasColumn('pets', 'contact_number')) {
            Schema::table('pets', function (Blueprint $table): void {
                $table->string('contact_number')->nullable()->change();
            });
        }

        if (Schema::hasColumn('pets', 'sex')) {
            Schema::table('pets', function (Blueprint $table): void {
                $table->string('sex')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'weight',
            'color',
            'date_of_birth',
            'is_neutered',
            'microchip_number',
            'allergies',
            'notes',
        ];
        $existingColumns = array_filter($columns, fn (string $column): bool => Schema::hasColumn('pets', $column));

        if ($existingColumns !== []) {
            Schema::table('pets', function (Blueprint $table) use ($existingColumns): void {
                $table->dropColumn($existingColumns);
            });
        }

        if (Schema::hasColumn('pets', 'contact_number')) {
            DB::table('pets')->whereNull('contact_number')->update(['contact_number' => '']);
            Schema::table('pets', function (Blueprint $table): void {
                $table->string('contact_number')->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('pets', 'sex')) {
            DB::table('pets')->where('sex', 'unknown')->update(['sex' => null]);
            Schema::table('pets', function (Blueprint $table): void {
                $table->enum('sex', ['male', 'female'])->nullable()->change();
            });
        }
    }
};

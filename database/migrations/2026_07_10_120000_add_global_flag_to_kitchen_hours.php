<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kitchen_hours', function (Blueprint $table) {
            $table->boolean('is_global')->default(false)->after('day_of_week');
        });

        // Make day_of_week nullable
        Schema::table('kitchen_hours', function (Blueprint $table) {
            $table->string('day_of_week', 20)->nullable()->change();
        });

        // Add a plain tenant_id index first so the FK keeps a usable index,
        // then drop the per-day unique index.
        Schema::table('kitchen_hours', function (Blueprint $table) {
            $table->index('tenant_id');
        });
        Schema::table('kitchen_hours', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'day_of_week']);
        });

        // Convert existing rows into one global row
        $first = DB::table('kitchen_hours')->orderBy('id')->first();
        if ($first) {
            DB::table('kitchen_hours')->where('id', '!=', $first->id)->delete();
            DB::table('kitchen_hours')->where('id', $first->id)->update([
                'day_of_week' => null,
                'is_global' => true,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('kitchen_hours', function (Blueprint $table) {
            $table->dropColumn('is_global');
        });
    }
};

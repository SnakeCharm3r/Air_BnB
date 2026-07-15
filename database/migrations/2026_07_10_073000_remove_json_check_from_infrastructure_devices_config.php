<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the JSON validity CHECK constraint so plain text notes can be stored.
        DB::statement('ALTER TABLE infrastructure_devices MODIFY config LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE infrastructure_devices MODIFY config LONGTEXT NULL CHECK (json_valid(`config`))');
    }
};

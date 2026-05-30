<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('check_in_time')->nullable()->after('check_in_date');
            $table->time('check_out_time')->nullable()->after('check_out_date');
            $table->enum('payment_type', ['cash', 'crdb'])->nullable()->after('status');
            $table->string('payment_reference')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['check_in_time', 'check_out_time', 'payment_type', 'payment_reference']);
        });
    }
};

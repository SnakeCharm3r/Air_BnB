<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the old payment_type column if it exists
            if (Schema::hasColumn('bookings', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });

        // Add the new payment_method column with extended options
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'credit_card', 'bank_transfer', 'crdb', 'selcom', 'dpo', 'gepg', 'mobile_money', 'control_number'])->nullable()->after('guest_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'payment_type')) {
                $table->enum('payment_type', ['cash', 'crdb'])->nullable()->after('guest_phone');
            }
        });
    }
};

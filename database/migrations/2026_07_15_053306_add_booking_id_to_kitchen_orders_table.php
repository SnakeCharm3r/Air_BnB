<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kitchen_orders', function (Blueprint $table) {
            $table->foreignId('booking_id')->nullable()->after('room_id')->constrained('bookings')->nullOnDelete();
            $table->unsignedBigInteger('folio_charge_id')->nullable()->after('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_orders', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn(['booking_id', 'folio_charge_id']);
        });
    }
};

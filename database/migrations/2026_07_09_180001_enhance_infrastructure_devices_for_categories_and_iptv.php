<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infrastructure_devices', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id');
            $table->foreign('category_id')->references('id')->on('infrastructure_categories')->nullOnDelete();

            $table->unsignedBigInteger('room_id')->nullable()->after('location');
            $table->unsignedBigInteger('iptv_device_id')->nullable()->unique()->after('room_id');
            $table->string('ip_address')->nullable()->after('status');
            $table->string('mac_address')->nullable()->after('ip_address');
            $table->string('serial_number')->nullable()->after('mac_address');
            $table->string('source')->default('manual')->after('serial_number');
            $table->timestamp('iptv_last_seen')->nullable()->after('last_checked');
        });

        // Convert the enum column to varchar to allow dynamic category-driven types
        DB::statement("ALTER TABLE infrastructure_devices MODIFY device_type VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        Schema::table('infrastructure_devices', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'room_id',
                'iptv_device_id',
                'ip_address',
                'mac_address',
                'serial_number',
                'source',
                'iptv_last_seen',
            ]);
        });

        DB::statement("ALTER TABLE infrastructure_devices MODIFY device_type ENUM('cctv','water_pump','generator','solar','ac','other') NOT NULL DEFAULT 'other'");
    }
};

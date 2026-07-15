<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infrastructure_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('infrastructure_categories')->insert([
            ['name' => 'TV', 'slug' => 'tv', 'icon' => 'tv', 'description' => 'Television sets and IPTV receivers', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AC', 'slug' => 'ac', 'icon' => 'wind', 'description' => 'Air conditioning units', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CCTV', 'slug' => 'cctv', 'icon' => 'video', 'description' => 'Security cameras and surveillance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Water Pump', 'slug' => 'water-pump', 'icon' => 'droplet', 'description' => 'Water pumps', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Generator', 'slug' => 'generator', 'icon' => 'zap', 'description' => 'Power generators', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Solar', 'slug' => 'solar', 'icon' => 'sun', 'description' => 'Solar power systems', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'slug' => 'other', 'icon' => 'box', 'description' => 'Other infrastructure devices', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructure_categories');
    }
};

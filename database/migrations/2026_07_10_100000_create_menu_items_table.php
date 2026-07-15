<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->unsignedBigInteger('iptv_menu_item_id')->nullable();
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('unit')->nullable();
            $table->string('image')->nullable();
            $table->boolean('available')->default(true);
            $table->time('available_from')->nullable();
            $table->time('available_until')->nullable();
            $table->boolean('requires_chef')->default(false);
            $table->timestamps();

            $table->index('category');
            $table->index('iptv_menu_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};

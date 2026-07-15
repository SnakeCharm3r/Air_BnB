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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('inventory_items', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Enhanced categories
            if (!Schema::hasColumn('inventory_items', 'category')) {
                $table->enum('category', ['drinks', 'food', 'amenities', 'cleaning_supplies', 'maintenance_materials', 'linen', 'stationery'])->default('amenities')->after('name');
            }
        });

        // Create inventory_usage table if it doesn't exist
        if (!Schema::hasTable('inventory_usage')) {
            Schema::create('inventory_usage', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->enum('type', ['purchase', 'stock_in', 'stock_out', 'sale', 'adjustment', 'transfer', 'restock'])->default('restock');
                $table->integer('quantity');
                $table->string('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('date')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
                
                $table->index(['item_id', 'date']);
                $table->index('type');
            });
        } else {
            // If table exists, enhance it
            Schema::table('inventory_usage', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_usage', 'tenant_id')) {
                    $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                }
                
                if (!Schema::hasColumn('inventory_usage', 'type')) {
                    $table->enum('type', ['purchase', 'stock_in', 'stock_out', 'sale', 'adjustment', 'transfer', 'restock'])->default('restock')->after('item_id');
                }
                
                if (!Schema::hasColumn('inventory_usage', 'reference_type')) {
                    $table->string('reference_type')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('inventory_usage', 'reference_id')) {
                    $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'category']);
        });

        if (Schema::hasTable('inventory_usage')) {
            Schema::table('inventory_usage', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn(['tenant_id', 'type', 'reference_type', 'reference_id']);
            });
        }
    }
};

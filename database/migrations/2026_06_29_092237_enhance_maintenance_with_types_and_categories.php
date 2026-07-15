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
        Schema::table('maintenance_tasks', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('maintenance_tasks', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Add maintenance type
            if (!Schema::hasColumn('maintenance_tasks', 'maintenance_type')) {
                $table->enum('maintenance_type', ['corrective', 'preventive', 'emergency'])->default('corrective')->after('title');
            }
            
            // Add category
            if (!Schema::hasColumn('maintenance_tasks', 'category')) {
                $table->enum('category', ['plumbing', 'electrical', 'furniture', 'air_conditioning', 'painting', 'bathroom', 'internet', 'general'])->default('general')->after('maintenance_type');
            }
            
            // Add vendor information
            if (!Schema::hasColumn('maintenance_tasks', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('cost');
            }
            if (!Schema::hasColumn('maintenance_tasks', 'vendor_contact')) {
                $table->string('vendor_contact')->nullable()->after('vendor_name');
            }
            
            // Add attachment support
            if (!Schema::hasColumn('maintenance_tasks', 'attachments')) {
                $table->json('attachments')->nullable()->after('vendor_contact');
            }
            
            // Add room maintenance history reference
            if (!Schema::hasColumn('maintenance_tasks', 'room_maintenance_count')) {
                $table->integer('room_maintenance_count')->default(0)->after('attachments');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_tasks', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'maintenance_type', 'category', 'vendor_name', 'vendor_contact', 'attachments', 'room_maintenance_count']);
        });
    }
};

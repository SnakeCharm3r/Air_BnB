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
        Schema::table('bookings', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('bookings', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Add guest_id for CRM integration
            if (!Schema::hasColumn('bookings', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->after('room_id')->constrained('guests')->nullOnDelete();
            }
            
            // Add reservation type
            if (!Schema::hasColumn('bookings', 'reservation_type')) {
                $table->enum('reservation_type', ['walk_in', 'advance', 'group', 'corporate', 'vip'])->default('walk_in')->after('guest_id');
            }
            
            // Add enhanced statuses
            if (!Schema::hasColumn('bookings', 'is_no_show')) {
                $table->boolean('is_no_show')->default(false)->after('status');
            }
            if (!Schema::hasColumn('bookings', 'no_show_reason')) {
                $table->string('no_show_reason')->nullable()->after('is_no_show');
            }
            if (!Schema::hasColumn('bookings', 'expiry_date')) {
                $table->timestamp('expiry_date')->nullable()->after('no_show_reason');
            }
            if (!Schema::hasColumn('bookings', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('expiry_date');
            }
            
            // Add corporate fields
            if (!Schema::hasColumn('bookings', 'company_name')) {
                $table->string('company_name')->nullable()->after('guest_phone');
            }
            if (!Schema::hasColumn('bookings', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('bookings', 'credit_terms_days')) {
                $table->integer('credit_terms_days')->nullable()->after('tax_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['guest_id']);
            $table->dropColumn(['tenant_id', 'guest_id', 'reservation_type', 'is_no_show', 'no_show_reason', 'expiry_date', 'reminder_sent_at', 'company_name', 'tax_id', 'credit_terms_days']);
        });
    }
};

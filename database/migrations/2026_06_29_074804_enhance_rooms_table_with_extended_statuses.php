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
        Schema::table('rooms', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('rooms', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Add new columns
            if (!Schema::hasColumn('rooms', 'is_smoking')) {
                $table->boolean('is_smoking')->default(false)->after('status');
            }
            if (!Schema::hasColumn('rooms', 'is_accessible')) {
                $table->boolean('is_accessible')->default(false)->after('is_smoking');
            }
            if (!Schema::hasColumn('rooms', 'blocked_until')) {
                $table->timestamp('blocked_until')->nullable()->after('is_accessible');
            }
            if (!Schema::hasColumn('rooms', 'blocked_reason')) {
                $table->string('blocked_reason')->nullable()->after('blocked_until');
            }
            if (!Schema::hasColumn('rooms', 'last_cleaned_at')) {
                $table->timestamp('last_cleaned_at')->nullable()->after('blocked_reason');
            }
            if (!Schema::hasColumn('rooms', 'cleaned_by')) {
                $table->foreignId('cleaned_by')->nullable()->after('last_cleaned_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['cleaned_by']);
            $table->dropColumn(['tenant_id', 'is_smoking', 'is_accessible', 'blocked_until', 'blocked_reason', 'last_cleaned_at', 'cleaned_by']);
        });
    }
};

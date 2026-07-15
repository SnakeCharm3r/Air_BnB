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
        Schema::create('night_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('audit_date')->unique();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            
            // Statistics
            $table->integer('total_rooms')->default(0);
            $table->integer('occupied_rooms')->default(0);
            $table->integer('available_rooms')->default(0);
            $table->integer('arrivals')->default(0);
            $table->integer('departures')->default(0);
            $table->integer('no_shows')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('cash_collected', 12, 2)->default(0);
            $table->decimal('card_collected', 12, 2)->default(0);
            $table->decimal('transfer_collected', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            
            // Actions taken
            $table->json('room_charges_posted')->nullable();
            $table->json('no_shows_processed')->nullable();
            $table->json('reservations_expired')->nullable();
            $table->json('next_day_arrivals')->nullable();
            $table->json('next_day_departures')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('discrepancies')->nullable();
            $table->timestamps();
            
            $table->index('audit_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('night_audits');
    }
};

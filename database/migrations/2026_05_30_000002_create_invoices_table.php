<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('room_number');
            $table->date('check_in_date');
            $table->time('check_in_time')->nullable();
            $table->date('check_out_date');
            $table->time('check_out_time')->nullable();
            $table->integer('nights');
            $table->decimal('room_rate', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2);
            $table->enum('payment_type', ['cash', 'crdb'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'paid', 'partial', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

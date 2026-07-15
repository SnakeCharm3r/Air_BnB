<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrade the billing module to a professional PMS accounting structure.
 *
 * Adds folio-centric accounting columns to guest_folios, booking_charges,
 * payments, invoices, and bookings while keeping all existing columns intact
 * for backward compatibility.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ==========================================================
        // 1. GUEST FOLIOS
        // ==========================================================
        // The folio becomes the guest's financial account. We add a
        // dedicated subtotal and service_charge column, plus a payment_status
        // that tracks the folio independently from the booking operational status.
        Schema::table('guest_folios', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_folios', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('guest_folios', 'service_charge')) {
                $table->decimal('service_charge', 12, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('guest_folios', 'payment_status')) {
                $table->string('payment_status', 30)->default('unpaid')->after('balance_due');
            }

            $table->index('booking_id');
            $table->index('status');
            $table->index('payment_status');
        });

        // ==========================================================
        // 2. BOOKING CHARGES (Transaction Ledger)
        // ==========================================================
        // Every billable item becomes one row tied to a folio. We add folio_id,
        // charge_type, quantity, unit_price, discount, total, posting metadata,
        // and reference fields for integrations such as restaurant POS,
        // laundry, or kitchen orders.
        Schema::table('booking_charges', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_charges', 'folio_id')) {
                $table->foreignId('folio_id')->nullable()->after('booking_id')->constrained('guest_folios')->nullOnDelete();
            }
            if (!Schema::hasColumn('booking_charges', 'charge_type')) {
                $table->string('charge_type', 50)->default('miscellaneous')->after('description');
            }
            if (!Schema::hasColumn('booking_charges', 'quantity')) {
                $table->decimal('quantity', 10, 2)->default(1)->after('charge_type');
            }
            if (!Schema::hasColumn('booking_charges', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('booking_charges', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('booking_charges', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('booking_charges', 'posting_date')) {
                $table->date('posting_date')->default(now()->toDateString())->after('total_amount');
            }
            if (!Schema::hasColumn('booking_charges', 'status')) {
                $table->string('status', 30)->default('posted')->after('posting_date');
            }
            if (!Schema::hasColumn('booking_charges', 'posted_by')) {
                $table->foreignId('posted_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('booking_charges', 'reference_type')) {
                $table->string('reference_type', 50)->nullable()->after('posted_by');
            }
            if (!Schema::hasColumn('booking_charges', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }

            $table->index('folio_id');
            $table->index('booking_id');
            $table->index('charge_type');
            $table->index('posting_date');
            $table->index('status');
            $table->index(['reference_type', 'reference_id']);
        });

        // ==========================================================
        // 3. PAYMENTS
        // ==========================================================
        // Payments are linked to a folio and only reduce the balance. We add
        // payment status, gateway, cashier shift, receipt details, currency,
        // approval, and void support for refunds and reversals.
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'folio_id')) {
                $table->foreignId('folio_id')->nullable()->after('booking_id')->constrained('guest_folios')->nullOnDelete();
            }
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->date('payment_date')->default(now()->toDateString())->after('amount');
            }
            if (!Schema::hasColumn('payments', 'payment_status')) {
                $table->string('payment_status', 30)->default('successful')->after('payment_date');
            }
            if (!Schema::hasColumn('payments', 'payment_gateway')) {
                $table->string('payment_gateway', 50)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('payments', 'cashier_shift')) {
                $table->string('cashier_shift', 50)->nullable()->after('payment_gateway');
            }
            if (!Schema::hasColumn('payments', 'receipt_number')) {
                $table->string('receipt_number', 50)->nullable()->after('cashier_shift')->unique();
            }
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 10)->default('TZS')->after('receipt_number');
            }
            if (!Schema::hasColumn('payments', 'exchange_rate')) {
                $table->decimal('exchange_rate', 12, 6)->default(1)->after('currency');
            }
            if (!Schema::hasColumn('payments', 'bank_name')) {
                $table->string('bank_name', 100)->nullable()->after('exchange_rate');
            }
            if (!Schema::hasColumn('payments', 'mobile_provider')) {
                $table->string('mobile_provider', 50)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('payments', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('mobile_provider')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('payments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('payments', 'is_void')) {
                $table->boolean('is_void')->default(false)->after('approved_at');
            }
            if (!Schema::hasColumn('payments', 'void_reason')) {
                $table->string('void_reason')->nullable()->after('is_void');
            }

            $table->index('folio_id');
            $table->index('booking_id');
            $table->index('payment_status');
            $table->index('payment_date');
            $table->index('is_void');
        });

        // ==========================================================
        // 4. INVOICES
        // ==========================================================
        // Invoices are generated from a folio snapshot at checkout. We add
        // folio_id, invoice_status, invoice_date, issued_by, payment terms,
        // customer type, discount/service rates, and grand total.
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'folio_id')) {
                $table->foreignId('folio_id')->nullable()->after('booking_id')->constrained('guest_folios')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'invoice_status')) {
                $table->string('invoice_status', 30)->default('draft')->after('folio_id');
            }
            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->date('invoice_date')->default(now()->toDateString())->after('invoice_status');
            }
            if (!Schema::hasColumn('invoices', 'issued_by')) {
                $table->foreignId('issued_by')->nullable()->after('invoice_date')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'payment_terms')) {
                $table->string('payment_terms', 50)->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('invoices', 'customer_type')) {
                $table->string('customer_type', 30)->default('walk_in')->after('payment_terms');
            }
            if (!Schema::hasColumn('invoices', 'discount_rate')) {
                $table->decimal('discount_rate', 5, 4)->default(0)->after('customer_type');
            }
            if (!Schema::hasColumn('invoices', 'service_charge_rate')) {
                $table->decimal('service_charge_rate', 5, 4)->default(0)->after('discount_rate');
            }
            if (!Schema::hasColumn('invoices', 'service_charge')) {
                $table->decimal('service_charge', 12, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('invoices', 'grand_total')) {
                $table->decimal('grand_total', 12, 2)->default(0)->after('total_amount');
            }

            $table->index('folio_id');
            $table->index('booking_id');
            $table->index('invoice_status');
            $table->index('invoice_date');
            $table->index('customer_type');
        });

        // ==========================================================
        // 5. BOOKINGS
        // ==========================================================
        // Booking status is purely operational. We add a separate payment_status
        // so that accounting and operations can be tracked independently.
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status', 30)->default('unpaid')->after('status');
            }

            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_status']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['folio_id']);
            $table->dropForeign(['issued_by']);
            $table->dropColumn([
                'folio_id', 'invoice_status', 'invoice_date', 'issued_by',
                'payment_terms', 'customer_type', 'discount_rate', 'service_charge_rate',
                'service_charge', 'grand_total',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['folio_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'folio_id', 'payment_date', 'payment_status', 'payment_gateway', 'cashier_shift',
                'receipt_number', 'currency', 'exchange_rate', 'bank_name', 'mobile_provider',
                'approved_by', 'approved_at', 'is_void', 'void_reason',
            ]);
        });

        Schema::table('booking_charges', function (Blueprint $table) {
            $table->dropForeign(['folio_id']);
            $table->dropForeign(['posted_by']);
            $table->dropColumn([
                'folio_id', 'charge_type', 'quantity', 'unit_price', 'discount_amount',
                'total_amount', 'posting_date', 'status', 'posted_by', 'reference_type', 'reference_id',
            ]);
        });

        Schema::table('guest_folios', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'service_charge', 'payment_status']);
        });
    }
};

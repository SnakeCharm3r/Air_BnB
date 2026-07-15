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
        Schema::table('payments', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('payments', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Enhanced payment methods
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'credit_card', 'bank_transfer', 'crdb', 'selcom', 'dpo', 'gepg', 'mobile_money', 'control_number'])->default('cash')->after('amount');
            }
            
            // Add split payment support
            if (!Schema::hasColumn('payments', 'is_split_payment')) {
                $table->boolean('is_split_payment')->default(false)->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'parent_payment_id')) {
                $table->foreignId('parent_payment_id')->nullable()->after('is_split_payment')->constrained('payments')->nullOnDelete();
            }
            
            // Add refund support
            if (!Schema::hasColumn('payments', 'is_refund')) {
                $table->boolean('is_refund')->default(false)->after('parent_payment_id');
            }
            if (!Schema::hasColumn('payments', 'refunded_payment_id')) {
                $table->foreignId('refunded_payment_id')->nullable()->after('is_refund')->constrained('payments')->nullOnDelete();
            }
            if (!Schema::hasColumn('payments', 'refund_reason')) {
                $table->string('refund_reason')->nullable()->after('refunded_payment_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('invoices', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            
            // Corporate invoice enhancements
            if (!Schema::hasColumn('invoices', 'invoice_type')) {
                $table->enum('invoice_type', ['standard', 'vip', 'corporate', 'ngo', 'government'])->default('standard')->after('invoice_number');
            }
            if (!Schema::hasColumn('invoices', 'credit_terms_days')) {
                $table->integer('credit_terms_days')->default(0)->after('invoice_type');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('credit_terms_days');
            }
            if (!Schema::hasColumn('invoices', 'company_name')) {
                $table->string('company_name')->nullable()->after('guest_name');
            }
            if (!Schema::hasColumn('invoices', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('invoices', 'vat_number')) {
                $table->string('vat_number')->nullable()->after('tax_id');
            }
            
            // Enhanced invoice status
            if (!Schema::hasColumn('invoices', 'overdue_since')) {
                $table->timestamp('overdue_since')->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('overdue_since');
            }
            if (!Schema::hasColumn('invoices', 'final_notice_sent_at')) {
                $table->timestamp('final_notice_sent_at')->nullable()->after('reminder_sent_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['parent_payment_id']);
            $table->dropForeign(['refunded_payment_id']);
            $table->dropColumn(['tenant_id', 'payment_method', 'is_split_payment', 'parent_payment_id', 'is_refund', 'refunded_payment_id', 'refund_reason']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'invoice_type', 'credit_terms_days', 'due_date', 'company_name', 'tax_id', 'vat_number', 'overdue_since', 'reminder_sent_at', 'final_notice_sent_at']);
        });
    }
};

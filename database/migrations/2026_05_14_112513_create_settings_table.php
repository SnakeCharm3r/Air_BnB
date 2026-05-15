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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('lodge_name')->default('LodgeOS');
            $table->string('lodge_logo')->nullable();
            $table->string('login_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('owner_email')->nullable();
            $table->integer('max_login_attempts')->default(3);
            $table->integer('lockout_duration')->default(30); // minutes
            $table->boolean('two_factor_auth')->default(false);
            $table->integer('session_timeout')->default(24); // hours
            $table->boolean('audit_logging')->default(true);
            $table->json('notification_settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

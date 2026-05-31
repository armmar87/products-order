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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'customer'])->default('customer');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('referral_code', 50)->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('user_security', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('api_token')->unique()->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->unsignedInteger('login_attempts')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('timezone', 50)->default('UTC');
            $table->string('locale', 10)->default('en');
            $table->json('preferences')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('user_payment_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('stripe_customer_id')->unique()->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_payment_info');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_security');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');
    }
};

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
        Schema::create('social_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // 'google', 'facebook', etc.
            $table->string('provider_id'); // The ID from the OAuth provider
            $table->text('token')->nullable(); // OAuth access token
            $table->text('refresh_token')->nullable(); // OAuth refresh token
            $table->timestamp('expires_at')->nullable(); // Token expiration
            $table->timestamps();

            // Ensure unique combination of provider and provider_id
            $table->unique(['provider', 'provider_id']);
            
            // Index for faster lookups
            $table->index(['user_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_logins');
    }
};

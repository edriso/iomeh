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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('points_earned')->default(0);
            $table->text('notes')->nullable();
            $table->string('proof_url')->nullable();
            $table->timestamps();
            
            // Unique constraint: one activity per interest per day
            $table->unique(['user_id', 'interest_id', 'date'], 'unique_user_interest_date');
            
            // Indexes for performance
            $table->index(['user_id', 'date'], 'idx_user_date'); // For checking today's activities
            $table->index('date', 'idx_date'); // For recent activities
            $table->index(['user_id', 'created_at']); // For user's activity history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('season')->nullable(); // 1=Q1, 2=Q2, 3=Q3, 4=Q4, NULL=yearly
            $table->year('year');
            $table->integer('points')->default(0);
            $table->integer('rank')->default(0);
            $table->timestamps();
            
            // Unique constraint: one ranking per user per season per year
            $table->unique(['user_id', 'season', 'year'], 'unique_user_season_year');
            
            // Indexes for leaderboard queries
            $table->index(['year', 'season', 'rank']); // For season rankings
            $table->index(['year', 'rank']); // For yearly rankings (when season is NULL)
            $table->index(['user_id', 'year']); // For user's ranking history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};


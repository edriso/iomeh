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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('name'); // 1=Q1, 2=Q2, 3=Q3, 4=Q4 (NOT nullable - only season records)
            $table->year('year');
            $table->integer('points')->default(0);
            $table->integer('season_year_points')->default(0);
            $table->timestamps();
            
            // Unique constraint: one season per user per name per year
            $table->unique(['user_id', 'name', 'year'], 'unique_user_name_year');
            
            // Indexes for leaderboard queries
            $table->index(['year', 'name', 'points']); // For season rankings
            $table->index(['year', 'name', 'season_year_points']); // For yearly rankings
            $table->index(['user_id', 'year']); // For user's season history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};


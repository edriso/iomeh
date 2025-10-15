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
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_type_id')->constrained()->cascadeOnDelete();
            $table->string('custom_name');
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->timestamps();
            
            // Unique constraint: can't favorite the same activity type twice
            $table->unique(['user_id', 'activity_type_id'], 'unique_user_activity_type');
            
            // Index for user interests ordering
            $table->index(['user_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interests');
    }
};

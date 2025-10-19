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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_type_id')->nullable()->constrained('activity_types')->nullOnDelete();
            $table->string('custom_name');
            $table->string('custom_icon', 50)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->timestamps();
            
            // Index for user habits ordering
            $table->index(['user_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};

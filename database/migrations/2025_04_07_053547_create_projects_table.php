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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['ideation', 'planning', 'construction', 'completed'])->default('ideation');
            $table->string('location')->nullable(); // property address
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('current_property_value', 15, 2)->nullable();
            $table->decimal('calculated_value', 15, 2)->nullable();
            $table->foreignId('entrepreneur_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

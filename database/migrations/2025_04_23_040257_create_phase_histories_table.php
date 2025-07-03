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
        Schema::create('phase_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('phases')->onDelete('cascade');
            $table->string('stage')->nullable()->comment('See PhaseStage');
            $table->string('type')->nullable()->comment('See PhaseType');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('not_started');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('progress')->nullable();
            $table->decimal('planned_cost', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->date('date_of_expense')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_histories');
    }
};

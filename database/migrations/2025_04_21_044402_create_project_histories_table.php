<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('project_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');  // link to the projects table
            $table->foreignId('project_category_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('current_property_value', 15, 2)->nullable();
            $table->decimal('calculated_value', 15, 2)->nullable();
            $table->foreignId('entrepreneur_id')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('renovation_cost', 15, 2)->nullable();
            $table->timestamp('changed_at')->nullable(); // Time when this history was created
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('project_histories');
    }
};

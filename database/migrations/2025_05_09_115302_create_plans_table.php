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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('currency');
            $table->string('code');
            $table->string('symbol');
            $table->string('thousand_separator')->nullable();
            $table->string('decimal_separator')->nullable();
            $table->timestamps();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id');
            $table->double('amount')->default(0);
            $table->string('title');
            $table->string('duration')->nullable()->comment('see PlanDuration');
            $table->longText('notes')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('plans');
    }
};

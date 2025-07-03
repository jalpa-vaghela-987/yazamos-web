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
        Schema::table('phases', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('project_id');
            $table->foreign('parent_id')->references('id')->on('phases')->onDelete('cascade');
            $table->string('stage')->after('parent_id')->comment('See PhaseStage');
            $table->string('type')->nullable()->comment('See PhaseType');

            //set nullable
            $table->string('status')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phases', function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'stage', 'type']);

            $table->string('status')->nullable(false)->change();
        });
    }
};

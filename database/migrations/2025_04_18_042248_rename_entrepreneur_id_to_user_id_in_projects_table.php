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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['entrepreneur_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('entrepreneur_id', 'user_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('user_id', 'entrepreneur_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('entrepreneur_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};

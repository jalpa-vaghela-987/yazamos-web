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
        Schema::table('milestones', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
        });

        DB::statement("
            DELETE FROM milestones
            WHERE project_id IS NOT NULL
            AND project_id NOT IN (SELECT id FROM projects)
        ");

        Schema::table('milestones', function (Blueprint $table) {
            $table->foreign('project_id') ->references('id')->on('projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
        Schema::table('milestones', function (Blueprint $table) {
            $table->renameColumn('due_date', 'date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');

            $table->foreignId('phase_id')->constrained()->onDelete('cascade');
            $table->string('status')->change();
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->renameColumn('date', 'due_date');
        });
    }
};

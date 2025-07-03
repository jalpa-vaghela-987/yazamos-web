<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // First drop the existing foreign key
            $table->dropForeign(['project_id']);
            
            // Add the correct foreign key constraint
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the correct foreign key
            $table->dropForeign(['project_id']);
            
            // Restore the original incorrect foreign key
            $table->foreign('project_id')
                  ->references('id')
                  ->on('messages')
                  ->onDelete('cascade');
        });
    }
}; 
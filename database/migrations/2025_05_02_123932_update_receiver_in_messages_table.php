<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReceiverInMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['receiver_id']);

            // Modify the column to be nullable and re-add the foreign key
            $table->foreignId('receiver_id')
                ->nullable()
                ->change();

            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Add the new receiver_type column
            $table->tinyInteger('receiver_type')
                ->nullable()
                ->comment('0 = All, 1 = Investor, 2 = Tenant, 3 = Admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the modified foreign key
            $table->dropForeign(['receiver_id']);

            // Change back to non-nullable if necessary (adjust depending on original state)
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->change();

            // Drop the new column
            $table->dropColumn('receiver_type');
        });
    }
}


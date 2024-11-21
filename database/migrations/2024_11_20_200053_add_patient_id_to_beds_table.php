<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            // Add room_id if it doesn't exist
            if (!Schema::hasColumn('beds', 'room_id')) {
                $table->unsignedBigInteger('room_id');
                $table->foreign('room_id')
                      ->references('id')
                      ->on('rooms')
                      ->onDelete('cascade'); // This will delete beds when room is deleted
            }

            // Add or modify patient_id (referencing users table)
            if (Schema::hasColumn('beds', 'patient_id')) {
                $table->dropForeign(['patient_id']);
                $table->dropColumn('patient_id');
            }
            
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // When user is deleted, bed becomes available
        });
    }

    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            // Remove foreign key constraints
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['room_id']);
            
            // Remove columns
            $table->dropColumn(['patient_id', 'room_id']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nurse_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurse_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->enum('shift', ['morning', 'afternoon', 'night']);
            $table->enum('status', ['scheduled', 'completed', 'absent', 'cancelled'])
                  ->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nurse_schedules');
    }
};

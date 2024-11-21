<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('beds')) {
            Schema::create('beds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained()->onDelete('cascade');
                $table->integer('bed_number');
                $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['room_id', 'bed_number']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('beds');
    }
};
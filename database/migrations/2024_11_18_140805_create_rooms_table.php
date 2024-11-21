<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('room_number')->unique();
                $table->integer('floor');
                $table->integer('total_beds');
                $table->enum('type', ['ward', 'private', 'icu']);
                $table->enum('status', ['available', 'maintenance', 'closed'])->default('available');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
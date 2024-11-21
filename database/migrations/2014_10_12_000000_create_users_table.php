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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('staff_id')->nullable(); // Make staff_id nullable
            $table->string('gender');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('ic_number')->nullable(); // Make ic_number nullable
            $table->string('address')->nullable(); // Make address nullable
            $table->string('blood_type')->nullable(); // Make blood_type nullable
            $table->string('contact_number')->nullable(); // Make contact_number nullable
            $table->text('medical_history')->nullable(); // Make medical_history nullable
            $table->text('description')->nullable(); // Make description nullable
            $table->string('emergency_contact')->nullable(); // Make emergency_contact nullable
            $table->string('relation')->nullable(); // Make relation nullable
            $table->string('profile_picture')->default('images/profile.png'); // Add profile_picture column with default value
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};

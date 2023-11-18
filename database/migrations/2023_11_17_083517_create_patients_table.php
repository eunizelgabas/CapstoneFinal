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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middlename')->nullable();
            $table->string('ext_name')->nullable();
            $table->string('email')->unique();
            $table->string('contact_no');
            $table->string('emergency_contact');
            $table->string('sex');
            $table->string('address');
            $table->date('dob');
            $table->enum('type', ['Student', 'Teacher']);
            $table->boolean('status')->default(1)->comment("1-active, 0-inactive");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pat_id')->unsigned();
            $table->date('date');
            $table->time('time');
            $table->string('status', 20)->default('Pending');
            $table->bigInteger('doc_id')->unsigned();
            $table->bigInteger('service_id')->unsigned();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('doc_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('pat_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

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
        Schema::create('med_certs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pat_id')->unsigned();
            $table->bigInteger('doc_id')->unsigned();
            $table->date('date');
            $table->string('diagnosis');
            $table->integer('days');
            $table->timestamps();
            $table->foreign('pat_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doc_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_certs');
    }
};

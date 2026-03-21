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
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // evitar citas duplicadas para el mismo doc en el mismo bloque de tiempo
            $table->unique(['doctor_id', 'start_datetime', 'end_datetime']);
            
            $table->index(['patient_id']);
            $table->index(['doctor_id']);
            $table->index(['start_datetime']);
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

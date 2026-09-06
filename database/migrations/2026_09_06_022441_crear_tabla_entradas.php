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
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->longText('contenido');
            $table->string('portada')->nullable();
            $table->enum('tipo', ['entrada', 'cita'])->default('entrada');
            $table->enum('estado', ['borrador', 'publicada'])->default('borrador');
            $table->dateTime('publicada_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};

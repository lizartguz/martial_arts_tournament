<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de preferencias de columnas para descargas por usuario.
     */
    public function up(): void
    {
        Schema::create('user_download_column_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->json('selected_columns')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de preferencias de columnas para descargas.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_download_column_preferences');
    }
};

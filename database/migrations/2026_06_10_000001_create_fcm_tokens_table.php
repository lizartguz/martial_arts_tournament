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
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id()->comment('Identificador unico interno del token FCM');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Usuario propietario del token FCM');
            $table->string('token', 255)
                ->unique()
                ->comment('Token FCM unico generado por Firebase para el dispositivo o navegador');
            $table->string('platform', 20)
                ->comment('Plataforma logica del token: mobile o web');
            $table->string('device_identifier', 300)
                ->nullable()
                ->comment('Identificador del dispositivo reportado por la app o el navegador');
            $table->string('device_name', 150)
                ->nullable()
                ->comment('Nombre legible del dispositivo reportado por el cliente');
            $table->string('browser', 120)
                ->nullable()
                ->comment('Navegador usado cuando el token corresponde a Web Push');
            $table->string('delivery_platform', 20)
                ->default('unknown')
                ->comment('Canal concreto del token: android, ios, web o unknown');
            $table->timestamp('last_seen_at')
                ->nullable()
                ->comment('Ultima fecha y hora en la que el cliente reporto este token como vigente');
            $table->timestamp('last_sent_at')
                ->nullable()
                ->comment('Ultima fecha y hora en la que el backend intento enviar una notificacion a este token');
            $table->timestamp('last_error_at')
                ->nullable()
                ->comment('Ultima fecha y hora en la que Firebase devolvio un error para este token');
            $table->string('last_error_message', 500)
                ->nullable()
                ->comment('Ultimo mensaje de error registrado para este token');
            $table->timestamp('invalidated_at')
                ->nullable()
                ->comment('Fecha y hora en la que el token se marco como invalido u obsoleto');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Indica si el token sigue habilitado para futuros envios');
            $table->timestamps();

            $table->index(['user_id', 'platform', 'is_active'], 'fcm_tokens_user_platform_active_idx');
            $table->index(['delivery_platform', 'is_active'], 'fcm_tokens_delivery_platform_active_idx');
            $table->index('last_seen_at', 'fcm_tokens_last_seen_at_idx');
            $table->index('invalidated_at', 'fcm_tokens_invalidated_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};

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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('image_1', 255)->nullable();
            $table->string('image_2', 255)->nullable();
            $table->string('link', 300)->nullable();
            $table->string('type_user',15)->nullable()->comment('all, internal, external, group, individual');
            $table->string('type',15)->nullable()->comment('push, normal, both');
            $table->string('delivery_platform',20)->default('all')->comment('Destino del push: mobile, web o all');
            $table->json('metadata')->nullable();
            $table->datetime('reg_date')->nullable();
            $table->date('deadline')->nullable()->comment('Fecha de expiracion del aviso');
            $table->datetime('scheduled_at')->nullable()->comment('Fecha desde la cual el aviso es visible');
            $table->datetime('push_sent_at')->nullable()->comment('Fecha y hora en la que el push se envio a Firebase');
            $table->datetime('push_last_error_at')->nullable()->comment('Ultima fecha y hora en la que el envio push fallo');
            $table->string('push_last_error_message',500)->nullable()->comment('Ultimo error registrado al intentar enviar el push');
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

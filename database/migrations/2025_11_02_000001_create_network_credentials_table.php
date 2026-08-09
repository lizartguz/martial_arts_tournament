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
        Schema::create('network_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('ssid',100)->nullable()->comment('nombre de la red');
            $table->string('password',100)->nullable()->comment('contraseña de la red');
            $table->unsignedBigInteger('station_id')->nullable()->unique()->comment('Un solo registro de credencial por estación');
            $table->foreign('station_id')->references('id')->on('stations');
            $table->unsignedBigInteger('user_creator_id')->nullable();
            $table->foreign('user_creator_id')->references('id')->on('users');
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->tinyInteger('sent_to_device')->default(0)->comment('0:Sin actualización pendiente, 1:Admin activó la bandera - El Nodo debe obtener nuevas credenciales');
            $table->timestamps();
            $table->comment('credenciales de red');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_credentials');
    }
};

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
        Schema::create('network_credentials_history', function (Blueprint $table) {
            $table->id();
            $table->string('ssid',100)->nullable()->comment('nombre de la red');
            $table->string('password',100)->nullable()->comment('contraseña de la red');                    
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo'); 
            $table->unsignedBigInteger('network_credential_id')->nullable();
            $table->foreign('network_credential_id')->references('id')->on('network_credentials');           
            $table->unsignedBigInteger('user_creator_id')->nullable();
            $table->foreign('user_creator_id')->references('id')->on('users');
            $table->timestamps();
            $table->comment('historial de credenciales de red');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_credentials_history');
    }
};

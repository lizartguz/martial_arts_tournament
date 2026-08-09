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
        Schema::create('details_stations', function (Blueprint $table) {
            $table->id();
            $table->string('image1')->nullable()->comment('imagen 1 de la estacion');
            $table->string('image2')->nullable()->comment('imagen 2 de la estacion');
            $table->string('image3')->nullable()->comment('imagen 3 de la estacion');
            $table->string('brand')->nullable()->comment('marca de la estacion');
            $table->enum('power_supply', ['Photovoltaic', 'Electric'])->nullable()->comment('fuente de alimentacion de la estacion');
            $table->enum('reception_type', ['WiFi', 'Ethernet'])->nullable()->comment('tipo de recepcion de la estacion');
            $table->string('model')->nullable()->comment('modelo de la estacion');
            $table->unsignedBigInteger('station_model_id')->nullable()->comment('Modelo del equipo (FK a station_models)');
            $table->foreign('station_model_id')->references('id')->on('station_models')->onDelete('set null');
            $table->date('installation_date')->nullable()->comment('fecha de instalacion de la estacion');
            $table->decimal('elevation', 10, 2)->nullable()->comment('Elevacion msnm');
            $table->unsignedBigInteger('installer_user_id')->nullable()->comment('usuario que instalo/modifico la estacion');
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->foreign('installer_user_id')->references('id')->on('users')->onDelete('set null');
            $table->comment('detalles de la estacion');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details_stations');
    }
};

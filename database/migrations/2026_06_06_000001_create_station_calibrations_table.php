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
        Schema::create('station_calibrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_id')->comment('Estacion a la que aplica la calibracion');
            $table->string('variable_name', 60)->comment('Variable meteorologica, por ejemplo winddir o windspeed');
            $table->string('calibration_key', 100)->comment('Clave controlada que resuelve un metodo de calibracion en codigo');
            $table->json('calibration_params')->nullable()->comment('Parametros opcionales para la calibracion');
            $table->tinyInteger('is_active')->default(1)->comment('1=Activa, 0=Inactiva');
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->unique(['station_id', 'variable_name'], 'station_calibrations_station_variable_unique');
            $table->index(['station_id', 'is_active'], 'station_calibrations_station_active_index');
            $table->comment('Configuracion de calibraciones por estacion y variable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_calibrations');
    }
};

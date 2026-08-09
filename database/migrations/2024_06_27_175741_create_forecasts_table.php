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
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->datetime('reg_date')->nullable()->comment('Fecha y hora del pronóstico generado');
            $table->decimal('v10m')->nullable()->comment('Velocidad del viento a 10 metros (km/h)');
            $table->string('v10m_dir',50)->nullable()->comment('Dirección del viento a 10 metros (grados)');
            $table->decimal('v10m_max')->nullable()->comment('Velocidad máxima del viento a 10 metros (km/h)');
            $table->decimal('v850')->nullable()->comment('Velocidad del viento a 850 hPa (km/h)');
            $table->string('v850_dir',50)->nullable()->comment('Dirección del viento a 850 hPa (grados)');
            $table->decimal('prec')->nullable()->comment('Precipitación acumulada en la hora (mm)');
            $table->decimal('cape')->nullable()->comment('Energía potencial convectiva disponible (CAPE)');
            $table->decimal('li')->nullable()->comment('Índice Lifted Index');
            $table->decimal('dam')->nullable()->comment('Daño probable por eventos severos');
            $table->decimal('a850')->nullable()->comment('Altura geopotencial a 850 hPa');
            $table->decimal('a500')->nullable()->comment('Altura geopotencial a 500 hPa');
            $table->decimal('t2m')->nullable()->comment('Temperatura a 2 metros (°C)');
            $table->integer('hr2m')->nullable()->comment('Humedad relativa a 2 metros (%)');
            $table->integer('hr2m_max')->nullable()->comment('Humedad relativa máxima prevista (%)');
            $table->integer('hr2m_min')->nullable()->comment('Humedad relativa mínima prevista (%)');
            $table->decimal('t850')->nullable()->comment('Temperatura a 850 hPa (°C)');
            $table->decimal('t500')->nullable()->comment('Temperatura a 500 hPa (°C)');
            $table->decimal('baro')->nullable()->comment('Presión barométrica (hPa)');
            $table->integer('cloud')->nullable()->comment('Cobertura nubosa estimada (%)');
            $table->integer('snow')->nullable()->comment('Probabilidad o intensidad de nieve');
            $table->decimal('dp')->nullable()->comment('Temperatura de punto de rocío (°C)');
            $table->decimal('tmax')->nullable()->comment('Temperatura máxima diaria prevista (°C)');
            $table->decimal('tmin')->nullable()->comment('Temperatura mínima diaria prevista (°C)');
            $table->decimal('prec_total')->nullable()->comment('Precipitación total acumulada del día (mm)');
            $table->integer('sh_ts')->nullable()->comment('Indicador de chubascos o tormentas');
            $table->integer('icon')->nullable()->comment('Índice numérico para seleccionar iconografía meteorológica');
            $table->string('icon_image')->nullable()->comment('Ruta o nombre del recurso gráfico asociado al icono');
            $table->decimal('min_t2m')->nullable()->comment('Temperatura mínima a 2 metros registrada (°C)');
            $table->decimal('min_dp')->nullable()->comment('Mínimo de punto de rocío registrado (°C)');
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->foreign('station_id')->references('id')->on('stations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};

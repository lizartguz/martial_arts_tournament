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
        Schema::create('customize_sensor_station', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->unsignedBigInteger('sensor_id')->nullable();
            $table->unsignedBigInteger('stations_type_id')->nullable();
            $table->tinyInteger('is_enabled')->nullable()->comment('0:disabled, 1:enabled');
            $table->foreign('station_id')->references('id')->on('stations');
            $table->foreign('sensor_id')->references('id')->on('sensors');
            $table->foreign('stations_type_id')->references('id')->on('stations_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customize_sensor_station');
    }
};

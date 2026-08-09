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
        Schema::create('type_sensors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_type_id')->nullable();
            $table->unsignedBigInteger('sensor_id')->nullable();            
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');
            $table->foreign('station_type_id')->references('id')->on('stations_type');
            $table->foreign('sensor_id')->references('id')->on('sensors');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_sensors');
    }
};

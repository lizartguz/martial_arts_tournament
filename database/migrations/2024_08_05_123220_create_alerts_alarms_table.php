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
        Schema::create('alerts_alarms', function (Blueprint $table) {
            $table->id();
            $table->decimal('temp_max')->nullable(); 
            $table->decimal('temp_min')->nullable(); 
            $table->decimal('wind_max')->nullable(); 
            $table->decimal('rain_total')->nullable(); 
            $table->decimal('uv_max')->nullable();
            $table->decimal('solar_rad_max')->nullable();
            $table->string('drop_type',20)->nullable();
            $table->decimal('dt_max')->nullable();
            $table->decimal('dt_min')->nullable();
            $table->string('type',15)->nullable()->comment('alert, alarm');   
            $table->decimal('dewpoint')->nullable();
            $table->tinyInteger('notification_status')->nullable()->comment('0:Inactivo, 1:Activo');                   
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->foreign('station_id')->references('id')->on('stations');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts_alarms');
    }
};

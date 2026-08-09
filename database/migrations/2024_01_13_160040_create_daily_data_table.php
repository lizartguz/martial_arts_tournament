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
        Schema::create('daily_data', function (Blueprint $table) {
            $table->id();
            $table->date('registration_date')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('day');
            $table->decimal('tempin')->nullable();           
            $table->decimal('tempout')->nullable();
            $table->decimal('tempoutmin')->nullable();
            $table->decimal('tempoutmax')->nullable();
            $table->integer('total_tempout')->nullable();
            
            $table->decimal('humin')->nullable();
            $table->decimal('humout')->nullable();
            $table->decimal('humoutmin')->nullable();
            $table->decimal('humoutmax')->nullable();
            $table->integer('total_humout')->nullable();

            $table->decimal('dewptout')->nullable();
            $table->decimal('dewptin')->nullable();
            $table->decimal('heatindexout')->nullable();   
            
            $table->decimal('press')->nullable();
            $table->decimal('seapress')->nullable();

            $table->decimal('windchill')->nullable();
            $table->decimal('windavg')->nullable();
            $table->decimal('windspeed')->nullable();            
            $table->decimal('winddir')->nullable();           
            $table->decimal('windspeedhi')->nullable();
            $table->decimal('winddirhi')->nullable();
            $table->string('winddirstr',10)->nullable();

            $table->decimal('windspeed2')->nullable();            
            $table->decimal('winddir2')->nullable();
            $table->decimal('windspeedhi2')->nullable();
            $table->decimal('winddirhi2')->nullable();
            $table->string('winddirstr2',10)->nullable();

            $table->decimal('rainrate')->nullable();
            $table->decimal('raintotal')->nullable();
            $table->integer('total_rainrate')->nullable();

            $table->decimal('uvindex')->nullable();
            $table->decimal('solrad')->nullable();
            $table->decimal('solradhi')->nullable();
            $table->decimal('solevo')->nullable();

            $table->decimal('soiltemp1')->nullable();
            $table->decimal('soiltemp2')->nullable();
            $table->decimal('soiltemp3')->nullable();
            $table->decimal('soiltemp4')->nullable();

            $table->decimal('soilhum1')->nullable();
            $table->decimal('soilhum2')->nullable();
            $table->decimal('soilhum3')->nullable();
            $table->decimal('soilhum4')->nullable();
            
            $table->decimal('leaftemp1')->nullable();
            $table->decimal('leaftemp2')->nullable();
            $table->decimal('leaftemp3')->nullable();
            $table->decimal('leaftemp4')->nullable();

            $table->decimal('leafhum1')->nullable(); 
            $table->decimal('leafhum2')->nullable();
            $table->decimal('leafhum3')->nullable();
            $table->decimal('leafhum4')->nullable();
            
            $table->integer('total_leafhum1')->nullable(); 
            $table->integer('total_leafhum2')->nullable();
            $table->integer('total_leafhum3')->nullable();
            $table->integer('total_leafhum4')->nullable();

            $table->decimal('pm10')->nullable();
            $table->decimal('pm2_5')->nullable();
            $table->decimal('pm1')->nullable();

            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');
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
        Schema::dropIfExists('daily_data');
    }
};

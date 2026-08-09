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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();            
            $table->string('description',255)->nullable(); 
            $table->date('reg_date')->nullable(); 
            $table->tinyInteger('f')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();               
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');
            $table->foreign('station_id')->references('id')->on('stations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};

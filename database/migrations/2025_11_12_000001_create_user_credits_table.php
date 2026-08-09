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
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['forecast'])->nullable()->comment('Tipo de crédito: forecast');
            $table->integer('used_credit')->nullable()->comment('Cantidad de créditos usados');
            $table->integer('total_credit')->nullable()->comment('Cantidad total de créditos'); 
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->comment('Tabla de créditos de los usuarios, se reinicia el primero de cada mes mediante cronjob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credits');
    }
};

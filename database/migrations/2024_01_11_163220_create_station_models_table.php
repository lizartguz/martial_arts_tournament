<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->comment('Nombre del modelo');
            $table->tinyInteger('state')->nullable()->comment('1:Activo, 0:Inactivo');
            $table->unsignedBigInteger('brand_id')->comment('Marca a la que pertenece el modelo');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->unique(['brand_id', 'name']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_models');
    }
};

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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->uuid('code')->unique()->comment('Código UUID único de la estación');
            $table->string('name', 60)->nullable()->comment('nombre de la estacion');
            $table->string('location', 130)->nullable()->comment('combinacion de municipio, provincia y departamento, ej: San Miguel de Tucuman, San Miguel de Tucuman, Tucuman');
            $table->string('address', 130)->nullable()->comment('direccion de la estacion, com osewer ,calles nazano, avenida 123, etc.');
            $table->string('search_concatenation', 600)->nullable()->comment('Se ocupara para la busqueda por nombres de: estacion, localidad[concatenacion de: municipio,departamento] y direccion en conjunto');        
            $table->string('reg_date', 60)->nullable()->comment('fecha de registro de la estacion');
            $table->string('latitude', 30)->nullable()->comment('latitud de la estacion');
            $table->string('longitude', 30)->nullable()->comment('longitud de la estacion');     
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');
            $table->tinyInteger('et')->nullable()->default(0)->comment('evapotranspiracion: 1=Activo, 0=Inactivo');
            $table->unsignedBigInteger('user_creator_id')->nullable()->comment('usuario que creo la estacion');
            $table->unsignedBigInteger('modifier_user_id')->nullable()->comment('usuario que modifico la estacion');
            $table->unsignedBigInteger('municipality_id')->nullable()->comment('municipio donde se ubica la estacion');
            $table->foreign('user_creator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modifier_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('municipality_id')->references('id')->on('municipalities')->onDelete('set null');
            $table->comment('estaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};

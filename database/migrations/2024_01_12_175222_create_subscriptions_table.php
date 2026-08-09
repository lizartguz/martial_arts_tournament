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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('Nombre de la suscripcion');
            $table->string('tag')->nullable()->comment('Etiqueta identificativa de la suscripcion para usarla como validador');
            $table->decimal('price')->nullable()->comment('Precio de la suscripcion');
            $table->integer('amount')->nullable()->comment('Cantidad de usuarios habilitados para esta suscripción');
            $table->integer('duration')->nullable()->comment('Duracion de en años del valor de la ssucripcion');
            $table->decimal('duration_value_discount')->nullable()->comment('Descuento por duracion de susccripcion');
            $table->decimal('duration_discount')->nullable()->comment('Cantidaad de años a partir de la cua lse aplicara el descuento');
            $table->decimal('discount_value_stations')->nullable()->comment('Valor del porcentaje de descuento por numero estaciones adquiridas');
            $table->integer('number_stations_discount')->nullable()->comment('Número de estaciones, en la cual, a partir de estas se aplicará el descuento');
            $table->tinyInteger('state_de')->nullable()->comment('Estado para el descuento por estaciones  0:Inactivo, 1:Activo');
            $table->tinyInteger('state_dd')->nullable()->comment('Estado para el descuento por duración 0:Inactivo, 1:Activo');
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

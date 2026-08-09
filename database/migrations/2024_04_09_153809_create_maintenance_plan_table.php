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

        Schema::create('maintenance_plan', function (Blueprint $table) {
            $table->id();
            $table->date('maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->string('delivery_note_number',30)->nullable();
            $table->string('work_order_number',30)->nullable();
            $table->string('preventive_maintenance',3)->nullable();
            $table->string('corrective_maintenance',3)->nullable();
            $table->string('description',800)->nullable();
            $table->string('recommendations',800)->nullable();
            $table->string('image_before')->nullable()->comment('Imagen de la estación antes del mantenimiento'); 
            $table->string('image_after')->nullable()->comment('Imagen de la estación despues del mantenimiento');
            $table->string('image_signature')->nullable()->comment('Imagen de la firma del cliente aprobando el mantenimiento');
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo , 1:Activo');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Registrado por');
            $table->unsignedBigInteger('modified_by_user_id')->nullable()->comment('Modificado por');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('modified_by_user_id')->references('id')->on('users');
            $table->foreign('station_id')->references('id')->on('stations');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_plan');
    }
};

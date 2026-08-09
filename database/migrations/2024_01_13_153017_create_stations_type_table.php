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
        Schema::create('stations_type', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->nullable();  
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');               
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations_type');
    }
};

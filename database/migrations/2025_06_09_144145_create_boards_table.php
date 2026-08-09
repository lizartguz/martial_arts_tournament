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
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('nombe de la persona o institucion');
            $table->string('link',500)->nullable();
            $table->tinyInteger('total_views')->nullable();
            $table->tinyInteger('busy_views')->nullable();                    
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');            
            $table->unsignedBigInteger('user_creator_id')->nullable();
            $table->foreign('user_creator_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};

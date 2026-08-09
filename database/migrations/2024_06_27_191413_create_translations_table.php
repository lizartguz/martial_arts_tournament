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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->datetime('reg_date')->nullable();
            $table->string('description',500)->nullable();
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('station_id')->references('id')->on('stations');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};

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
        Schema::create('subowner_dependence', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_owner_id')->nullable();
            $table->unsignedBigInteger('user_subowner_id')->nullable();
            $table->tinyInteger('state')->nullable()->comment('1:Activo , 0:Inactivo');            
            $table->foreign('user_owner_id')->references('id')->on('users');
            $table->foreign('user_subowner_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subowner_dependence');
    }
};

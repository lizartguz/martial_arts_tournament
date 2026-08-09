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
        Schema::create('customer_service', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->nullable();
            $table->string('lastname',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('company',80)->nullable();
            $table->string('number_phone',15)->nullable();
            $table->string('location',255)->nullable();
            $table->string('country',50)->nullable();
            $table->string('city',100)->nullable();
            $table->string('title',200)->nullable();
            $table->string('description',700)->nullable();
            $table->tinyInteger('type')->nullable()->comment('1:Customer service, 2:Contact');
            $table->tinyInteger('state')->nullable()->comment('0:inactivo, 1:activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_service');
    }
};

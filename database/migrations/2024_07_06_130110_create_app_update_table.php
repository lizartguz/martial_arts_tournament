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
        Schema::create('app_update', function (Blueprint $table) {
            $table->id();
            $table->string('update_message_android',100)->nullable(); 
            $table->string('update_message_ios',100)->nullable(); 
            $table->string('block_android',100)->nullable(); 
            $table->string('block_ios',100)->nullable();
            $table->string('current_version_android_text',30)->nullable();
            $table->string('current_version_ios_text',30)->nullable();
            $table->string('new_version_android_text',30)->nullable();
            $table->string('new_version_ios_text',30)->nullable();
            $table->string('link_android',300)->nullable();
            $table->string('link_ios',300)->nullable();           
            $table->tinyInteger('state')->nullable()->comment('0:Inactivo, 1:Activo');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_update');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica los cambios definidos por la migración.
     */
    public function up(): void
    { 
        /**
         * Define la estructura de la tabla users.
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->nullable()->unique();
            $table->string('name', 100);
            $table->string('lastname', 100)->nullable();
            $table->string('email', 150)->unique();
            $table->string('number_phone', 30)->nullable();
            $table->string('identity_document', 40)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->rememberToken();
            $table->string('device_identifier', 300)->nullable();
            $table->string('version_app', 30)->nullable();
            $table->tinyInteger('state')->default(1)->comment('0 bloqueado/inactivo, 1 activo');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('state');
            $table->index('number_phone');
        });
    }

    /**
     * Revierte los cambios definidos por la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

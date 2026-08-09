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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username',100)->nullable()->comment('version artguzClima 2');
            $table->string('name',100)->nullable();
            $table->string('lastname',100)->nullable();
            $table->string('email',100)->nullable()->unique();
            $table->string('occupation',50)->nullable();
            $table->string('ci',30)->nullable();
            //$table->string('issue|d',3)->nullable()->comment('expedido');
            $table->string('number_phone',15)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('image')->nullable();
            $table->rememberToken();
            $table->string('device_identifier',300)->nullable();
            $table->string('pin',8)->nullable();
            $table->string('version_app',30)->nullable();
            $table->string('is_update',5)->nullable();
            //$table->foreignId('current_team_id')->nullable();
            //$table->string('profile_photo_path', 2048)->nullable();
            $table->unsignedBigInteger('user_dependency_id')->nullable();
            $table->tinyInteger('access_type')->nullable()->comment('0:interno, 1:externo');
            $table->tinyInteger('user_type')->nullable()->comment('1:owner (pay), 2:sub owner 3:dependency user to owner o subowner');
            $table->tinyInteger('payment_state')->nullable()->comment(' 0:pendiente de pago, 1:pagado');
            $table->tinyInteger('own_state')->nullable()->comment('Estado que puede cambiar dependiendo de la expiracion o prorroga, 0:inactivo, 1:activo');
            $table->tinyInteger('state')->nullable()->comment('Es un estado personal o propio del usuario, 0:inactivo, 1:activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

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
        Schema::create('user_dependency', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dependent_user_id')->nullable();
            $table->unsignedBigInteger('user_subscription_id')->nullable();           
            $table->foreign('dependent_user_id')->references('id')->on('users');
            $table->foreign('user_subscription_id')->references('id')->on('users_subscriptions');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dependency');
    }
};

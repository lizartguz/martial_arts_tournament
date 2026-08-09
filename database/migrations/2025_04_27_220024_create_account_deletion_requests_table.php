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
        Schema::create('account_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('user who requested account deletion');
            $table->string('reason')->nullable()->comment('reason for account deletion');
            $table->tinyInteger('state')->comment('status of the request (0:pending, 1:approved, 2:rejected)'); 
            $table->unsignedBigInteger('validator_user_id')->nullable()->comment('user who validated the request');
            $table->string('validation_comment')->nullable()->comment('comment from the validator');           
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('validator_user_id')->references('id')->on('users'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_deletion_requests');
    }
};

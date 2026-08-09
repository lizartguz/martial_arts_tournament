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
        Schema::create('users_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('business_name',100)->nullable();
            $table->string('nit',20)->nullable();
            $table->date('reg_date')->nullable()->comment('fecha de registro de transacción');
            $table->date('accumulation_date')->nullable()->comment('fecha de renovacion en caso que la suscripcion no haya caducado, inicia desde la compra no caducada');
            $table->date('start_date')->nullable();
            $table->date('expiration_notice')->nullable();
            $table->date('date_expiry')->nullable();
            $table->integer('added_user_count')->nullable();
            $table->decimal('added_user_total_cost')->nullable();
            $table->integer('amount')->nullable()->comment('cantidad de suscripciones compradas por año');
            $table->boolean('is_discount')->nullable();
            $table->decimal('cost')->nullable()->comment('costo de la suscripción, lleva tambien el acomulado de renovaciones');
            $table->string('latitude',70)->nullable();
            $table->string('longitude',70)->nullable();
            $table->json('invitation_reference_data')->nullable()->comment('datos almacenados sobre descuento de invitacion a otros usuarios');
            $table->date('temporary_extension_start')->nullable()->comment('fecha de prorroga despues de la expiracion inicial');
            $table->date('temporary_extension_end')->nullable()->comment('fecha de prorroga  final');
            $table->json('incident_extension')->nullable()->comment('datos como % descuentos,fechai, fechaF, monto, y motivo por la cual se aplica');
            $table->json('data_save')->nullable()->comment('almacena los datos de la suscripción elegida (BackUp)');
            $table->tinyInteger('is_it_paid_user')->nullable()->comment('0:pendiente 1:pagado');
            $table->tinyInteger('payment_type_user')->nullable()->comment('0:ninguno 1:efectivo 2:tarjeta 3:Qr 4:cortesia(sin pagar)');
            $table->tinyInteger('is_it_paid_renewal')->nullable()->comment('0:pendiente 1:pagado');
            $table->tinyInteger('payment_type_renewal')->nullable()->comment('0:ninguno 1:efectivo 2:tarjeta 3:Qr 4:cortesia(sin pagar)');
            $table->tinyInteger('state')->nullable()->comment('0:suscripcion inactiva, 1:suscripción activa');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('station_id')->references('id')->on('stations');
            $table->string('payment_link_renewal',300)->nullable();
            $table->string('payment_link_user',300)->nullable();
            $table->string('transaction_renewal_id',255)->nullable();
            $table->string('transaction_user_id',255)->nullable();
            $table->timestamps();

         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_subscriptions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 160)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('BOB');
            $table->string('billing_period', 30)->default('monthly');
            $table->unsignedInteger('duration_days')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->json('features')->nullable();
            $table->json('limits')->nullable();
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'display_order']);
        });

        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('renewal_at')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 pendiente, 1 activa, 2 vencida, 3 cancelada, 4 suspendida');
            $table->string('source', 40)->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['subscription_plan_id', 'status']);
            $table->index('ends_at');
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BOB');
            $table->string('payment_method', 40);
            $table->string('provider', 80)->nullable();
            $table->string('provider_transaction_id', 180)->nullable();
            $table->string('payment_url', 500)->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_proof_mime', 100)->nullable();
            $table->unsignedInteger('payment_proof_size')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 pendiente, 1 pagado, 2 fallido, 3 reembolsado, 4 vencido');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_subscription_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('provider_transaction_id');
        });

        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name', 150);
            $table->string('contact_email', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('contact_whatsapp', 30)->nullable();
            $table->string('preferred_channel', 30)->default('whatsapp');
            $table->string('request_type', 40)->default('general_contact');
            $table->text('message')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_proof_mime', 100)->nullable();
            $table->unsignedInteger('payment_proof_size')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 pendiente, 1 en revision, 2 contactado, 3 convertido, 4 cerrado, 5 rechazado');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('handled_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('request_type');
            $table->index('event_id');
            $table->index('subscription_plan_id');
            $table->index('assigned_to');
            $table->index('created_at');
        });

        Schema::create('subscription_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->string('feature_key', 100)->nullable();
            $table->string('value', 120)->nullable();
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['subscription_plan_id', 'status']);
        });

        Schema::create('ticket_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('provider_name', 120);
            $table->string('label', 120);
            $table->string('sale_channel', 40);
            $table->string('url', 500);
            $table->decimal('price_from', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_links');
        Schema::dropIfExists('subscription_plan_features');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};

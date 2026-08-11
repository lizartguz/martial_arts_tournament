<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 180);
            $table->string('slug', 220)->unique();
            $table->string('subtitle', 200)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('doors_open_at')->nullable();
            $table->string('timezone', 60)->nullable();
            $table->string('poster_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('stream_url', 500)->nullable();
            $table->string('ticket_url', 500)->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 borrador, 1 publicado, 2 archivado, 3 cancelado');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'starts_at']);
            $table->index(['is_featured', 'status']);
        });

        Schema::create('fights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('weight_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('corner_red_fighter_id')->nullable()->constrained('fighters')->nullOnDelete();
            $table->foreignId('corner_blue_fighter_id')->nullable()->constrained('fighters')->nullOnDelete();
            $table->string('title', 160)->nullable();
            $table->string('bout_type', 40)->default('regular');
            $table->unsignedTinyInteger('rounds')->default(3);
            $table->unsignedInteger('display_order')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->string('promo_image')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 programado, 1 en vivo, 2 finalizado, 3 cancelado');
            $table->boolean('is_main_event')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'display_order']);
            $table->index(['weight_class_id', 'status']);
            $table->index(['status', 'starts_at']);
        });

        Schema::create('fight_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fight_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('winner_fighter_id')->nullable()->constrained('fighters')->nullOnDelete();
            $table->string('result_type', 40);
            $table->string('method', 80)->nullable();
            $table->unsignedTinyInteger('round')->nullable();
            $table->string('time', 10)->nullable();
            $table->text('official_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type', 30)->default('image');
            $table->string('category', 50)->nullable();
            $table->string('title', 180)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['event_id', 'category', 'status']);
            $table->index(['event_id', 'is_featured']);
        });

        Schema::create('fighter_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fighter_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_type', 30)->default('image');
            $table->string('title', 180)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fighter_id', 'status']);
            $table->index(['fighter_id', 'is_featured']);
        });

        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('excerpt', 300)->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'status']);
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->string('slug', 220)->unique();
            $table->string('logo_path')->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('event_sponsor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sponsor_id')->constrained()->cascadeOnDelete();
            $table->string('placement', 80)->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'sponsor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sponsor');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('news_posts');
        Schema::dropIfExists('fighter_media');
        Schema::dropIfExists('event_media');
        Schema::dropIfExists('fight_results');
        Schema::dropIfExists('fights');
        Schema::dropIfExists('events');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 150)->default('Combate Real');
            $table->string('public_title', 180)->default('Combate Real');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('default_image')->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('whatsapp_phone', 30)->nullable();
            $table->json('social_links')->nullable();
            $table->text('short_description')->nullable();
            $table->string('seo_title', 180)->nullable();
            $table->string('seo_description', 300)->nullable();
            $table->boolean('landing_show_rankings')->default(false);
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('iso2', 2)->nullable()->unique();
            $table->string('iso3', 3)->nullable()->unique();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->unique(['country_id', 'name']);
            $table->index(['country_id', 'status']);
        });

        Schema::create('weight_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 160)->unique();
            $table->string('gender', 20)->default('mixed');
            $table->decimal('min_weight_kg', 6, 2)->nullable();
            $table->decimal('max_weight_kg', 6, 2)->nullable();
            $table->decimal('max_weight_lb', 6, 2)->nullable();
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['gender', 'status']);
            $table->index(['display_order', 'status']);
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->string('slug', 220)->unique();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('contact_name', 120)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
        });

        Schema::create('fighter_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->string('slug', 220)->unique();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('coach_name', 120)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
        });

        Schema::create('fighters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('nickname', 120)->nullable();
            $table->string('slug', 220)->unique();
            $table->string('gender', 20);
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fighter_team_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('weight_class_id')->nullable()->constrained()->nullOnDelete();
            $table->date('birthdate')->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('reach_cm', 5, 2)->nullable();
            $table->string('stance', 50)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->unsignedInteger('draws')->default(0);
            $table->unsignedInteger('no_contests')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['gender', 'status']);
            $table->index(['weight_class_id', 'status']);
            $table->index(['fighter_team_id', 'status']);
        });

        Schema::create('fighter_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fighter_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('knockout_wins')->default(0);
            $table->unsignedInteger('submission_wins')->default(0);
            $table->unsignedInteger('decision_wins')->default(0);
            $table->unsignedInteger('knockout_losses')->default(0);
            $table->unsignedInteger('submission_losses')->default(0);
            $table->unsignedInteger('decision_losses')->default(0);
            $table->integer('current_streak')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('fighter_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weight_class_id')->constrained()->cascadeOnDelete();
            $table->string('gender', 20);
            $table->foreignId('fighter_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->unsignedInteger('previous_position')->nullable();
            $table->boolean('is_champion')->default(false);
            $table->date('ranked_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['weight_class_id', 'gender', 'fighter_id']);
            $table->index(['weight_class_id', 'gender', 'status', 'position'], 'rankings_class_gender_status_position_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fighter_rankings');
        Schema::dropIfExists('fighter_stats');
        Schema::dropIfExists('fighters');
        Schema::dropIfExists('fighter_teams');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('weight_classes');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('system_settings');
    }
};

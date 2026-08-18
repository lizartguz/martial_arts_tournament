<?php

namespace Tests\Feature\Services;

use App\Services\SlugGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Fase 7, punto 5 (complemento): amplio pero superficial, a nivel de servicio,
 * cubre las 7 tablas con slug sin levantar 7 componentes Livewire.
 */
class SlugGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fila mínima por tabla que satisface sus columnas NOT NULL, además de 'slug'.
     */
    public static function slugTablesProvider(): array
    {
        return [
            'events' => ['events', ['uuid' => (string) Str::uuid(), 'name' => 'Demo', 'starts_at' => now()]],
            'fighters' => ['fighters', ['uuid' => (string) Str::uuid(), 'first_name' => 'Demo', 'gender' => 'male']],
            'venues' => ['venues', ['name' => 'Demo']],
            'fighter_teams' => ['fighter_teams', ['name' => 'Demo']],
            'news_posts' => ['news_posts', ['uuid' => (string) Str::uuid(), 'title' => 'Demo']],
            'weight_classes' => ['weight_classes', ['name' => 'Demo']],
            'sponsors' => ['sponsors', ['name' => 'Demo']],
        ];
    }

    #[DataProvider('slugTablesProvider')]
    public function test_collision_gets_auto_suffixed_for_every_slug_bearing_table(string $table, array $extraColumns): void
    {
        DB::table($table)->insert(array_merge($extraColumns, [
            'slug' => 'demo',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $slug = app(SlugGeneratorService::class)->uniqueForTable($table, 'Demo');

        $this->assertSame('demo-2', $slug);
    }

    public function test_third_collision_increments_the_suffix_again(): void
    {
        DB::table('venues')->insert([
            ['name' => 'Demo', 'slug' => 'demo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Demo', 'slug' => 'demo-2', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $slug = app(SlugGeneratorService::class)->uniqueForTable('venues', 'Demo');

        $this->assertSame('demo-3', $slug);
    }

    public function test_ignore_id_allows_a_record_to_keep_its_own_slug(): void
    {
        $id = DB::table('venues')->insertGetId([
            'name' => 'Demo',
            'slug' => 'demo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $slug = app(SlugGeneratorService::class)->uniqueForTable('venues', 'Demo', 'slug', $id);

        $this->assertSame('demo', $slug);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\FighterMedia\FighterMediaTable;
use App\Models\Fighter;
use App\Models\FighterMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Mismo comportamiento que EventMediaTableTest, para la galería de
 * peleadores: el toggle de estado renombra el archivo; el resto de los
 * campos no.
 */
class FighterMediaTableTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->actingAsRole('admin');
        Storage::fake('local');
    }

    public function test_toggling_status_without_a_new_upload_renames_the_file(): void
    {
        $fighter = Fighter::factory()->create();
        $originalPath = 'mma/fighter-media/pieza_original.webp';
        Storage::disk('local')->put($originalPath, 'contenido');

        $media = FighterMedia::query()->create([
            'fighter_id' => $fighter->id,
            'file_path' => $originalPath,
            'file_type' => 'image',
            'status' => 1,
        ]);

        Livewire::test(FighterMediaTable::class)
            ->call('edit', $media->id)
            ->set('form.status', 0)
            ->call('save')
            ->assertHasNoErrors();

        $media->refresh();

        $this->assertNotSame($originalPath, $media->file_path);
        Storage::disk('local')->assertMissing($originalPath);
        Storage::disk('local')->assertExists($media->file_path);
        $this->assertSame('contenido', Storage::disk('local')->get($media->file_path));
    }

    public function test_saving_without_changing_status_keeps_the_same_file_path(): void
    {
        $fighter = Fighter::factory()->create();
        $originalPath = 'mma/fighter-media/pieza_original.webp';
        Storage::disk('local')->put($originalPath, 'contenido');

        $media = FighterMedia::query()->create([
            'fighter_id' => $fighter->id,
            'file_path' => $originalPath,
            'file_type' => 'image',
            'status' => 1,
            'title' => 'Antes',
        ]);

        Livewire::test(FighterMediaTable::class)
            ->call('edit', $media->id)
            ->set('form.title', 'Después')
            ->call('save')
            ->assertHasNoErrors();

        $media->refresh();

        $this->assertSame($originalPath, $media->file_path);
        Storage::disk('local')->assertExists($originalPath);
        $this->assertSame('Después', $media->title);
    }
}

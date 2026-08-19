<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\EventMedia\EventMediaTable;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Cambiar el estado (Activo/Inactivo) de una pieza de galería, sin subir un
 * archivo nuevo, renombra el archivo en disco y actualiza `file_path`.
 *
 * La URL vieja queda huérfana de inmediato: no depende de que expire un
 * caché para que el cambio de visibilidad se refleje. Ver GalleryMediaController.
 */
class EventMediaTableTest extends TestCase
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
        $event = Event::factory()->create();
        $originalPath = 'mma/event-media/pieza_original.webp';
        Storage::disk('local')->put($originalPath, 'contenido');

        $media = EventMedia::query()->create([
            'event_id' => $event->id,
            'file_path' => $originalPath,
            'file_type' => 'image',
            'status' => 1,
        ]);

        Livewire::test(EventMediaTable::class)
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
        $event = Event::factory()->create();
        $originalPath = 'mma/event-media/pieza_original.webp';
        Storage::disk('local')->put($originalPath, 'contenido');

        $media = EventMedia::query()->create([
            'event_id' => $event->id,
            'file_path' => $originalPath,
            'file_type' => 'image',
            'status' => 1,
            'title' => 'Antes',
        ]);

        Livewire::test(EventMediaTable::class)
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

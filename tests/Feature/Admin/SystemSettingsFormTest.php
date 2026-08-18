<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SystemSettings\SystemSettingForm;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * El aviso de éxito debe distinguir si las imágenes se reemplazaron o no:
 * antes se mostraba "guardado con éxito" siempre, incluso cuando el archivo
 * nunca llegó al servidor, lo que hacía imposible detectar una subida fallida.
 */
class SystemSettingsFormTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->actingAsRole('super_manager');
        // Las imagenes administradas viven en el disco privado desde la
        // migracion a /media/{path}; `public` se falsea solo por el legado.
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_saving_with_a_new_logo_confirms_the_image_was_replaced(): void
    {
        Livewire::test(SystemSettingForm::class)
            ->set('logoImage', UploadedFile::fake()->image('nuevo-logo.png', 400, 400))
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('successAlert', function (string $event, array $params) {
                return $params[0]['success'] === __('mma.admin.system_settings.messages.updated_with_images');
            });

        $this->assertStringContainsString('system-logo', (string) SystemSetting::query()->first()->logo_path);
    }

    public function test_saving_without_a_new_logo_does_not_claim_images_changed(): void
    {
        Livewire::test(SystemSettingForm::class)
            ->set('form.product_name', 'Combate Real Bolivia')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('successAlert', function (string $event, array $params) {
                return $params[0]['success'] === __('mma.admin.system_settings.messages.updated');
            });
    }

    public function test_uploaded_png_logo_keeps_the_png_format(): void
    {
        Livewire::test(SystemSettingForm::class)
            ->set('logoImage', UploadedFile::fake()->image('logo.png', 300, 300))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStringEndsWith('.png', (string) SystemSetting::query()->first()->logo_path);
    }

    public function test_replacing_a_logo_deletes_the_previous_file(): void
    {
        $component = Livewire::test(SystemSettingForm::class)
            ->set('logoImage', UploadedFile::fake()->image('primero.png', 200, 200))
            ->call('save');

        $firstPath = (string) SystemSetting::query()->first()->logo_path;

        // La ruta guardada ya no lleva el prefijo `storage/`: es relativa al
        // disco privado, y solo se resuelve a URL por App\Support\PublicMedia.
        $this->assertStringStartsWith('mma/system/', $firstPath);
        Storage::disk('local')->assertExists($firstPath);

        $component
            ->set('logoImage', UploadedFile::fake()->image('segundo.png', 200, 200))
            ->call('save');

        Storage::disk('local')->assertMissing($firstPath);
    }
}

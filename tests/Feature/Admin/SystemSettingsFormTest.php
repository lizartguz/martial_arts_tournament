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

        $firstPath = str(SystemSetting::query()->first()->logo_path)->after('storage/')->toString();
        Storage::disk('public')->assertExists($firstPath);

        $component
            ->set('logoImage', UploadedFile::fake()->image('segundo.png', 200, 200))
            ->call('save');

        Storage::disk('public')->assertMissing($firstPath);
    }
}

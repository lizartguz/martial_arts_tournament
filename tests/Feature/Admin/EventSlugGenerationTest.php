<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Events\EventTable;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Fase 7, punto 5: slugs duplicados e inválidos (paso completo en un modelo
 * representativo, Event: validación, persistencia y ruta pública por slug).
 */
class EventSlugGenerationTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->actingAsRole('admin');
    }

    public function test_duplicate_name_gets_auto_suffixed_slug_on_save(): void
    {
        Livewire::test(EventTable::class)
            ->call('create')
            ->set('form.name', 'UFC Night')
            ->set('form.starts_at', now()->addWeek()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(EventTable::class)
            ->call('create')
            ->set('form.name', 'UFC Night')
            ->set('form.starts_at', now()->addWeeks(2)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('events', ['name' => 'UFC Night', 'slug' => 'ufc-night']);
        $this->assertDatabaseHas('events', ['name' => 'UFC Night', 'slug' => 'ufc-night-2']);
    }

    public function test_manually_entered_duplicate_slug_is_rejected(): void
    {
        Event::factory()->create(['name' => 'UFC Night', 'slug' => 'ufc-night']);

        Livewire::test(EventTable::class)
            ->call('create')
            ->set('form.name', 'Otro Evento')
            ->set('form.slug', 'ufc-night')
            ->set('form.starts_at', now()->addWeek()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasErrors(['form.slug']);
    }

    /**
     * updatedFormSlug() sanea con Str::slug() cualquier valor apenas se escribe
     * en el campo, antes de que la regla regex de rules() pueda evaluarlo. Por
     * diseño del formulario, un usuario no puede hacer llegar un slug con formato
     * inválido a save() escribiéndolo directamente: siempre se autocorrige.
     */
    public function test_slug_field_is_auto_sanitized_as_the_user_types_it(): void
    {
        $component = Livewire::test(EventTable::class)
            ->call('create')
            ->set('form.name', 'Evento de prueba')
            ->set('form.slug', 'UFC Night!');

        $component->assertSet('form.slug', 'ufc-night');

        $component
            ->set('form.starts_at', now()->addWeek()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('events', ['name' => 'Evento de prueba', 'slug' => 'ufc-night']);
    }

    public function test_public_route_resolves_by_slug_and_404s_on_unknown_slug(): void
    {
        Event::factory()->create(['name' => 'UFC Night', 'slug' => 'ufc-night', 'status' => 1]);

        $this->get(route('landing.events.show', 'ufc-night'))->assertOk();
        $this->get(route('landing.events.show', 'no-existe'))->assertNotFound();
    }
}

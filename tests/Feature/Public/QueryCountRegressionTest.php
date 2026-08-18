<?php

namespace Tests\Feature\Public;

use App\Livewire\Admin\Events\EventTable;
use App\Models\Event;
use App\Models\Fight;
use App\Models\Fighter;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CountsQueries;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Fase 7, punto 7: revisión de N+1.
 *
 * Estilo diferencial: se compara el conteo de queries con pocos registros vs.
 * con varios; el test falla solo si el conteo crece proporcionalmente al
 * crecimiento de filas (indicio real de N+1), no contra un número fijo.
 *
 * La home pública (GET /) queda fuera de esta suite: su consulta usa
 * ORDER BY ABS(TIMESTAMPDIFF(...)), una función específica de MySQL que no
 * existe en sqlite (motor usado por esta suite de tests) y rompe con
 * "no such function: TIMESTAMPDIFF" antes de poder medir nada.
 */
class QueryCountRegressionTest extends TestCase
{
    use CountsQueries;
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_admin_events_index_query_count_does_not_grow_with_more_events(): void
    {
        $this->actingAsRole('admin');
        $venue = Venue::factory()->create();

        Event::factory()->count(2)->create(['venue_id' => $venue->id]);
        $fewQueries = $this->countQueriesDuring(fn () => Livewire::test(EventTable::class));

        Event::factory()->count(10)->create(['venue_id' => $venue->id]);
        $manyQueries = $this->countQueriesDuring(fn () => Livewire::test(EventTable::class));

        $this->assertLessThanOrEqual(
            $fewQueries + 2,
            $manyQueries,
            'El conteo de queries del índice admin de eventos creció junto con la cantidad de filas: posible N+1.'
        );
    }

    public function test_event_detail_query_count_is_bounded_regardless_of_fight_card_size(): void
    {
        $smallEvent = Event::factory()->create(['status' => 1]);
        $this->seedFights($smallEvent, 2);

        $fewQueries = $this->countQueriesDuring(
            fn () => $this->get(route('landing.events.show', $smallEvent->slug))
        );

        $bigEvent = Event::factory()->create(['status' => 1]);
        $this->seedFights($bigEvent, 10);

        $manyQueries = $this->countQueriesDuring(
            fn () => $this->get(route('landing.events.show', $bigEvent->slug))
        );

        $this->assertLessThanOrEqual(
            $fewQueries + 2,
            $manyQueries,
            'El conteo de queries del detalle público de evento creció junto con la cantidad de combates: posible N+1.'
        );
    }

    private function seedFights(Event $event, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Fight::query()->create([
                'event_id' => $event->id,
                'corner_red_fighter_id' => Fighter::factory()->create()->id,
                'corner_blue_fighter_id' => Fighter::factory()->create()->id,
                'display_order' => $i,
                'status' => 0,
            ]);
        }
    }
}

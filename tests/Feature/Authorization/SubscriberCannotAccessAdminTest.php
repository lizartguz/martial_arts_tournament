<?php

namespace Tests\Feature\Authorization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Fase 7, punto 4: subscriber no entra a /admin.
 */
class SubscriberCannotAccessAdminTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_subscriber_gets_403_on_representative_admin_sections(): void
    {
        $this->actingAsRole('subscriber');

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.settings.index'))->assertForbidden();
        $this->get(route('admin.events.index'))->assertForbidden();
        $this->get(route('admin.logs.index'))->assertForbidden();
    }

    public function test_subscriber_can_use_their_own_portal_routes(): void
    {
        $this->actingAsRole('subscriber');

        $this->get(route('subscriber.dashboard'))->assertOk();
        $this->get(route('subscriber.profile'))->assertOk();
        $this->get(route('subscriber.events'))->assertOk();
    }

    public function test_smart_dashboard_redirect_routes_by_permission(): void
    {
        $this->actingAsRole('admin');
        $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));

        $this->actingAsRole('subscriber');
        $this->get(route('dashboard'))->assertRedirect(route('subscriber.dashboard'));

        // Usuario sin ningún rol asignado: no tiene dashboard.view ni
        // subscriber.dashboard.view, cae en el abort(403) del closure.
        $roleless = \App\Models\User::factory()->create();
        $this->withoutMiddleware(\Laravel\Jetstream\Http\Middleware\AuthenticateSession::class);
        $this->actingAs($roleless);
        $this->get(route('dashboard'))->assertForbidden();
    }

    /**
     * Comportamiento actual confirmado como intencional (no un bug): el rol admin
     * tiene "todos los permisos menos logs.download", lo que incluye los permisos
     * subscriber.*.view. Un admin sí puede navegar el portal del suscriptor hoy.
     */
    public function test_admin_role_can_currently_reach_subscriber_portal_routes(): void
    {
        $this->actingAsRole('admin');

        $this->get(route('subscriber.dashboard'))->assertOk();
    }
}

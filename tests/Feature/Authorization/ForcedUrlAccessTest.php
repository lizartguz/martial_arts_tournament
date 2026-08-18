<?php

namespace Tests\Feature\Authorization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Fase 7, punto 1: acceso forzado por URL.
 *
 * Cubre una muestra representativa de rutas admin por nivel de permiso,
 * no las ~25 rutas admin existentes.
 */
class ForcedUrlAccessTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_is_redirected_to_login_for_protected_dashboards(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('subscriber.dashboard'))->assertRedirect(route('login'));
    }

    public function test_role_without_required_permission_is_forbidden_by_direct_url(): void
    {
        $this->actingAsRole('checkin');
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.settings.index'))->assertForbidden();

        $this->actingAsRole('support');
        $this->get(route('admin.users.index'))->assertForbidden();

        $this->actingAsRole('subscriber');
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_role_with_required_permission_reaches_the_route(): void
    {
        $this->actingAsRole('sales');
        $this->get(route('admin.subscription-plans.index'))->assertOk();

        $this->actingAsRole('publisher');
        $this->get(route('admin.events.index'))->assertOk();

        $this->actingAsRole('super_manager');
        $this->get(route('admin.logs.index'))->assertOk();
    }

    public function test_admin_is_forbidden_from_the_one_permission_it_lacks(): void
    {
        $this->actingAsRole('admin');

        $this->get(route('admin.logs.download'))->assertForbidden();
    }
}

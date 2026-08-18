<?php

namespace Tests\Feature\Authorization;

use App\Services\AuthorizationHierarchyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Fase 7, punto 2: jerarquía de roles.
 *
 * Prueba App\Services\AuthorizationHierarchyService directamente (sin HTTP):
 * es la única fuente de verdad detrás de "admin no modifica admin" (punto 3)
 * y "subscriber no entra a /admin" (punto 4).
 */
class AuthorizationHierarchyServiceTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private AuthorizationHierarchyService $hierarchy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->hierarchy = app(AuthorizationHierarchyService::class);
    }

    public function test_super_manager_can_manage_anyone_including_another_super_manager(): void
    {
        $actor = $this->userWithRole('super_manager');
        $anotherSuperManager = $this->userWithRole('super_manager');
        $subscriber = $this->userWithRole('subscriber');

        $this->assertTrue($this->hierarchy->canManageUser($actor, $anotherSuperManager));
        $this->assertTrue($this->hierarchy->canManageUser($actor, $subscriber));
    }

    public function test_equal_rank_users_cannot_manage_each_other(): void
    {
        $salesOne = $this->userWithRole('sales');
        $salesTwo = $this->userWithRole('sales');

        $this->assertFalse($this->hierarchy->canManageUser($salesOne, $salesTwo));
        $this->assertFalse($this->hierarchy->canManageUser($salesTwo, $salesOne));
    }

    public function test_actor_can_manage_only_strictly_lower_ranked_target(): void
    {
        $publisher = $this->userWithRole('publisher');
        $sales = $this->userWithRole('sales');

        $this->assertTrue($this->hierarchy->canManageUser($publisher, $sales));
        $this->assertFalse($this->hierarchy->canManageUser($sales, $publisher));
    }

    public function test_no_one_but_super_manager_can_manage_a_super_manager(): void
    {
        $admin = $this->userWithRole('admin');
        $superManager = $this->userWithRole('super_manager');

        $this->assertFalse($this->hierarchy->canManageUser($admin, $superManager));
    }

    public function test_visible_and_hidden_role_names_respect_rank(): void
    {
        $admin = $this->userWithRole('admin');

        $visible = $this->hierarchy->visibleRoleNamesFor($admin);
        $hidden = $this->hierarchy->hiddenRoleNamesFor($admin);

        $this->assertNotContains('super_manager', $visible, 'super_manager es un rol protegido, no debe ser visible para admin');
        $this->assertContains('super_manager', $hidden);
        $this->assertContains('publisher', $visible, 'admin (80) debe ver roles de rango menor como publisher (50)');
        $this->assertContains('admin', $visible, 'admin debe verse a sí mismo (rango igual, <=)');
    }
}

<?php

namespace Tests\Feature\Authorization;

use App\Livewire\Admin\Users\UserTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;
use Livewire\Livewire;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Fase 7, punto 3: admin no modifica a otro admin.
 *
 * Se prueba a través del componente Livewire real (App\Livewire\Admin\Users\UserTable),
 * no por HTTP directo al modal, para aislar la lógica de negocio de la capa visual.
 */
class UserManagementHierarchyTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->withoutMiddleware(AuthenticateSession::class);
    }

    public function test_admin_cannot_edit_another_admin(): void
    {
        $actor = $this->userWithRole('admin');
        $target = $this->userWithRole('admin', ['name' => 'Otro Admin']);

        Livewire::actingAs($actor)
            ->test(UserTable::class)
            ->call('edit', $target->id)
            ->assertForbidden();
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $actor = $this->userWithRole('admin');
        $target = $this->userWithRole('admin', ['name' => 'Otro Admin']);

        Livewire::actingAs($actor)
            ->test(UserTable::class)
            ->call('confirmDelete', $target->id)
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_can_manage_a_lower_ranked_user(): void
    {
        $actor = $this->userWithRole('admin');
        $target = $this->userWithRole('publisher', ['name' => 'Publicador Original']);

        Livewire::actingAs($actor)
            ->test(UserTable::class)
            ->call('edit', $target->id)
            ->set('form.name', 'Publicador Editado')
            ->set('form.roles', ['publisher'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Publicador Editado']);
    }

    /**
     * Caso borde documentado, no corregido: a diferencia de confirmDelete()/delete()
     * (que bloquean la auto-eliminación con un mensaje amigable), edit()/save() no
     * tienen guard de auto-edición. Un admin editando su propia fila cae en
     * canManageUser($actor, $actor) -> rango 80 < 80 -> false -> 403 duro.
     */
    public function test_admin_editing_their_own_row_through_user_table_is_blocked_by_hierarchy(): void
    {
        $actor = $this->userWithRole('admin');

        Livewire::actingAs($actor)
            ->test(UserTable::class)
            ->call('edit', $actor->id)
            ->assertForbidden();
    }
}

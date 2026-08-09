<?php

namespace Tests\Feature;

use App\Support\AdminPanel;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminSidebarMenuTest extends TestCase
{
    public function test_menu_includes_notifications_for_users_with_matching_permission(): void
    {
        Auth::shouldReceive('user')->andReturn(
            $this->fakeUserWithPermissions(['ManageMarketing'])
        );

        $notifications = collect(AdminPanel::menu())->firstWhere('text', 'notifications');

        $this->assertNotNull($notifications);
        $this->assertSame('/notificationsp', $notifications['url']);
    }

    public function test_sidebar_partial_renders_authorized_administration_submenu_items(): void
    {
        Auth::shouldReceive('user')->andReturn(
            $this->fakeUserWithPermissions(['ManageManager'])
        );

        $administration = collect(AdminPanel::menu())->firstWhere('text', 'administration.label');

        $this->assertNotNull($administration);

        $html = view('layouts.partials.admin-sidebar-item', [
            'item' => $administration,
        ])->render();

        $this->assertStringNotContainsString('station-status', $html);
        $this->assertStringContainsString('forecasts-edit', $html);
    }

    private function fakeUserWithPermissions(array $permissions): object
    {
        return new class($permissions)
        {
            public function __construct(private array $permissions)
            {
            }

            public function canAny($requiredPermissions): bool
            {
                return array_intersect($this->permissions, (array) $requiredPermissions) !== [];
            }
        };
    }
}

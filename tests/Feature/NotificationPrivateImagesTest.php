<?php

namespace Tests\Feature;

use App\Livewire\NotificationsPush\NotificationsPush;
use App\Models\FcmToken;
use App\Models\NotificationM;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;
use Livewire\Livewire;
use Mockery\MockInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationPrivateImagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createMinimalSchema();
        $this->withoutMiddleware(AuthenticateSession::class);
        Storage::fake('local');
        Storage::fake('public');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('ManageMarketing', 'web');
    }

    protected function tearDown(): void
    {
        Schema::dropAllTables();
        parent::tearDown();
    }

    public function test_only_the_recipient_can_open_a_private_notification_image(): void
    {
        $recipient = $this->createUser('recipient@example.test');
        $unrelatedUser = $this->createUser('unrelated@example.test');
        $notification = $this->createNotification($recipient);

        Storage::disk('local')->put($notification->image, 'private-image-content');

        $this->actingAs($unrelatedUser)
            ->get(route('notifications.images.show', [$notification, 'image']))
            ->assertForbidden();

        $response = $this->actingAs($recipient)
            ->get(route('notifications.images.show', [$notification, 'image']));

        $response
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private');
        $this->assertSame('private-image-content', $response->streamedContent());
    }

    public function test_marketing_manager_can_open_any_notification_image(): void
    {
        $recipient = $this->createUser('recipient@example.test');
        $manager = $this->createUser('manager@example.test');
        $manager->givePermissionTo('ManageMarketing');
        $notification = $this->createNotification($recipient);

        Storage::disk('local')->put($notification->image, 'manager-image-content');

        $response = $this->actingAs($manager)
            ->get(route('notifications.images.show', [$notification, 'image']));

        $response->assertOk();
        $this->assertSame('manager-image-content', $response->streamedContent());
    }

    public function test_recipient_can_open_the_notice_detail_and_it_is_marked_as_read(): void
    {
        $recipient = $this->createUser('recipient@example.test');
        $unrelatedUser = $this->createUser('unrelated@example.test');
        $notification = $this->createNotification($recipient);

        $this->actingAs($unrelatedUser)
            ->get(route('notifications.show', $notification))
            ->assertForbidden();

        $this->actingAs($recipient)
            ->get(route('notifications.show', $notification))
            ->assertOk()
            ->assertSee('Aviso privado')
            ->assertSee(route('notifications.images.show', [$notification, 'image']), false);

        $this->assertDatabaseHas('notification_reads', [
            'notification_id' => $notification->id,
            'user_id' => $recipient->id,
        ]);
    }

    public function test_marketing_manager_can_open_the_fictitious_notification_preview(): void
    {
        $manager = $this->createUser('manager@example.test');
        $manager->givePermissionTo('ManageMarketing');

        $this->actingAs($manager)
            ->get(route('notifications.preview'))
            ->assertOk()
            ->assertSee('Vista de demostracion')
            ->assertSee('Alerta preventiva por lluvias intensas')
            ->assertSee('senvatec-hero-weather-station.png');
    }

    public function test_mobile_image_url_requires_an_untampered_signature(): void
    {
        $recipient = $this->createUser('recipient@example.test');
        $notification = $this->createNotification($recipient);

        Storage::disk('local')->put($notification->image, 'signed-image-content');

        $signedUrl = URL::temporarySignedRoute(
            'api.notifications.images.show',
            now()->addMinutes(15),
            [
                'notification' => $notification,
                'slot' => 'image',
                'user' => $recipient,
            ]
        );

        $response = $this->get($signedUrl);

        $response->assertOk();
        $this->assertSame('signed-image-content', $response->streamedContent());

        $this->get($signedUrl.'&user=999999')
            ->assertForbidden();
    }

    public function test_command_moves_legacy_images_to_private_storage(): void
    {
        $recipient = $this->createUser('recipient@example.test');
        $notification = $this->createNotification($recipient);

        Storage::disk('public')->put($notification->image, 'legacy-public-content');

        $this->artisan('app:notifications-migrate-images-private')
            ->assertSuccessful();

        Storage::disk('local')->assertExists($notification->image);
        Storage::disk('public')->assertMissing($notification->image);
        $this->assertSame(
            'legacy-public-content',
            Storage::disk('local')->get($notification->image)
        );
    }

    public function test_firebase_payload_does_not_expose_private_images(): void
    {
        $method = new \ReflectionMethod(PushNotificationService::class, 'buildPayload');
        $payload = $method->invoke(
            app(PushNotificationService::class),
            'test-token',
            'Aviso privado',
            'Descripcion del aviso',
            'https://example.test/notifications/1',
            null,
            ['notification_id' => '1']
        );

        $this->assertArrayNotHasKey('image', $payload['message']['notification']);
        $this->assertArrayNotHasKey('image', $payload['message']['webpush']['notification']);
        $this->assertArrayNotHasKey('image', $payload['message']['android']['notification']);
        $this->assertSame(
            'https://example.test/notifications/1',
            $payload['message']['webpush']['fcm_options']['link']
        );
    }

    public function test_firebase_credentials_support_a_readable_relative_storage_path(): void
    {
        $relativePath = 'framework/testing/firebase-adminsdk-test.json';
        $absolutePath = storage_path($relativePath);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0777, true);
        }

        file_put_contents($absolutePath, '{}');
        config(['services.firebase.sdk' => $relativePath]);

        try {
            $method = new \ReflectionMethod(PushNotificationService::class, 'resolveServiceAccountFile');

            $this->assertSame(
                $absolutePath,
                $method->invoke(app(PushNotificationService::class))
            );
        } finally {
            @unlink($absolutePath);
        }
    }

    public function test_missing_firebase_credentials_return_a_safe_error_without_internal_path(): void
    {
        config(['services.firebase.sdk' => 'app/firebase/missing-private-file.json']);

        $method = new \ReflectionMethod(PushNotificationService::class, 'resolveServiceAccountFile');

        try {
            $method->invoke(app(PushNotificationService::class));
            $this->fail('Se esperaba un error por credenciales de Firebase inexistentes.');
        } catch (\RuntimeException $exception) {
            $this->assertSame(
                'No se pudo acceder a las credenciales privadas de Firebase en el servidor.',
                $exception->getMessage()
            );
            $this->assertStringNotContainsString(storage_path(), $exception->getMessage());
        }
    }

    public function test_authenticated_browser_can_register_a_web_push_token(): void
    {
        $user = $this->createUser('push-user@example.test');

        $this->actingAs($user)
            ->postJson(route('firebase.web-push.token.store'), [
                'token' => 'firebase-web-token-for-testing',
                'browser' => 'Microsoft Edge',
                'device_name' => 'Windows / es-BO',
                'device_identifier' => '127.0.0.1:8000',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('fcm_tokens', [
            'user_id' => $user->id,
            'token' => 'firebase-web-token-for-testing',
            'platform' => 'web',
            'delivery_platform' => 'web',
            'is_active' => 1,
        ]);
    }

    public function test_web_push_bell_is_available_in_the_top_navigation(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/admin.blade.php'));

        $this->assertStringContainsString('id="web-push-toggle"', $layout);
        $this->assertStringContainsString('web-push-navbar-link', $layout);
        $this->assertStringContainsString('fas fa-bell text-secondary', $layout);
        $this->assertStringContainsString('href="#"', $layout);
    }

    public function test_web_push_bell_supports_adminlte_and_livewire_navigation(): void
    {
        $script = file_get_contents(resource_path('js/push-notifications.js'));

        $this->assertStringContainsString('a.web-push-navbar-link', $script);
        $this->assertStringContainsString('button.dataset.webPushInitialized', $script);
        $this->assertStringContainsString("'livewire:navigated'", $script);
        $this->assertStringContainsString("setBellState('synchronized')", $script);
    }

    public function test_authenticated_user_can_check_their_active_web_push_token_without_exposing_it(): void
    {
        $user = $this->createUser('push-status@example.test');

        app(\App\Services\FcmTokenService::class)->registerWebTokenForUser(
            $user,
            'firebase-status-token',
            ['browser' => 'Microsoft Edge']
        );

        $this->actingAs($user)
            ->postJson(route('firebase.web-push.token.status'), [
                'token' => 'firebase-status-token',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'registered' => true,
            ])
            ->assertJsonMissing([
                'token' => 'firebase-status-token',
            ]);
    }

    public function test_web_push_token_status_is_false_for_another_user(): void
    {
        $owner = $this->createUser('push-owner@example.test');
        $otherUser = $this->createUser('push-other@example.test');

        app(\App\Services\FcmTokenService::class)->registerWebTokenForUser(
            $owner,
            'firebase-private-status-token'
        );

        $this->actingAs($otherUser)
            ->postJson(route('firebase.web-push.token.status'), [
                'token' => 'firebase-private-status-token',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'registered' => false,
            ]);
    }

    public function test_same_web_push_token_is_reassigned_to_the_latest_authenticated_user(): void
    {
        $firstUser = $this->createUser('push-first@example.test');
        $secondUser = $this->createUser('push-second@example.test');
        $service = app(\App\Services\FcmTokenService::class);

        $service->registerWebTokenForUser($firstUser, 'firebase-shared-browser-token');
        $service->registerWebTokenForUser($secondUser, 'firebase-shared-browser-token');

        $this->assertDatabaseCount('fcm_tokens', 1);
        $this->assertDatabaseHas('fcm_tokens', [
            'token' => 'firebase-shared-browser-token',
            'user_id' => $secondUser->id,
            'is_active' => 1,
        ]);
        $this->assertFalse(
            $service->isActiveWebTokenForUser($firstUser, 'firebase-shared-browser-token')
        );
        $this->assertTrue(
            $service->isActiveWebTokenForUser($secondUser, 'firebase-shared-browser-token')
        );
    }

    public function test_service_worker_uses_cached_firebase_configuration(): void
    {
        config()->set('services.firebase.web', [
            'api_key' => 'test-api-key',
            'auth_domain' => 'example.firebaseapp.com',
            'project_id' => 'example-project',
            'storage_bucket' => 'example.firebasestorage.app',
            'messaging_sender_id' => '123456789',
            'app_id' => '1:123456789:web:test',
        ]);

        $response = $this->get(route('firebase.web-push.sw'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=UTF-8')
            ->assertSee('"test-api-key"', false)
            ->assertSee('"example-project"', false)
            ->assertSee('"1:123456789:web:test"', false)
            ->assertSee('self.skipWaiting()', false)
            ->assertSee('self.clients.claim()', false);

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    public function test_long_browser_user_agent_is_safely_limited_when_registering_a_token(): void
    {
        $user = $this->createUser('long-browser@example.test');
        $longUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
            .'(KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0 '
            .str_repeat('extension-data-', 20);

        $this->actingAs($user)
            ->postJson(route('firebase.web-push.token.store'), [
                'token' => 'firebase-web-token-with-long-browser',
                'browser' => $longUserAgent,
                'device_name' => str_repeat('Windows device ', 20),
                'device_identifier' => 'environmental.artguz.com',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $token = FcmToken::query()
            ->where('token', 'firebase-web-token-with-long-browser')
            ->firstOrFail();

        $this->assertLessThanOrEqual(120, mb_strlen($token->browser));
        $this->assertLessThanOrEqual(150, mb_strlen($token->device_name));
    }

    public function test_manager_can_resend_an_existing_notification_without_creating_another_record(): void
    {
        $manager = $this->createUser('manager-resend@example.test');
        $manager->givePermissionTo('ManageMarketing');
        $recipient = $this->createUser('recipient-resend@example.test');
        $notification = $this->createNotification($recipient);

        $this->mock(PushNotificationService::class, function (MockInterface $mock) use ($notification) {
            $mock->shouldReceive('sendNotificationRecord')
                ->once()
                ->withArgs(fn (NotificationM $record) => $record->is($notification))
                ->andReturn([
                    'success_count' => 1,
                    'failure_count' => 0,
                    'errors' => [],
                ]);
        });

        Livewire::actingAs($manager)
            ->test(NotificationsPush::class)
            ->call('resendPush', $notification->id)
            ->assertDispatched('successAlert');

        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_concurrent_resend_is_rejected_before_calling_firebase(): void
    {
        $manager = $this->createUser('manager-duplicate@example.test');
        $manager->givePermissionTo('ManageMarketing');
        $recipient = $this->createUser('recipient-duplicate@example.test');
        $notification = $this->createNotification($recipient);
        $lock = Cache::lock("notification-push-resend:{$notification->id}", 300);

        $this->assertTrue($lock->get());

        $this->mock(PushNotificationService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('sendNotificationRecord');
        });

        try {
            Livewire::actingAs($manager)
                ->test(NotificationsPush::class)
                ->call('resendPush', $notification->id)
                ->assertDispatched('failedAlert');
        } finally {
            $lock->release();
        }
    }

    private function createNotification(User $recipient): NotificationM
    {
        return NotificationM::create([
            'title' => 'Aviso privado',
            'description' => 'Contenido protegido',
            'image' => 'notifications/private-test.jpg',
            'type_user' => 'individual',
            'type' => 'both',
            'delivery_platform' => 'all',
            'metadata' => [
                'target_user_ids' => [$recipient->id],
            ],
            'reg_date' => now(),
            'deadline' => now()->addDay()->toDateString(),
            'scheduled_at' => now()->subMinute(),
            'state' => 1,
        ]);
    }

    private function createUser(string $email): User
    {
        return User::forceCreate([
            'name' => 'Usuario de prueba',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'state' => 1,
            'access_type' => 0,
        ]);
    }

    private function createMinimalSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->tinyInteger('state')->nullable();
            $table->tinyInteger('access_type')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('link')->nullable();
            $table->string('type_user')->nullable();
            $table->string('type')->nullable();
            $table->string('delivery_platform')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('reg_date')->nullable();
            $table->date('deadline')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('push_sent_at')->nullable();
            $table->dateTime('push_last_error_at')->nullable();
            $table->string('push_last_error_message')->nullable();
            $table->tinyInteger('state')->nullable();
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
            $table->unique(['notification_id', 'user_id']);
        });

        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token')->unique();
            $table->string('platform', 20);
            $table->string('device_identifier', 300)->nullable();
            $table->string('device_name', 150)->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('delivery_platform', 20)->default('unknown');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->string('last_error_message', 500)->nullable();
            $table->timestamp('invalidated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });
    }
}

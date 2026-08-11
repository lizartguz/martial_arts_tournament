<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MmaDemoSeeder extends Seeder
{
    private array $images = [
        'frontend/images/senvatec-hero-weather-station.png',
        'frontend/images/senvatec-cta-landscape.png',
        'frontend/images/senvatec-agro-monitoring.png',
        'frontend/images/senvatec-monitoring-dashboard.png',
        'frontend/images/senvatec-sensor-network.png',
    ];

    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        DB::transaction(function () {
            $rootId = User::query()->where('email', 'sarteaga@root.dev')->value('id');
            $cityIds = DB::table('cities')->pluck('id', 'name')->all();
            $countryId = DB::table('countries')->where('iso2', 'BO')->value('id');

            $venueIds = $this->seedVenues($cityIds, $rootId);
            $teamIds = $this->seedTeams($cityIds, $rootId);
            $fighterIds = $this->seedFighters($countryId, $cityIds, $teamIds, $rootId);
            $eventIds = $this->seedEvents($venueIds, $rootId);
            $this->seedFights($eventIds, $fighterIds, $rootId);
            $this->seedRankings($fighterIds, $rootId);
            $planIds = $this->seedPlans($rootId);
            $subscriberIds = $this->seedSubscribers();
            $subscriptionIds = $this->seedSubscriptions($subscriberIds, $planIds, $rootId);
            $this->seedPayments($subscriptionIds, $subscriberIds);
            $this->seedPurchaseRequests($eventIds, $planIds, $subscriberIds, $rootId);
            $this->seedTicketLinks($eventIds);
        });
    }

    private function seedVenues(array $cityIds, ?int $rootId): array
    {
        $venues = [
            ['Arena Santa Cruz', 'Santa Cruz de la Sierra', 'Av. Cristo Redentor, Santa Cruz', 8500],
            ['Coliseo La Paz Fight Night', 'La Paz', 'Zona Sur, La Paz', 5200],
            ['Cochabamba Combat Center', 'Cochabamba', 'Av. América, Cochabamba', 4300],
        ];

        $ids = [];
        foreach ($venues as $index => [$name, $city, $address, $capacity]) {
            $slug = Str::slug($name);
            DB::table('venues')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'city_id' => $cityIds[$city] ?? null,
                    'address' => $address,
                    'capacity' => $capacity,
                    'image' => $this->images[$index],
                    'status' => 1,
                    'created_by' => $rootId,
                    'updated_by' => $rootId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $ids[] = DB::table('venues')->where('slug', $slug)->value('id');
        }

        return $ids;
    }

    private function seedTeams(array $cityIds, ?int $rootId): array
    {
        $teams = [
            ['Jaguar Fight Team', 'Santa Cruz de la Sierra', 'Coach Andrés Rojas'],
            ['Altura MMA Club', 'La Paz', 'Coach Mariana Salvatierra'],
            ['Valle Grappling Academy', 'Cochabamba', 'Coach Diego Mercado'],
            ['Oriente Striking Lab', 'Santa Cruz de la Sierra', 'Coach Carla Méndez'],
            ['Andes Combat House', 'La Paz', 'Coach Javier Quiroga'],
            ['Central MMA Bolivia', 'Cochabamba', 'Coach Patricia León'],
        ];

        $ids = [];
        foreach ($teams as [$name, $city, $coach]) {
            $slug = Str::slug($name);
            DB::table('fighter_teams')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'city_id' => $cityIds[$city] ?? null,
                    'coach_name' => $coach,
                    'contact_phone' => '+59170000000',
                    'logo_path' => 'images/logo_circle.png',
                    'description' => "Equipo demo para pruebas de {$name}.",
                    'status' => 1,
                    'created_by' => $rootId,
                    'updated_by' => $rootId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $ids[] = DB::table('fighter_teams')->where('slug', $slug)->value('id');
        }

        return $ids;
    }

    private function seedFighters(?int $countryId, array $cityIds, array $teamIds, ?int $rootId): array
    {
        $names = [
            ['Mateo', 'Ríos', 'El Trueno', 'male'],
            ['Bruno', 'Vargas', 'Martillo', 'male'],
            ['Lucas', 'Paz', 'Cóndor', 'male'],
            ['Diego', 'Salvatierra', 'Relámpago', 'male'],
            ['Nicolás', 'Suárez', 'Jaguar', 'male'],
            ['Gabriel', 'Molina', 'Furia', 'male'],
            ['Valentina', 'Roca', 'Tormenta', 'female'],
            ['Camila', 'Luna', 'La Pantera', 'female'],
            ['Renata', 'Soliz', 'Amazona', 'female'],
            ['Lucía', 'Vega', 'Centella', 'female'],
            ['Daniela', 'Cruz', 'La Cobra', 'female'],
            ['Sofía', 'Méndez', 'Titania', 'female'],
        ];

        $weightClasses = DB::table('weight_classes')->get()->groupBy('gender');
        $cityValues = array_values($cityIds);
        $ids = [];

        foreach ($names as $index => [$firstName, $lastName, $nickname, $gender]) {
            $class = $weightClasses[$gender]->values()[$index % $weightClasses[$gender]->count()];
            $slug = Str::slug("{$firstName} {$lastName}");

            DB::table('fighters')->updateOrInsert(
                ['slug' => $slug],
                [
                    'uuid' => (string) Str::uuid(),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'nickname' => $nickname,
                    'gender' => $gender,
                    'country_id' => $countryId,
                    'city_id' => $cityValues[$index % count($cityValues)] ?? null,
                    'fighter_team_id' => $teamIds[$index % count($teamIds)] ?? null,
                    'weight_class_id' => $class->id,
                    'birthdate' => now()->subYears(22 + ($index % 12))->subMonths($index)->toDateString(),
                    'height_cm' => 160 + ($index * 3),
                    'reach_cm' => 165 + ($index * 3),
                    'stance' => $index % 2 === 0 ? 'orthodox' : 'southpaw',
                    'bio' => "Perfil demo de {$firstName} {$lastName} para visualizar fichas públicas y administrativas.",
                    'profile_image' => $this->images[$index % count($this->images)],
                    'cover_image' => $this->images[($index + 1) % count($this->images)],
                    'wins' => 4 + $index,
                    'losses' => $index % 4,
                    'draws' => $index % 2,
                    'no_contests' => 0,
                    'status' => 1,
                    'created_by' => $rootId,
                    'updated_by' => $rootId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $fighterId = DB::table('fighters')->where('slug', $slug)->value('id');
            DB::table('fighter_stats')->updateOrInsert(
                ['fighter_id' => $fighterId],
                [
                    'knockout_wins' => $index % 5,
                    'submission_wins' => ($index + 1) % 4,
                    'decision_wins' => ($index + 2) % 5,
                    'knockout_losses' => $index % 2,
                    'submission_losses' => 0,
                    'decision_losses' => $index % 3,
                    'current_streak' => ($index % 5) - 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $ids[] = $fighterId;
        }

        return $ids;
    }

    private function seedEvents(array $venueIds, ?int $rootId): array
    {
        $events = [
            ['Combate Real I: Noche de Campeones', -12, true, 1],
            ['Combate Real II: Choque Andino', -2, false, 1],
            ['Combate Real III: Guerra en la Jaula', 0, true, 1],
            ['Combate Real IV: Camino al Título', 18, false, 1],
            ['Combate Real V: Final de Temporada', 42, false, 1],
            ['Combate Real Draft Interno', 60, false, 0],
        ];

        $ids = [];
        foreach ($events as $index => [$name, $days, $featured, $status]) {
            $slug = Str::slug($name);
            DB::table('events')->updateOrInsert(
                ['slug' => $slug],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $name,
                    'subtitle' => 'Cartelera demo de artes marciales mixtas',
                    'description' => "Evento demo {$name} para probar landing, filtros y cartelera.",
                    'venue_id' => $venueIds[$index % count($venueIds)] ?? null,
                    'starts_at' => now()->addDays($days)->setTime(20, 0),
                    'doors_open_at' => now()->addDays($days)->setTime(18, 30),
                    'timezone' => config('app.timezone'),
                    'poster_image' => $this->images[$index % count($this->images)],
                    'banner_image' => $this->images[($index + 1) % count($this->images)],
                    'stream_url' => null,
                    'ticket_url' => null,
                    'status' => $status,
                    'is_featured' => $featured,
                    'published_at' => $status === 1 ? now()->subDays(20 - $index) : null,
                    'created_by' => $rootId,
                    'updated_by' => $rootId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $ids[] = DB::table('events')->where('slug', $slug)->value('id');
        }

        return $ids;
    }

    private function seedFights(array $eventIds, array $fighterIds, ?int $rootId): void
    {
        $weightClassIds = DB::table('weight_classes')->pluck('id')->values()->all();

        foreach ($eventIds as $eventIndex => $eventId) {
            for ($i = 0; $i < 5; $i++) {
                $red = $fighterIds[($eventIndex + $i) % count($fighterIds)];
                $blue = $fighterIds[($eventIndex + $i + 6) % count($fighterIds)];
                $title = "Combate {$eventIndex}-{$i}";

                DB::table('fights')->updateOrInsert(
                    ['event_id' => $eventId, 'display_order' => $i + 1],
                    [
                        'weight_class_id' => $weightClassIds[($eventIndex + $i) % count($weightClassIds)],
                        'corner_red_fighter_id' => $red,
                        'corner_blue_fighter_id' => $blue,
                        'title' => $title,
                        'bout_type' => $i === 0 ? 'main_event' : ($i === 1 ? 'co_main_event' : 'regular'),
                        'rounds' => $i < 2 ? 5 : 3,
                        'starts_at' => DB::table('events')->where('id', $eventId)->value('starts_at'),
                        'promo_image' => $this->images[$i % count($this->images)],
                        'status' => $eventIndex < 2 ? 2 : 0,
                        'is_main_event' => $i === 0,
                        'is_featured' => $i < 2,
                        'notes' => 'Combate demo para visualización de cartelera.',
                        'created_by' => $rootId,
                        'updated_by' => $rootId,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }

    private function seedRankings(array $fighterIds, ?int $rootId): void
    {
        $classes = DB::table('weight_classes')->get();

        foreach ($classes as $class) {
            $fighters = DB::table('fighters')
                ->where('gender', $class->gender)
                ->where('weight_class_id', $class->id)
                ->pluck('id')
                ->values();

            foreach ($fighters as $index => $fighterId) {
                DB::table('fighter_rankings')->updateOrInsert(
                    ['weight_class_id' => $class->id, 'gender' => $class->gender, 'fighter_id' => $fighterId],
                    [
                        'position' => $index + 1,
                        'previous_position' => $index + 2,
                        'is_champion' => $index === 0,
                        'ranked_at' => now()->toDateString(),
                        'status' => 1,
                        'created_by' => $rootId,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }

    private function seedPlans(?int $rootId): array
    {
        $plans = [
            ['Básico', 49, 10],
            ['Fan Plus', 89, 15],
            ['VIP Combate', 149, 20],
        ];

        $ids = [];
        foreach ($plans as $index => [$name, $price, $discount]) {
            $slug = Str::slug($name);
            DB::table('subscription_plans')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => "Plan demo {$name} para beneficios de suscriptor.",
                    'price' => $price,
                    'currency' => 'BOB',
                    'billing_period' => 'monthly',
                    'duration_days' => 30,
                    'discount_percentage' => $discount,
                    'features' => json_encode(['Avisos de eventos', 'Historial de compras', 'Contacto preferente']),
                    'limits' => json_encode(['events' => null]),
                    'display_order' => $index + 1,
                    'status' => 1,
                    'created_by' => $rootId,
                    'updated_by' => $rootId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $ids[] = DB::table('subscription_plans')->where('slug', $slug)->value('id');
        }

        return $ids;
    }

    private function seedSubscribers(): array
    {
        $ids = [];
        for ($i = 1; $i <= 12; $i++) {
            $user = User::query()->updateOrCreate(
                ['email' => "suscriptor{$i}@demo.dev"],
                [
                    'username' => "suscriptor{$i}",
                    'name' => "Suscriptor {$i}",
                    'lastname' => 'Demo',
                    'number_phone' => '+591700000'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password'),
                    'state' => 1,
                ]
            );
            $user->syncRoles(['subscriber']);
            $ids[] = $user->id;
        }

        return $ids;
    }

    private function seedSubscriptions(array $subscriberIds, array $planIds, ?int $rootId): array
    {
        $ids = [];
        foreach ($subscriberIds as $index => $userId) {
            DB::table('user_subscriptions')->updateOrInsert(
                ['user_id' => $userId, 'subscription_plan_id' => $planIds[$index % count($planIds)]],
                [
                    'starts_at' => now()->subDays($index + 1),
                    'ends_at' => now()->addDays(30 - $index),
                    'trial_ends_at' => null,
                    'cancelled_at' => null,
                    'renewal_at' => now()->addDays(30 - $index),
                    'status' => $index % 5 === 0 ? 0 : 1,
                    'source' => 'manual',
                    'metadata' => json_encode(['demo' => true]),
                    'created_by' => $rootId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $ids[] = DB::table('user_subscriptions')
                ->where('user_id', $userId)
                ->where('subscription_plan_id', $planIds[$index % count($planIds)])
                ->value('id');
        }

        return $ids;
    }

    private function seedPayments(array $subscriptionIds, array $subscriberIds): void
    {
        foreach ($subscriptionIds as $index => $subscriptionId) {
            DB::table('subscription_payments')->updateOrInsert(
                ['user_subscription_id' => $subscriptionId],
                [
                    'user_id' => $subscriberIds[$index],
                    'amount' => [49, 89, 149][$index % 3],
                    'currency' => 'BOB',
                    'payment_method' => ['cash', 'qr', 'bank_transfer'][$index % 3],
                    'provider' => null,
                    'provider_transaction_id' => null,
                    'payment_url' => null,
                    'payment_proof_path' => null,
                    'payment_proof_mime' => null,
                    'payment_proof_size' => null,
                    'paid_at' => $index % 4 === 0 ? null : now()->subDays($index),
                    'expires_at' => now()->addDays(3),
                    'status' => $index % 4 === 0 ? 0 : 1,
                    'notes' => 'Pago demo registrado manualmente.',
                    'metadata' => json_encode(['demo' => true]),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedPurchaseRequests(array $eventIds, array $planIds, array $subscriberIds, ?int $rootId): void
    {
        for ($i = 1; $i <= 15; $i++) {
            DB::table('purchase_requests')->updateOrInsert(
                ['contact_email' => "interesado{$i}@demo.dev"],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $subscriberIds[($i - 1) % count($subscriberIds)],
                    'event_id' => $eventIds[($i - 1) % count($eventIds)],
                    'subscription_plan_id' => $planIds[($i - 1) % count($planIds)],
                    'contact_name' => "Interesado {$i}",
                    'contact_email' => "interesado{$i}@demo.dev",
                    'contact_phone' => '+591711100'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'contact_whatsapp' => '+591711100'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'preferred_channel' => ['whatsapp', 'phone', 'form'][$i % 3],
                    'request_type' => $i % 2 === 0 ? 'event_ticket' : 'subscription_plan',
                    'message' => 'Solicitud demo creada desde landing o portal.',
                    'payment_proof_path' => null,
                    'payment_proof_mime' => null,
                    'payment_proof_size' => null,
                    'status' => $i % 6,
                    'assigned_to' => $rootId,
                    'handled_by' => $i % 3 === 0 ? $rootId : null,
                    'handled_at' => $i % 3 === 0 ? now()->subDays($i) : null,
                    'notes' => 'Nota interna demo.',
                    'metadata' => json_encode(['demo' => true, 'source' => 'landing']),
                    'updated_at' => now(),
                    'created_at' => now()->subDays($i),
                ]
            );
        }
    }

    private function seedTicketLinks(array $eventIds): void
    {
        foreach ($eventIds as $index => $eventId) {
            foreach ([['WhatsApp', 'Reservar por WhatsApp', 'whatsapp'], ['Boletería', 'Entrada general', 'physical_box_office']] as $order => [$provider, $label, $channel]) {
                DB::table('ticket_links')->updateOrInsert(
                    ['event_id' => $eventId, 'provider_name' => $provider, 'label' => $label],
                    [
                        'sale_channel' => $channel,
                        'url' => $channel === 'whatsapp' ? 'https://wa.me/59170000000' : 'https://example.com/entradas',
                        'price_from' => $channel === 'whatsapp' ? null : 50 + ($index * 10),
                        'currency' => 'BOB',
                        'starts_at' => now()->subDays(30),
                        'ends_at' => now()->addDays(60),
                        'display_order' => $order + 1,
                        'status' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    /**
     * Muestra la pantalla principal del módulo.
     */
    public function index(Request $request)
    {
        $perPage = min(max((int) $request->integer('per_page', 12), 1), 50);

        $events = Event::query()
            ->published()
            ->with(['venue:id,name,city_id'])
            ->withCount('fights')
            ->orderByDesc('is_featured')
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, starts_at, NOW()))')
            ->paginate($perPage, [
                'id',
                'uuid',
                'venue_id',
                'name',
                'slug',
                'subtitle',
                'starts_at',
                'doors_open_at',
                'timezone',
                'poster_image',
                'banner_image',
                'is_featured',
                'published_at',
            ]);

        return response()->json($events->through(fn (Event $event) => $this->eventSummary($event)));
    }

    /**
     * Muestra el recurso solicitado cuando el usuario tiene acceso.
     */
    public function show(Event $event)
    {
        abort_unless($event->status === 1, 404);

        $event->load([
            'venue:id,name,city_id,address',
            'ticketLinks' => fn ($query) => $query
                ->where('status', 1)
                ->orderBy('display_order')
                ->select([
                    'id',
                    'event_id',
                    'provider_name',
                    'label',
                    'sale_channel',
                    'url',
                    'price_from',
                    'currency',
                    'starts_at',
                    'ends_at',
                    'display_order',
                ]),
            'fights' => fn ($query) => $query
                ->with([
                    'cornerRed:id,first_name,last_name,nickname,slug,country_id,weight_class_id',
                    'cornerBlue:id,first_name,last_name,nickname,slug,country_id,weight_class_id',
                    'weightClass:id,name,slug,gender,max_weight_kg,max_weight_lb',
                ])
                ->orderBy('display_order')
                ->select([
                    'id',
                    'event_id',
                    'weight_class_id',
                    'corner_red_fighter_id',
                    'corner_blue_fighter_id',
                    'title',
                    'bout_type',
                    'rounds',
                    'display_order',
                    'starts_at',
                    'status',
                    'is_main_event',
                    'is_featured',
                ]),
        ]);

        return response()->json([
            'data' => array_merge($this->eventSummary($event), [
                'description' => $event->description,
                'stream_url' => $event->stream_url,
                'ticket_url' => $event->ticket_url,
                'fights' => $event->fights->map(fn ($fight) => [
                    'id' => $fight->id,
                    'title' => $fight->title,
                    'bout_type' => $fight->bout_type,
                    'rounds' => $fight->rounds,
                    'display_order' => $fight->display_order,
                    'starts_at' => $fight->starts_at?->toIso8601String(),
                    'status' => $fight->status,
                    'is_main_event' => (bool) $fight->is_main_event,
                    'is_featured' => (bool) $fight->is_featured,
                    'weight_class' => $fight->weightClass ? [
                        'id' => $fight->weightClass->id,
                        'name' => $fight->weightClass->name,
                        'slug' => $fight->weightClass->slug,
                        'gender' => $fight->weightClass->gender,
                        'max_weight_kg' => $fight->weightClass->max_weight_kg,
                        'max_weight_lb' => $fight->weightClass->max_weight_lb,
                    ] : null,
                    'corner_red' => $this->fighterSummary($fight->cornerRed),
                    'corner_blue' => $this->fighterSummary($fight->cornerBlue),
                ])->values(),
                'ticket_links' => $event->ticketLinks->map(fn ($link) => [
                    'id' => $link->id,
                    'provider_name' => $link->provider_name,
                    'label' => $link->label,
                    'sale_channel' => $link->sale_channel,
                    'url' => $link->url,
                    'price_from' => $link->price_from,
                    'currency' => $link->currency,
                    'starts_at' => $link->starts_at?->toIso8601String(),
                    'ends_at' => $link->ends_at?->toIso8601String(),
                ])->values(),
            ]),
        ]);
    }

    /**
     * Resume los datos públicas del evento para la API.
     */
    private function eventSummary(Event $event): array
    {
        return [
            'id' => $event->id,
            'uuid' => $event->uuid,
            'name' => $event->name,
            'slug' => $event->slug,
            'subtitle' => $event->subtitle,
            'starts_at' => $event->starts_at?->toIso8601String(),
            'doors_open_at' => $event->doors_open_at?->toIso8601String(),
            'timezone' => $event->timezone,
            'is_featured' => (bool) $event->is_featured,
            'poster_url' => $event->posterUrl(),
            'banner_url' => $event->bannerUrl(),
            'fights_count' => $event->fights_count ?? null,
            'venue' => $event->venue ? [
                'id' => $event->venue->id,
                'name' => $event->venue->name,
                'address' => $event->venue->address ?? null,
            ] : null,
        ];
    }

    /**
     * Resume los datos públicas del peleador para la API.
     */
    private function fighterSummary($fighter): ?array
    {
        if (! $fighter) {
            return null;
        }

        return [
            'id' => $fighter->id,
            'name' => trim("{$fighter->first_name} {$fighter->last_name}"),
            'nickname' => $fighter->nickname,
            'slug' => $fighter->slug,
        ];
    }
}

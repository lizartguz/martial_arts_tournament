<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\SystemSettingsService;

class LandingController extends Controller
{
    /**
     * Muestra una landing publica inicial con eventos publicados.
     */
    public function index(SystemSettingsService $settings)
    {
        $events = Event::query()
            ->with('venue:id,name,city_id')
            ->published()
            ->orderByDesc('is_featured')
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, starts_at, NOW()))')
            ->limit(12)
            ->get(['id', 'venue_id', 'name', 'slug', 'starts_at', 'subtitle', 'description', 'poster_image', 'is_featured']);

        return view('landing.mma-home', compact('events', 'settings'));
    }

    /**
     * Muestra el detalle publico basico de un evento publicado.
     */
    public function event(Event $event, SystemSettingsService $settings)
    {
        abort_unless($event->status === 1, 404);

        $event->load([
            'venue:id,name,city_id,address',
            'fights' => fn ($query) => $query
                ->with([
                    'cornerRed:id,nickname,first_name,last_name,slug',
                    'cornerBlue:id,nickname,first_name,last_name,slug',
                    'weightClass:id,name,gender',
                ])
                ->orderBy('display_order'),
        ]);

        return view('landing.event-detail', compact('event', 'settings'));
    }
}

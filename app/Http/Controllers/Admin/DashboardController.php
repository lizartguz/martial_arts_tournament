<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Fighter;
use App\Models\PurchaseRequest;
use App\Models\SubscriptionPayment;

class DashboardController extends Controller
{
    /**
     * Muestra indicadores iniciales del panel administrativo.
     */
    public function index()
    {
        $summary = [
            'published_events' => Event::query()->where('status', 1)->count(),
            'active_fighters' => Fighter::query()->where('status', 1)->count(),
            'pending_purchase_requests' => PurchaseRequest::query()->whereIn('status', [0, 1])->count(),
            'pending_payments' => SubscriptionPayment::query()->where('status', 0)->count(),
        ];

        $upcomingEvents = Event::query()
            ->with('venue:id,name,city_id')
            ->where('status', 1)
            ->orderByDesc('is_featured')
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, starts_at, NOW()))')
            ->limit(5)
            ->get(['id', 'venue_id', 'name', 'slug', 'starts_at', 'status', 'is_featured']);

        return view('admin.dashboard', compact('summary', 'upcomingEvents'));
    }
}

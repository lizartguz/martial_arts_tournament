<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        Gate::authorize('subscriber.dashboard.view');

        $user = $request->user();

        return view('subscriber.dashboard', [
            'subscription' => $user->userSubscriptions()
                ->with('plan:id,name,slug,price,currency,billing_period,status')
                ->latest('id')
                ->first(),
            'payments' => $user->subscriptionPayments()
                ->with('subscription.plan:id,name,slug')
                ->latest('id')
                ->limit(5)
                ->get(),
            'purchaseRequests' => $user->purchaseRequests()
                ->with(['event:id,name,slug,starts_at,status', 'plan:id,name,slug'])
                ->latest('id')
                ->limit(5)
                ->get(),
            'events' => $this->accessibleEvents($user->id)->limit(4)->get(),
        ]);
    }

    public function purchases(Request $request)
    {
        Gate::authorize('subscriber.purchases.view');

        $user = $request->user();

        return view('subscriber.purchases', [
            'payments' => $user->subscriptionPayments()
                ->with('subscription.plan:id,name,slug')
                ->latest('id')
                ->paginate(10, ['*'], 'payments_page'),
            'purchaseRequests' => $user->purchaseRequests()
                ->with(['event:id,name,slug,starts_at,status', 'plan:id,name,slug'])
                ->latest('id')
                ->paginate(10, ['*'], 'requests_page'),
        ]);
    }

    public function events(Request $request)
    {
        Gate::authorize('subscriber.events.view');

        return view('subscriber.events', [
            'events' => $this->accessibleEvents($request->user()->id)
                ->paginate(12),
        ]);
    }

    public function subscription(Request $request)
    {
        Gate::authorize('subscriber.subscription.view');

        $user = $request->user();

        return view('subscriber.subscription', [
            'currentSubscription' => $user->userSubscriptions()
                ->with(['plan:id,name,slug,description,price,currency,billing_period,features,status'])
                ->latest('id')
                ->first(),
            'subscriptions' => $user->userSubscriptions()
                ->with('plan:id,name,slug,price,currency,billing_period')
                ->latest('id')
                ->paginate(10),
        ]);
    }

    public function profile(Request $request)
    {
        Gate::authorize('subscriber.profile.view');

        return view('subscriber.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        Gate::authorize('subscriber.profile.update');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'lastname' => ['nullable', 'string', 'max:100'],
            'number_phone' => ['nullable', 'string', 'max:30'],
            'identity_document' => ['nullable', 'string', 'max:40'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($request->user()->id),
            ],
        ], attributes: [
            'name' => __('mma.subscriber_portal.profile.form.name'),
            'lastname' => __('mma.subscriber_portal.profile.form.lastname'),
            'number_phone' => __('mma.subscriber_portal.profile.form.number_phone'),
            'identity_document' => __('mma.subscriber_portal.profile.form.identity_document'),
            'email' => __('mma.subscriber_portal.profile.form.email'),
        ]);

        $request->user()->update($validated);

        return redirect()
            ->route('subscriber.profile')
            ->with('success', __('mma.subscriber_portal.profile.updated'));
    }

    protected function accessibleEvents(int $userId)
    {
        return Event::query()
            ->with('venue:id,name,city_id')
            ->where('status', 1)
            ->whereIn('id', PurchaseRequest::query()
                ->where('user_id', $userId)
                ->whereNotNull('event_id')
                ->select('event_id'))
            ->orderByDesc('is_featured')
            ->orderByRaw('CASE WHEN starts_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('starts_at')
            ->select(['id', 'venue_id', 'name', 'slug', 'starts_at', 'subtitle', 'description', 'poster_image', 'is_featured']);
    }
}

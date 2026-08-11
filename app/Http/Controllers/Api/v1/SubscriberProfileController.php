<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriberProfileController extends Controller
{
    /**
     * Muestra el recurso solicitado cuando el usuario tiene acceso.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $user->load([
            'roles:id,name',
            'userSubscriptions' => fn ($query) => $query
                ->with('plan:id,name,slug,price,currency,status')
                ->latest('id')
                ->limit(5),
            'subscriptionPayments' => fn ($query) => $query
                ->latest('id')
                ->limit(5),
            'purchaseRequests' => fn ($query) => $query
                ->latest('id')
                ->limit(5),
        ]);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'number_phone' => $user->number_phone,
                'state' => $user->state,
                'roles' => $user->roles->pluck('name')->values(),
                'abilities' => [
                    'view_purchases' => $user->can('subscriber.purchases.view'),
                    'view_events' => $user->can('subscriber.events.view'),
                    'view_subscription' => $user->can('subscriber.subscription.view'),
                    'update_profile' => $user->can('subscriber.profile.update'),
                ],
                'subscriptions' => $user->userSubscriptions->map(fn ($subscription) => [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'starts_at' => $subscription->starts_at?->toDateString(),
                    'ends_at' => $subscription->ends_at?->toDateString(),
                    'plan' => $subscription->plan ? [
                        'id' => $subscription->plan->id,
                        'name' => $subscription->plan->name,
                        'slug' => $subscription->plan->slug,
                        'price' => $subscription->plan->price,
                        'currency' => $subscription->plan->currency,
                    ] : null,
                ])->values(),
                'payments' => $user->subscriptionPayments->map(fn ($payment) => [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at?->toIso8601String(),
                    'expires_at' => $payment->expires_at?->toIso8601String(),
                ])->values(),
                'purchase_requests' => $user->purchaseRequests->map(fn ($purchaseRequest) => [
                    'uuid' => $purchaseRequest->uuid,
                    'request_type' => $purchaseRequest->request_type,
                    'preferred_channel' => $purchaseRequest->preferred_channel,
                    'status' => $purchaseRequest->status,
                    'created_at' => $purchaseRequest->created_at?->toIso8601String(),
                ])->values(),
            ],
        ]);
    }
}

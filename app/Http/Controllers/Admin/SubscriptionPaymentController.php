<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubscriptionPaymentController extends Controller
{
    /**
     * Muestra la pantalla administrativa de pagos de suscripción.
     */
    public function index()
    {
        Gate::authorize('subscription_payments.view');

        return view('admin.subscription-payments.index');
    }

    /**
     * Descarga el comprobante privado del registro.
     */
    public function proof(SubscriptionPayment $subscriptionPayment)
    {
        Gate::authorize('subscription_payments.view');

        abort_if(blank($subscriptionPayment->payment_proof_path), 404);

        $disk = (string) config('uploads.payment_proofs.disk', 'local');
        abort_unless(Storage::disk($disk)->exists($subscriptionPayment->payment_proof_path), 404);

        return Storage::disk($disk)->response(
            $subscriptionPayment->payment_proof_path,
            basename($subscriptionPayment->payment_proof_path)
        );
    }
}

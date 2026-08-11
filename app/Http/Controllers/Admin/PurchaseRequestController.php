<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PurchaseRequestController extends Controller
{
    /**
     * Muestra la pantalla administrativa de solicitudes de compra.
     */
    public function index()
    {
        Gate::authorize('purchase_requests.view');

        return view('admin.purchase-requests.index');
    }

    /**
     * Descarga el comprobante privado del registro.
     */
    public function proof(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('purchase_requests.view');

        abort_if(blank($purchaseRequest->payment_proof_path), 404);

        $disk = (string) config('uploads.payment_proofs.disk', 'local');
        abort_unless(Storage::disk($disk)->exists($purchaseRequest->payment_proof_path), 404);

        return Storage::disk($disk)->response(
            $purchaseRequest->payment_proof_path,
            basename($purchaseRequest->payment_proof_path)
        );
    }
}

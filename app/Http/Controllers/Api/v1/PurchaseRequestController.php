<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PurchaseRequest;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class PurchaseRequestController extends Controller
{
    /**
     * Registra una solicitud pública de contacto, compra o validación manual.
     */
    public function store(Request $request, FileUploadService $files)
    {
        $validated = $request->validate([
            'event_id' => ['nullable', 'integer', Rule::exists('events', 'id')->where('status', 1)],
            'event_slug' => ['nullable', 'string', 'max:220'],
            'subscription_plan_id' => ['nullable', 'integer', Rule::exists('subscription_plans', 'id')->where('status', 1)],
            'contact_name' => ['required', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email', 'max:150', 'required_without_all:contact_phone,contact_whatsapp'],
            'contact_phone' => ['nullable', 'string', 'max:30', 'required_without_all:contact_email,contact_whatsapp'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30', 'required_without_all:contact_email,contact_phone'],
            'preferred_channel' => ['required', Rule::in(['whatsapp', 'phone', 'email'])],
            'request_type' => ['required', Rule::in(['general_contact', 'event_ticket', 'subscription', 'payment_proof'])],
            'message' => ['nullable', 'string', 'max:2000'],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:'.((int) config('uploads.payment_proofs.max_mb', 5) * 1024)],
        ]);

        $eventId = $validated['event_id'] ?? null;
        if (! $eventId && filled($validated['event_slug'] ?? null)) {
            $eventId = Event::query()
                ->published()
                ->where('slug', $validated['event_slug'])
                ->value('id');
        }

        if (($validated['request_type'] ?? null) === 'event_ticket' && ! $eventId) {
            throw ValidationException::withMessages([
                'event_id' => [__('mma.api.purchase_requests.event_required')],
            ]);
        }

        $proof = [];
        if ($request->hasFile('payment_proof')) {
            try {
                $proof = $files->storePaymentProof(
                    $request->file('payment_proof'),
                    'purchase-request'
                );
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'payment_proof' => [$exception->getMessage()],
                ]);
            }
        }

        $purchaseRequest = PurchaseRequest::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $request->user()?->id,
            'event_id' => $eventId,
            'subscription_plan_id' => $validated['subscription_plan_id'] ?? null,
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_whatsapp' => $validated['contact_whatsapp'] ?? null,
            'preferred_channel' => $validated['preferred_channel'],
            'request_type' => $validated['request_type'],
            'message' => $validated['message'] ?? null,
            'payment_proof_path' => $proof['path'] ?? null,
            'payment_proof_mime' => $proof['mime'] ?? null,
            'payment_proof_size' => $proof['size'] ?? null,
            'status' => 0,
            'metadata' => [
                'source' => 'api',
                'event_slug' => $validated['event_slug'] ?? null,
                'proof_original_name' => $proof['original_name'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            ],
        ]);

        return response()->json([
            'message' => __('mma.api.purchase_requests.created'),
            'data' => [
                'uuid' => $purchaseRequest->uuid,
                'status' => $purchaseRequest->status,
                'request_type' => $purchaseRequest->request_type,
            ],
        ], 201);
    }
}

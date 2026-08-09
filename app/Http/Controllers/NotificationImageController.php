<?php

namespace App\Http\Controllers;

use App\Models\NotificationM;
use App\Models\User;
use App\Services\NotificationAccessService;
use App\Services\NotificationAttachmentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationImageController extends Controller
{
    public function __construct(
        private readonly NotificationAccessService $notificationAccessService,
        private readonly NotificationAttachmentService $notificationAttachmentService,
    ) {}

    // Entrega una imagen solo cuando la sesion web tiene acceso al aviso.
    public function show(Request $request, NotificationM $notification, string $slot): Response
    {
        abort_unless(
            $request->user() && $this->notificationAccessService->canView($request->user(), $notification),
            403
        );

        return $this->notificationAttachmentService->response($notification, $slot);
    }

    // Entrega una imagen movil mediante una URL firmada, temporal y vinculada al destinatario.
    public function showApi(
        NotificationM $notification,
        string $slot,
        User $user
    ): Response {
        abort_unless($this->notificationAccessService->canView($user, $notification), 403);

        return $this->notificationAttachmentService->response($notification, $slot);
    }
}

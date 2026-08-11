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
    /**
     * Inyecta las dependencias requeridas por la clase.
     */
    public function __construct(
        private readonly NotificationAccessService $notificationAccessService,
        private readonly NotificationAttachmentService $notificationAttachmentService,
    ) {}

    /**
     * Muestra el recurso solicitado cuando el usuario tiene acceso.
     */
    public function show(Request $request, NotificationM $notification, string $slot): Response
    {
        abort_unless(
            $request->user() && $this->notificationAccessService->canView($request->user(), $notification),
            403
        );

        return $this->notificationAttachmentService->response($notification, $slot);
    }

    /**
     * Entrega el recurso firmado para clientes API.
     */
    public function showApi(
        NotificationM $notification,
        string $slot,
        User $user
    ): Response {
        abort_unless($this->notificationAccessService->canView($user, $notification), 403);

        return $this->notificationAttachmentService->response($notification, $slot);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NotificationM;
use App\Models\NotificationReadM;
use App\Services\NotificationAccessService;
use App\Services\NotificationAttachmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationViewController extends Controller
{
    public function __construct(
        private readonly NotificationAccessService $notificationAccessService,
        private readonly NotificationAttachmentService $notificationAttachmentService,
    ) {}

    // Muestra el contenido completo del aviso despues de validar sesion y destinatario.
    public function show(Request $request, NotificationM $notification): View
    {
        abort_unless(
            $this->notificationAccessService->canView($request->user(), $notification),
            403
        );

        NotificationReadM::updateOrCreate(
            [
                'notification_id' => $notification->id,
                'user_id' => $request->user()->id,
            ],
            [
                'read_at' => now('America/La_Paz'),
            ]
        );

        $images = collect(array_keys(NotificationAttachmentService::SLOT_COLUMNS))
            ->map(fn (string $slot) => $this->notificationAttachmentService->webUrl($notification, $slot))
            ->filter()
            ->values();

        return view('notificationspush.show', compact('notification', 'images'));
    }

    // Simula el detalle que vera un usuario al abrir una notificacion push.
    public function preview(Request $request): View
    {
        abort_unless($this->notificationAccessService->canManage($request->user()), 403);

        $notification = new NotificationM;
        $notification->forceFill([
            'title' => 'Nuevo evento confirmado',
            'description' => "Ya está disponible la cartelera completa del próximo evento.\n\nRevisa la fecha, sede y combates confirmados, y no te pierdas la venta de entradas.",
            'reg_date' => now('America/La_Paz'),
            'deadline' => now('America/La_Paz')->addDays(3)->toDateString(),
            'link' => route('landing.home'),
        ]);

        $images = collect();

        return view('notificationspush.show', [
            'notification' => $notification,
            'images' => $images,
            'isPreview' => true,
            'backUrl' => route('notificationsp'),
        ]);
    }
}

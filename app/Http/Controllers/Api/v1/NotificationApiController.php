<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\NotificationM;
use App\Models\NotificationReadM;
use App\Models\User;
use App\Services\NotificationAttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationApiController extends Controller
{
    public function __construct(
        private readonly NotificationAttachmentService $notificationAttachmentService,
    ) {}

    public function getNotifications(Request $request)
    {
        try {
            $userPermission = UserPermissionVerif::verif(
                $request->userId,
                $request->email,
                $request->phone,
                $request->identifier
            );

            $page = max((int) $request->input('page', 1), 1);
            $perPage = min(max((int) $request->input('perPage', 20), 1), 20);

            if ($userPermission[0] !== 'yes') {
                return response()->json($this->emptyResponse(
                    false,
                    $page,
                    $perPage,
                    $userPermission,
                    'no'
                ));
            }

            $userId = (int) $request->userId;
            $user = User::findOrFail($userId);
            $now = Carbon::now('America/La_Paz');

            $baseQuery = $this->visibleNotificationQuery($userId, $now);

            $total = (clone $baseQuery)->count();
            $notifications = (clone $baseQuery)
                ->orderBy('scheduled_at', 'desc')
                ->orderBy('id', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            $readNotificationIds = NotificationReadM::query()
                ->where('user_id', $userId)
                ->whereIn('notification_id', $notifications->pluck('id'))
                ->pluck('notification_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $notifications = $notifications
                ->map(function (NotificationM $notification) use ($readNotificationIds, $user) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'description' => $notification->description,
                        'link' => $notification->link,
                        'image' => $this->notificationAttachmentService->temporaryApiUrl($notification, 'image', $user),
                        'image_1' => $this->notificationAttachmentService->temporaryApiUrl($notification, 'image_1', $user),
                        'image_2' => $this->notificationAttachmentService->temporaryApiUrl($notification, 'image_2', $user),
                        'reg_date' => optional($notification->reg_date)->toDateTimeString(),
                        'deadline' => optional($notification->deadline)->toDateString(),
                        'scheduled_at' => optional($notification->scheduled_at)->toDateTimeString(),
                        'state' => $notification->state,
                        'is_read' => in_array((int) $notification->id, $readNotificationIds, true),
                    ];
                })
                ->values();

            $hasMore = ($page * $perPage) < $total;

            return response()->json([
                'response' => true,
                'notifications' => $notifications,
                'failed' => 'no',
                'data_user' => $userPermission[3],
                'user_permission' => $userPermission[0],
                'permit_detail' => $userPermission[1],
                'type_subscriptions' => $userPermission[2],
                'current_page' => $page,
                'per_page' => $perPage,
                'next_page' => $hasMore ? $page + 1 : null,
                'has_more' => $hasMore,
                'total' => $total,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'notifications' => [],
                'failed' => $th->getMessage(),
                'data_user' => null,
                'user_permission' => 'no',
                'permit_detail' => '',
                'type_subscriptions' => null,
                'current_page' => (int) $request->input('page', 1),
                'per_page' => min(max((int) $request->input('perPage', 20), 1), 20),
                'next_page' => null,
                'has_more' => false,
                'total' => 0,
            ]);
        }
    }

    public function markNotificationAsRead(Request $request)
    {
        try {
            $userPermission = UserPermissionVerif::verif(
                $request->userId,
                $request->email,
                $request->phone,
                $request->identifier
            );

            if ($userPermission[0] !== 'yes') {
                return response()->json([
                    'response' => false,
                    'failed' => 'No autorizado.',
                    'data_user' => $userPermission[3] ?? null,
                    'user_permission' => $userPermission[0] ?? 'no',
                    'permit_detail' => $userPermission[1] ?? '',
                    'type_subscriptions' => $userPermission[2] ?? null,
                ]);
            }

            $request->validate([
                'notificationId' => ['required', 'integer', 'exists:notifications,id'],
            ]);

            $userId = (int) $request->userId;
            $notificationId = (int) $request->input('notificationId');
            $now = Carbon::now('America/La_Paz');

            $notification = $this->visibleNotificationQuery($userId, $now)
                ->where('id', $notificationId)
                ->first();

            if (! $notification) {
                return response()->json([
                    'response' => false,
                    'failed' => 'El aviso no esta disponible para este usuario.',
                    'data_user' => $userPermission[3] ?? null,
                    'user_permission' => $userPermission[0] ?? 'no',
                    'permit_detail' => $userPermission[1] ?? '',
                    'type_subscriptions' => $userPermission[2] ?? null,
                ]);
            }

            NotificationReadM::updateOrCreate(
                [
                    'notification_id' => $notification->id,
                    'user_id' => $userId,
                ],
                [
                    'read_at' => $now,
                ]
            );

            return response()->json([
                'response' => true,
                'failed' => 'no',
                'notification_id' => $notification->id,
                'read_at' => $now->toDateTimeString(),
                'data_user' => $userPermission[3] ?? null,
                'user_permission' => $userPermission[0] ?? 'yes',
                'permit_detail' => $userPermission[1] ?? '',
                'type_subscriptions' => $userPermission[2] ?? null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'failed' => $th->getMessage(),
                'notification_id' => (int) $request->input('notificationId', 0),
                'read_at' => null,
                'data_user' => null,
                'user_permission' => 'no',
                'permit_detail' => '',
                'type_subscriptions' => null,
            ]);
        }
    }

    private function visibleNotificationQuery(int $userId, Carbon $now)
    {
        return NotificationM::query()
            ->where('state', 1)
            ->where(function ($query) {
                $query->whereNull('delivery_platform')
                    ->orWhereIn('delivery_platform', ['all', 'mobile']);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', $now);
            })
            ->whereDate('deadline', '>=', $now->toDateString())
            ->where(function ($query) use ($userId) {
                $query->whereNull('metadata')
                    ->orWhereJsonLength('metadata->target_user_ids', 0)
                    ->orWhereJsonContains('metadata->target_user_ids', $userId);
            });
    }

    private function emptyResponse(
        bool $response,
        int $page,
        int $perPage,
        array $userPermission,
        string $failed
    ): array {
        return [
            'response' => $response,
            'notifications' => [],
            'failed' => $failed,
            'data_user' => $userPermission[3] ?? null,
            'user_permission' => $userPermission[0] ?? 'no',
            'permit_detail' => $userPermission[1] ?? '',
            'type_subscriptions' => $userPermission[2] ?? null,
            'current_page' => $page,
            'per_page' => $perPage,
            'next_page' => null,
            'has_more' => false,
            'total' => 0,
        ];
    }
}

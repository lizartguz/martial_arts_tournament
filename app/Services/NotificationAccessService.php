<?php

namespace App\Services;

use App\Models\NotificationM;
use App\Models\User;
use Illuminate\Support\Carbon;

class NotificationAccessService
{
    // Determina si el usuario puede administrar todos los avisos de marketing.
    /**
     * Valida si el usuario puede manage.
     */
    public function canManage(User $user): bool
    {
        return $user->can('notifications.view');
    }

    // Valida que el aviso este disponible y que el usuario sea uno de sus destinatarios.
    /**
     * Valida si el usuario puede view.
     */
    public function canView(User $user, NotificationM $notification): bool
    {
        if ((int) $user->state !== 1) {
            return false;
        }

        if ($this->canManage($user) || (int) $notification->creator_user_id === (int) $user->id) {
            return true;
        }

        $now = Carbon::now('America/La_Paz');

        if ((int) $notification->state !== 1) {
            return false;
        }

        if ($notification->scheduled_at && $notification->scheduled_at->isFuture()) {
            return false;
        }

        if ($notification->deadline && $notification->deadline->lt($now->startOfDay())) {
            return false;
        }

        $metadata = is_array($notification->metadata) ? $notification->metadata : [];
        $targetUserIds = collect($metadata['target_user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        return empty($targetUserIds) || in_array((int) $user->id, $targetUserIds, true);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NotificationM;
use App\Services\NotificationAttachmentService;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationPushC extends Controller
{
    public function __construct(
        private readonly NotificationAttachmentService $notificationAttachmentService,
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()?->can('notifications.view'), 403);

        return view('notificationspush.index');
    }

    public function saveNotificationForm(Request $request): RedirectResponse
    {
        $this->debugNotificationFormLog('POST /notificationsp/save recibido', [
            'input' => $request->except(['image', 'image_1', 'image_2']),
            'files' => [
                'image' => $request->hasFile('image'),
                'image_1' => $request->hasFile('image_1'),
                'image_2' => $request->hasFile('image_2'),
            ],
        ]);

        try {
            $validated = $request->validate([
                'notification_id' => ['nullable', 'integer', 'exists:notifications,id'],
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:500'],
                'deadline' => ['required', 'date'],
                'scheduled_at' => ['nullable', 'date'],
                'link' => ['nullable', 'url', 'max:300'],
                'state' => ['required', 'in:0,1'],
                'selected_user_ids' => ['nullable', 'array'],
                'selected_user_ids.*' => ['integer', 'exists:users,id'],
                'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'mimetypes:image/jpeg,image/png,image/gif,image/webp', 'max:10240'],
                'image_1' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'mimetypes:image/jpeg,image/png,image/gif,image/webp', 'max:10240'],
                'image_2' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'mimetypes:image/jpeg,image/png,image/gif,image/webp', 'max:10240'],
            ], [
                'title.required' => __('messages.notices.validation.title_required'),
                'description.required' => __('messages.notices.validation.description_required'),
                'description.max' => __('messages.notices.validation.description_max'),
                'deadline.required' => __('messages.notices.validation.deadline_required'),
                'link.url' => __('messages.notices.validation.link_url'),
                'image.mimes' => __('messages.notices.validation.image_mimes_1'),
                'image.max' => __('messages.notices.validation.image_max_1'),
                'image_1.mimes' => __('messages.notices.validation.image_mimes_2'),
                'image_1.max' => __('messages.notices.validation.image_max_2'),
                'image_2.mimes' => __('messages.notices.validation.image_mimes_3'),
                'image_2.max' => __('messages.notices.validation.image_max_3'),
            ]);

            if (! empty($validated['scheduled_at']) && Carbon::parse($validated['deadline'])->lt(Carbon::parse($validated['scheduled_at'])->startOfDay())) {
                $this->debugNotificationFormLog('Fecha invalida en POST de aviso', $validated);

                return back()
                    ->withInput()
                    ->with('error', __('messages.notices.alerts.deadline_before_scheduled'));
            }

            $storedImages = [];
            $imagesToDeleteAfterCommit = [];

            DB::beginTransaction();

            $notification = ! empty($validated['notification_id'])
                ? NotificationM::findOrFail($validated['notification_id'])
                : new NotificationM;

            $image = $notification->image;
            $image1 = $notification->image_1;
            $image2 = $notification->image_2;

            if ($request->hasFile('image')) {
                $oldImage = $image;
                $image = $this->notificationAttachmentService->store($request->file('image'), 'aviso');
                $storedImages[] = $image;
                $imagesToDeleteAfterCommit[] = $oldImage;
            }

            if ($request->hasFile('image_1')) {
                $oldImage1 = $image1;
                $image1 = $this->notificationAttachmentService->store($request->file('image_1'), 'aviso_1');
                $storedImages[] = $image1;
                $imagesToDeleteAfterCommit[] = $oldImage1;
            }

            if ($request->hasFile('image_2')) {
                $oldImage2 = $image2;
                $image2 = $this->notificationAttachmentService->store($request->file('image_2'), 'aviso_2');
                $storedImages[] = $image2;
                $imagesToDeleteAfterCommit[] = $oldImage2;
            }

            $targetUserIds = collect($validated['selected_user_ids'] ?? [])
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $notification->fill([
                'reg_date' => $notification->reg_date ?? now('America/La_Paz'),
                'title' => trim($validated['title']),
                'description' => trim($validated['description']),
                'deadline' => $validated['deadline'],
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'link' => $validated['link'] ?? null,
                'image' => $image,
                'image_1' => $image1,
                'image_2' => $image2,
                'type_user' => count($targetUserIds) > 0 ? 'individual' : 'all',
                'type' => 'normal',
                'metadata' => [
                    'target_user_ids' => $targetUserIds,
                ],
                'state' => (int) $validated['state'],
                'creator_user_id' => Auth::id(),
            ]);

            $this->debugNotificationFormLog('Antes de guardar aviso por POST', [
                'payload' => $notification->getAttributes(),
            ]);

            $notification->save();
            DB::commit();

            foreach ($imagesToDeleteAfterCommit as $path) {
                $this->notificationAttachmentService->delete($path);
            }

            $this->debugNotificationFormLog('Aviso guardado por POST', [
                'saved_id' => $notification->id,
                'stored_images' => $storedImages,
            ]);

            return redirect()
                ->route('notificationsp')
                ->with('success', ! empty($validated['notification_id']) ? __('messages.notices.alerts.updated') : __('messages.notices.alerts.saved'));
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            foreach ($storedImages ?? [] as $path) {
                $this->notificationAttachmentService->delete($path);
            }

            $this->debugNotificationFormLog('Error guardando aviso por POST', [
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
            ]);

            Log::error('Error guardando aviso por formulario POST.', [
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('messages.notices.alerts.save_failed', ['error' => $th->getMessage()]));
        }
    }

    public function toggleStatus(NotificationM $notification): RedirectResponse
    {
        try {
            $notification->update([
                'state' => (int) ! $notification->state,
            ]);

            return redirect()
                ->route('notificationsp')
                ->with('success', $notification->state === 1 ? __('messages.notices.alerts.activated') : __('messages.notices.alerts.deactivated'));
        } catch (\Throwable $th) {
            Log::error('Error al cambiar estado del aviso.', [
                'notification_id' => $notification->id,
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return redirect()
                ->route('notificationsp')
                ->with('error', __('messages.notices.alerts.status_failed_controller'));
        }
    }

    public function destroy(NotificationM $notification): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $this->deleteStoredImageIfExists($notification->image);
            $this->deleteStoredImageIfExists($notification->image_1);
            $this->deleteStoredImageIfExists($notification->image_2);
            $notification->delete();

            DB::commit();

            return redirect()
                ->route('notificationsp')
                ->with('success', __('messages.notices.alerts.deleted_controller'));
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Error al eliminar aviso.', [
                'notification_id' => $notification->id,
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return redirect()
                ->route('notificationsp')
                ->with('error', __('messages.notices.alerts.delete_failed_controller'));
        }
    }

    private function deleteStoredImageIfExists(?string $path): void
    {
        $this->notificationAttachmentService->delete($path);
    }

    private function debugNotificationFormLog(string $message, array $extra = []): void
    {
        try {
            $line = '['.now('America/La_Paz')->format('Y-m-d H:i:s').'] '.$message.' '.json_encode([
                'user_id' => Auth::id(),
                'extra' => $extra,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;

            file_put_contents(storage_path('logs/notifications_debug.log'), $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $th) {
            Log::error('No se pudo escribir notifications_debug.log desde controller.', [
                'error' => $th->getMessage(),
                'message' => $message,
            ]);
        }
    }

    public function sendNotification(Request $request)
    {
        $result = app(PushNotificationService::class)->sendToUsers(
            [1061, 1062],
            'all',
            'Actualización Disponible!',
            'Ve a la tienda Play Store y actualiza las nuevas funciones!'
        );

        return response()->json($result);
    }
}

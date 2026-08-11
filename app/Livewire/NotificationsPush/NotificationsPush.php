<?php

namespace App\Livewire\NotificationsPush;

use App\Models\NotificationM;
use App\Models\User;
use App\Services\NotificationAttachmentService;
use App\Services\PushNotificationService;
use App\Support\ErrorMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class NotificationsPush extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';

    public $perPage = 10;

    public $searchTitle = null;

    public $sortColumn = 'id';

    public $sortDirection = 'desc';

    public $formAdd = false;

    public $formUpdate = false;

    public $roleOptions = [];

    public $roleFilter = '';

    public $userSearch = '';

    public $userSearchResults = [];

    public $selectedUsersPreview = [];

    public $idAU = null;

    public $titleAU = null;

    public $descriptionAU = null;

    public $deadlineAU = null;

    public $scheduledAtAU = null;

    public $linkAU = null;

    public $stateAU = 1;

    public $deliveryPlatformAU = 'all';

    public $selectedUserIds = [];

    public $imageAU = null;

    public $image1AU = null;

    public $image2AU = null;

    public $currentImage = null;

    public $currentImage1 = null;

    public $currentImage2 = null;

    protected $listeners = ['deleteNotif'];

    public function mount()
    {
        abort_unless(Auth::user()?->can('notifications.view'), 403);
        $this->loadRoleOptions();
    }

    public function updatingSearchTitle()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function visibleAdd()
    {
        $this->clearVariable();
        $this->formAdd = true;
        $this->formUpdate = false;
        $this->stateAU = 1;
        $this->refreshUserSearchResults();
    }

    public function noVisibleAdd()
    {
        $this->clearVariable();
    }

    public function editNotif($id)
    {
        $notification = NotificationM::find($id);
        if (! $notification) {
            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.not_found')]);

            return;
        }

        $metadata = is_array($notification->metadata) ? $notification->metadata : [];
        $this->idAU = $notification->id;
        $this->titleAU = $notification->title;
        $this->descriptionAU = $notification->description;
        $this->deadlineAU = optional($notification->deadline)->format('Y-m-d') ?? $notification->deadline;
        $this->scheduledAtAU = optional($notification->scheduled_at)->format('Y-m-d\TH:i');
        $this->linkAU = $notification->link;
        $this->stateAU = (int) $notification->state;
        $this->deliveryPlatformAU = $notification->delivery_platform ?: 'all';
        $this->selectedUserIds = collect($metadata['target_user_ids'] ?? [])
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
        $this->refreshSelectedUsersPreview();
        $this->refreshUserSearchResults();
        $this->currentImage = $notification->image;
        $this->currentImage1 = $notification->image_1;
        $this->currentImage2 = $notification->image_2;
        $this->imageAU = null;
        $this->image1AU = null;
        $this->image2AU = null;
        $this->formAdd = true;
        $this->formUpdate = true;
    }

    // Recalcula las coincidencias cada vez que cambia el texto de busqueda.
    public function updatedUserSearch()
    {
        $this->refreshUserSearchResults();
    }

    // Aplica el filtro por rol sin recargar todos los usuarios en memoria.
    public function updatedRoleFilter()
    {
        $this->refreshUserSearchResults();
    }

    // Agrega un usuario al listado destino evitando duplicados.
    public function addSelectedUser(int $userId)
    {
        $userExists = User::query()
            ->where('state', 1)
            ->whereKey($userId)
            ->when($this->roleFilter !== '', function ($query) {
                $query->role($this->roleFilter);
            })
            ->exists();

        if (! $userExists) {
            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.user_unavailable')]);
            $this->refreshUserSearchResults();

            return;
        }

        $this->selectedUserIds = collect($this->selectedUserIds)
            ->push((string) $userId)
            ->unique()
            ->values()
            ->all();

        $this->refreshSelectedUsersPreview();
        $this->refreshUserSearchResults();
    }

    // Quita un usuario de la segmentacion del aviso.
    public function removeSelectedUser(int $userId)
    {
        $this->selectedUserIds = collect($this->selectedUserIds)
            ->reject(fn ($id) => (int) $id === $userId)
            ->values()
            ->all();

        $this->refreshSelectedUsersPreview();
        $this->refreshUserSearchResults();
    }

    public function updatedImageAU()
    {
        $this->validateUploadedImage('imageAU');
    }

    public function updatedImage1AU()
    {
        $this->validateUploadedImage('image1AU');
    }

    public function updatedImage2AU()
    {
        $this->validateUploadedImage('image2AU');
    }

    public function saveNotification()
    {
        $this->debugNotificationLog('saveNotification llamado');
        Log::info('Intento de guardar aviso iniciado.', $this->notificationLogContext());

        try {
            $this->validate([
                'titleAU' => ['required', 'string', 'max:255'],
                'descriptionAU' => ['required', 'string', 'max:500'],
                'deadlineAU' => ['required', 'date'],
                'scheduledAtAU' => ['nullable', 'date'],
                'linkAU' => ['nullable', 'url', 'max:300'],
                'stateAU' => ['required', 'in:0,1'],
                'deliveryPlatformAU' => ['required', 'in:all,mobile,web'],
                'selectedUserIds' => ['array'],
                'selectedUserIds.*' => ['integer', 'exists:users,id'],
                ...$this->imageValidationRules(),
            ], $this->validationMessages());
        } catch (ValidationException $exception) {
            $this->debugNotificationLog('Validacion fallida al guardar aviso', [
                'errors' => $exception->errors(),
            ]);
            Log::warning('Validacion fallida al guardar aviso.', [
                ...$this->notificationLogContext(),
                'errors' => $exception->errors(),
            ]);
            throw $exception;
        } catch (\Throwable $th) {
            $this->debugNotificationLog('Error inesperado validando aviso', [
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
            Log::error('Error inesperado validando aviso.', [
                ...$this->notificationLogContext(),
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
            $this->dispatch('failedAlert', ['failed' => ErrorMessage::userSeesTechnicalDetail()
                ? __('messages.notices.alerts.validate_failed', ['error' => $th->getMessage()])
                : __('messages.operation_errors.notice_validate')]);

            return;
        }

        $this->debugNotificationLog('Validacion correcta al guardar aviso');
        Log::info('Validacion correcta al guardar aviso.', $this->notificationLogContext());

        if (! empty($this->scheduledAtAU) && Carbon::parse($this->deadlineAU)->lt(Carbon::parse($this->scheduledAtAU)->startOfDay())) {
            $this->debugNotificationLog('Fecha de expiracion menor que fecha programada');
            Log::warning('Fecha de expiracion menor que fecha programada al guardar aviso.', $this->notificationLogContext());
            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.deadline_before_scheduled')]);

            return;
        }

        $storedImages = [];
        $imagesToDeleteAfterCommit = [];
        $committed = false;
        DB::beginTransaction();
        try {
            $notification = $this->formUpdate
                ? NotificationM::findOrFail($this->idAU)
                : new NotificationM;

            $image = $notification->image;
            $image1 = $notification->image_1;
            $image2 = $notification->image_2;

            if ($this->imageAU) {
                $this->debugNotificationLog('Procesando imagen principal');
                Log::info('Procesando imagen principal del aviso.', $this->notificationLogContext());
                $oldImage = $image;
                $image = $this->storeOptimizedImage($this->imageAU, 'aviso');
                $storedImages[] = $image;
                $imagesToDeleteAfterCommit[] = $oldImage;
            }

            if ($this->image1AU) {
                $this->debugNotificationLog('Procesando imagen 1');
                Log::info('Procesando imagen 1 del aviso.', $this->notificationLogContext());
                $oldImage1 = $image1;
                $image1 = $this->storeOptimizedImage($this->image1AU, 'aviso_1');
                $storedImages[] = $image1;
                $imagesToDeleteAfterCommit[] = $oldImage1;
            }

            if ($this->image2AU) {
                $this->debugNotificationLog('Procesando imagen 2');
                Log::info('Procesando imagen 2 del aviso.', $this->notificationLogContext());
                $oldImage2 = $image2;
                $image2 = $this->storeOptimizedImage($this->image2AU, 'aviso_2');
                $storedImages[] = $image2;
                $imagesToDeleteAfterCommit[] = $oldImage2;
            }

            $targetUserIds = collect($this->selectedUserIds)
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $notification->fill([
                'reg_date' => $notification->reg_date ?? now('America/La_Paz'),
                'title' => trim($this->titleAU),
                'description' => trim($this->descriptionAU),
                'deadline' => $this->deadlineAU,
                'scheduled_at' => $this->scheduledAtAU ?: null,
                'link' => $this->linkAU ?: null,
                'image' => $image,
                'image_1' => $image1,
                'image_2' => $image2,
                'type_user' => count($targetUserIds) > 0 ? 'individual' : 'all',
                'type' => 'both',
                'delivery_platform' => $this->deliveryPlatformAU,
                'push_last_error_at' => $notification->push_sent_at ? $notification->push_last_error_at : null,
                'push_last_error_message' => $notification->push_sent_at ? $notification->push_last_error_message : null,
                'metadata' => [
                    'target_user_ids' => $targetUserIds,
                ],
                'state' => (int) $this->stateAU,
                'creator_user_id' => Auth::id(),
            ]);
            $wasUpdate = $this->formUpdate;
            $this->debugNotificationLog('Antes de persistir aviso en BD', [
                'payload' => $notification->getAttributes(),
            ]);
            Log::info('Antes de persistir aviso en base de datos.', [
                ...$this->notificationLogContext(),
                'payload' => $notification->getAttributes(),
            ]);
            $notification->save();

            DB::commit();
            $committed = true;
            $this->debugNotificationLog('Aviso guardado correctamente', [
                'saved_id' => $notification->id,
                'stored_images' => $storedImages,
            ]);
            Log::info('Aviso guardado correctamente.', [
                ...$this->notificationLogContext(),
                'saved_id' => $notification->id,
                'stored_images' => $storedImages,
            ]);

            if ($this->shouldSendPushNow($notification)) {
                try {
                    app(PushNotificationService::class)->sendNotificationRecord($notification->fresh());
                } catch (\Throwable $pushException) {
                    $notification->forceFill([
                        'push_last_error_at' => Carbon::now('America/La_Paz'),
                        'push_last_error_message' => mb_substr($pushException->getMessage(), 0, 500),
                    ])->save();

                    Log::error('El aviso se guardo, pero Firebase no pudo enviar el push.', [
                        ...$this->notificationLogContext(),
                        'notification_id' => $notification->id,
                        'error' => $pushException->getMessage(),
                    ]);
                }
            }

            foreach ($imagesToDeleteAfterCommit as $path) {
                $this->deleteStoredImage($path);
            }
            $this->clearVariable();

            // Mantiene la ruta del modulo limpia sin repetir el envio ni recargar la pagina.
            $this->js('window.history.replaceState({}, document.title, window.location.pathname)');
            $this->dispatch('successAlert', ['success' => $wasUpdate ? __('messages.notices.alerts.updated') : __('messages.notices.alerts.saved')]);
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            if (! $committed) {
                foreach ($storedImages as $path) {
                    $this->deleteStoredImage($path);
                }
            }
            $this->debugNotificationLog('Error al guardar aviso', [
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
                'stored_images' => $storedImages,
            ]);
            Log::error('Error al guardar aviso.', [
                ...$this->notificationLogContext(),
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
            ]);
            if ($th instanceof \InvalidArgumentException) {
                // Mensaje de validacion de negocio: visible para todos los roles.
                $message = $th->getMessage();
            } else {
                // Error tecnico: detalle crudo solo para roles privilegiados.
                $message = ErrorMessage::userSeesTechnicalDetail()
                    ? __('messages.notices.alerts.save_failed', ['error' => $th->getMessage()])
                    : __('messages.operation_errors.notice_save');
            }
            $this->dispatch('failedAlert', ['failed' => $message]);
        }
    }

    public function deleteNotif($payload)
    {
        $id = is_array($payload) ? ($payload['id'] ?? null) : $payload;
        DB::beginTransaction();
        try {
            $notification = NotificationM::findOrFail($id);
            $this->deleteStoredImage($notification->image);
            $this->deleteStoredImage($notification->image_1);
            $this->deleteStoredImage($notification->image_2);
            $notification->delete();
            DB::commit();
            $this->dispatch('successAlert', ['success' => __('messages.notices.alerts.deleted')]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.delete_failed')]);
        }
    }

    public function changeStatus($id, $estadoActual)
    {
        try {
            $newState = $estadoActual == 1 ? 0 : 1;
            NotificationM::where('id', $id)->update(['state' => $newState]);

            if ($newState === 1) {
                $notification = NotificationM::find($id);

                if ($notification && $this->shouldSendPushNow($notification)) {
                    app(PushNotificationService::class)->sendNotificationRecord($notification);
                }
            }
        } catch (\Throwable $th) {
            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.status_failed')]);
        }
    }

    // Reenvia un aviso existente y bloquea solicitudes simultaneas para evitar envios duplicados.
    public function resendPush(int $id): void
    {
        $lock = Cache::lock("notification-push-resend:{$id}", 300);

        if (! $lock->get()) {
            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_in_progress')]);

            return;
        }

        try {
            $notification = NotificationM::find($id);

            if (! $notification) {
                $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_not_found')]);

                return;
            }

            $now = Carbon::now('America/La_Paz');

            if ((int) $notification->state !== 1) {
                $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_inactive')]);

                return;
            }

            if ($notification->scheduled_at && $notification->scheduled_at->gt($now)) {
                $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_not_available')]);

                return;
            }

            if ($notification->deadline && $notification->deadline->lt($now->copy()->startOfDay())) {
                $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_expired')]);

                return;
            }

            $result = app(PushNotificationService::class)->sendNotificationRecord($notification);
            $successCount = (int) ($result['success_count'] ?? 0);
            $failureCount = (int) ($result['failure_count'] ?? 0);

            if ($successCount > 0) {
                $message = __('messages.notices.alerts.resend_success', ['count' => $successCount]);

                if ($failureCount > 0) {
                    $message .= ' '.__('messages.notices.alerts.resend_partial', ['count' => $failureCount]);
                }

                $this->dispatch('successAlert', ['success' => $message]);

                return;
            }

            if ($failureCount > 0) {
                $error = $result['errors'][0] ?? __('messages.notices.alerts.resend_firebase_failed');
                $this->dispatch('failedAlert', ['failed' => $error]);

                return;
            }

            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_no_devices')]);
        } catch (\Throwable $th) {
            Log::error('No se pudo reenviar el aviso mediante Firebase.', [
                'notification_id' => $id,
                'user_id' => Auth::id(),
                'error' => $th->getMessage(),
            ]);

            $this->dispatch('failedAlert', ['failed' => __('messages.notices.alerts.resend_error')]);
        } finally {
            $lock->release();
        }
    }

    public function clearVariable()
    {
        $this->reset([
            'idAU',
            'titleAU',
            'descriptionAU',
            'deadlineAU',
            'scheduledAtAU',
            'linkAU',
            'deliveryPlatformAU',
            'selectedUserIds',
            'imageAU',
            'image1AU',
            'image2AU',
            'currentImage',
            'currentImage1',
            'currentImage2',
            'roleFilter',
            'userSearch',
            'userSearchResults',
            'selectedUsersPreview',
        ]);
        $this->stateAU = 1;
        $this->deliveryPlatformAU = 'all';
        $this->formAdd = false;
        $this->formUpdate = false;
        $this->resetValidation();
    }

    public function getImageUrl(string $slot): ?string
    {
        if (! $this->idAU) {
            return null;
        }

        $notification = NotificationM::find($this->idAU);

        return $notification
            ? app(NotificationAttachmentService::class)->webUrl($notification, $slot)
            : null;
    }

    private function deleteStoredImage($path)
    {
        app(NotificationAttachmentService::class)->delete($path);
    }

    private function storeOptimizedImage($image, string $prefix): string
    {
        return app(NotificationAttachmentService::class)->store($image, $prefix);
    }

    private function validateUploadedImage(string $property): void
    {
        try {
            $this->validateOnly($property, $this->imageValidationRules(), $this->validationMessages());
            $this->resetErrorBag($property);
            $this->debugNotificationLog('Imagen validada correctamente', [
                'property' => $property,
            ]);
        } catch (ValidationException $exception) {
            $this->debugNotificationLog('Validacion fallida al cargar imagen', [
                'property' => $property,
                'errors' => $exception->errors(),
            ]);
            $this->reset($property);
            throw $exception;
        }
    }

    private function imageValidationRules(): array
    {
        $rules = ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'mimetypes:image/jpeg,image/png,image/gif,image/webp', 'max:10240'];

        return [
            'imageAU' => $rules,
            'image1AU' => $rules,
            'image2AU' => $rules,
        ];
    }

    private function validationMessages(): array
    {
        return [
            'titleAU.required' => __('messages.notices.validation.title_required'),
            'descriptionAU.required' => __('messages.notices.validation.description_required'),
            'descriptionAU.max' => __('messages.notices.validation.description_max'),
            'deadlineAU.required' => __('messages.notices.validation.deadline_required'),
            'linkAU.url' => __('messages.notices.validation.link_url'),
            'deliveryPlatformAU.required' => __('messages.notices.validation.delivery_required'),
            'imageAU.mimes' => __('messages.notices.validation.image_mimes_1'),
            'imageAU.mimetypes' => __('messages.notices.validation.image_mimes_1'),
            'imageAU.max' => __('messages.notices.validation.image_max_1'),
            'image1AU.mimes' => __('messages.notices.validation.image_mimes_2'),
            'image1AU.mimetypes' => __('messages.notices.validation.image_mimes_2'),
            'image1AU.max' => __('messages.notices.validation.image_max_2'),
            'image2AU.mimes' => __('messages.notices.validation.image_mimes_3'),
            'image2AU.mimetypes' => __('messages.notices.validation.image_mimes_3'),
            'image2AU.max' => __('messages.notices.validation.image_max_3'),
        ];
    }

    private function notificationLogContext(): array
    {
        return [
            'notification_id' => $this->idAU,
            'form_update' => $this->formUpdate,
            'user_id' => Auth::id(),
            'title' => $this->titleAU,
            'description_length' => is_string($this->descriptionAU) ? strlen($this->descriptionAU) : null,
            'deadline' => $this->deadlineAU,
            'scheduled_at' => $this->scheduledAtAU,
            'link' => $this->linkAU,
            'state' => $this->stateAU,
            'delivery_platform' => $this->deliveryPlatformAU,
            'selected_user_ids' => $this->selectedUserIds,
            'has_image' => (bool) $this->imageAU,
            'has_image_1' => (bool) $this->image1AU,
            'has_image_2' => (bool) $this->image2AU,
            'image_size' => $this->uploadedImageSize($this->imageAU),
            'image_1_size' => $this->uploadedImageSize($this->image1AU),
            'image_2_size' => $this->uploadedImageSize($this->image2AU),
            'current_image' => $this->currentImage,
            'current_image_1' => $this->currentImage1,
            'current_image_2' => $this->currentImage2,
        ];
    }

    private function uploadedImageSize($image): ?int
    {
        if (! $image || ! method_exists($image, 'getSize')) {
            return null;
        }

        try {
            return $image->getSize();
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function debugNotificationLog(string $message, array $extra = []): void
    {
        try {
            $line = '['.now('America/La_Paz')->format('Y-m-d H:i:s').'] '.$message.' '.json_encode([
                'context' => $this->notificationLogContext(),
                'extra' => $extra,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;

            file_put_contents(storage_path('logs/notifications_debug.log'), $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $th) {
            Log::error('No se pudo escribir notifications_debug.log.', [
                'error' => $th->getMessage(),
                'message' => $message,
            ]);
        }
    }

    // Carga una sola vez los roles disponibles para filtrar usuarios destino.
    private function loadRoleOptions(): void
    {
        $this->roleOptions = Role::query()
            ->orderBy('name')
            ->get(['name'])
            ->map(fn ($role) => [
                'value' => $role->name,
                'label' => $role->name,
            ])
            ->all();
    }

    // Busca usuarios por coincidencias y limita el resultado para evitar listas masivas.
    private function refreshUserSearchResults(): void
    {
        $term = trim((string) $this->userSearch);
        $normalizedSelectedIds = collect($this->selectedUserIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        if ($term === '' && $this->roleFilter === '') {
            $this->userSearchResults = [];

            return;
        }

        $users = User::query()
            ->with('roles:id,name')
            ->where('state', 1)
            ->when($this->roleFilter !== '', function ($query) {
                $query->role($this->roleFilter);
            })
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($subQuery) use ($term) {
                    $likeTerm = '%'.$term.'%';
                    $subQuery->where('name', 'LIKE', $likeTerm)
                        ->orWhere('lastname', 'LIKE', $likeTerm)
                        ->orWhere('email', 'LIKE', $likeTerm)
                        ->orWhere('username', 'LIKE', $likeTerm);
                });
            })
            ->when(! empty($normalizedSelectedIds), function ($query) use ($normalizedSelectedIds) {
                $query->whereNotIn('id', $normalizedSelectedIds);
            })
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'lastname', 'email', 'username']);

        $this->userSearchResults = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'label' => $this->formatUserLabel($user),
                'email' => $user->email,
                'username' => $user->username,
                'roles' => $user->roles->pluck('name')->values()->all(),
            ];
        })->all();
    }

    // Prepara la visualizacion de los usuarios ya seleccionados al crear o editar un aviso.
    private function refreshSelectedUsersPreview(): void
    {
        $selectedIds = collect($this->selectedUserIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($selectedIds)) {
            $this->selectedUsersPreview = [];

            return;
        }

        $users = User::query()
            ->with('roles:id,name')
            ->whereIn('id', $selectedIds)
            ->get(['id', 'name', 'lastname', 'email', 'username'])
            ->sortBy(function ($user) use ($selectedIds) {
                return array_search($user->id, $selectedIds, true);
            })
            ->values();

        $this->selectedUsersPreview = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'label' => $this->formatUserLabel($user),
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->values()->all(),
            ];
        })->all();
    }

    // Homogeneiza el texto mostrado para un usuario en resultados y badges.
    private function formatUserLabel(User $user): string
    {
        $fullName = trim(($user->name ?? '').' '.($user->lastname ?? ''));

        return $fullName !== ''
            ? $fullName
            : ($user->username ?: $user->email ?: 'Usuario #'.$user->id);
    }

    // Determina si el aviso ya está listo para disparar un push inmediato o programado vencido.
    private function shouldSendPushNow(NotificationM $notification): bool
    {
        $now = Carbon::now('America/La_Paz');

        return (int) $notification->state === 1
            && $notification->push_sent_at === null
            && (! $notification->scheduled_at || $notification->scheduled_at->lte($now))
            && (! $notification->deadline || $notification->deadline->gte($now->toDateString()));
    }

    private function resultData()
    {
        $searchTitle = trim((string) $this->searchTitle);

        return NotificationM::query()
            ->with('user')
            ->when($searchTitle !== '', function ($query) use ($searchTitle) {
                $query->where(function ($subQuery) use ($searchTitle) {
                    $subQuery->where('title', 'LIKE', '%'.$searchTitle.'%')
                        ->orWhere('description', 'LIKE', '%'.$searchTitle.'%');
                });
            })
            ->when($this->stateChange !== 'all', function ($query) {
                $query->where('state', (int) $this->stateChange);
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.notifications-push.notifications-push', [
            'data' => $this->resultData(),
        ]);
    }
}

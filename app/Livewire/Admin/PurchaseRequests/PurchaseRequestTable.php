<?php

namespace App\Livewire\Admin\PurchaseRequests;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseRequestTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $status = '';
    public string $requestType = '';
    public string $preferredChannel = '';
    public string $assignedTo = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public array $selectedSummary = [];
    public array $form = [
        'status' => 0,
        'assigned_to' => null,
        'notes' => '',
    ];

    /**
     * Inicializa el componente de solicitudes de compra.
     */
    public function mount(): void
    {
        Gate::authorize('purchase_requests.view');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el estado.
     */
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el tipo de solicitud.
     */
    public function updatingRequestType(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el canal preferido.
     */
    public function updatingPreferredChannel(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el responsable.
     */
    public function updatingAssignedTo(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar la fecha inicial.
     */
    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar la fecha final.
     */
    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el tamaño de página.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Gestiona manage dentro de la tabla de solicitudes de compra.
     */
    public function manage(int $id): void
    {
        Gate::authorize('purchase_requests.update');

        $purchaseRequest = PurchaseRequest::query()
            ->with(['event:id,name,slug,starts_at', 'plan:id,name,slug', 'assignedUser:id,name,lastname,email', 'user:id,name,lastname,email'])
            ->findOrFail($id);

        $this->editingId = $purchaseRequest->id;
        $this->form = [
            'status' => (int) $purchaseRequest->status,
            'assigned_to' => $purchaseRequest->assigned_to,
            'notes' => $purchaseRequest->notes ?? '',
        ];
        $this->selectedSummary = [
            'uuid' => $purchaseRequest->uuid,
            'contact_name' => $purchaseRequest->contact_name,
            'contact_email' => $purchaseRequest->contact_email,
            'contact_phone' => $purchaseRequest->contact_phone,
            'contact_whatsapp' => $purchaseRequest->contact_whatsapp,
            'request_type' => $purchaseRequest->request_type,
            'preferred_channel' => $purchaseRequest->preferred_channel,
            'event' => $purchaseRequest->event?->name,
            'plan' => $purchaseRequest->plan?->name,
            'message' => $purchaseRequest->message,
            'payment_proof_path' => $purchaseRequest->payment_proof_path,
            'payment_proof_size' => $purchaseRequest->payment_proof_size,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        Gate::authorize('purchase_requests.update');

        if (! $this->editingId) {
            return;
        }

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $purchaseRequest = PurchaseRequest::query()->findOrFail($this->editingId);
        $newStatus = (int) $validated['status'];

        $purchaseRequest->fill([
            'status' => $newStatus,
            'assigned_to' => filled($validated['assigned_to'] ?? null) ? (int) $validated['assigned_to'] : null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if (in_array($newStatus, [2, 3, 4, 5], true)) {
            $purchaseRequest->handled_by = auth()->id();
            $purchaseRequest->handled_at = now();
        }

        $purchaseRequest->save();

        $this->dispatch('successAlert', ['success' => __('mma.admin.purchase_requests.messages.updated')]);
        $this->closeModal();
    }

    /**
     * Gestiona assign to me dentro de la tabla de solicitudes de compra.
     */
    public function assignToMe(int $id): void
    {
        Gate::authorize('purchase_requests.assign');

        PurchaseRequest::query()->findOrFail($id)->update([
            'assigned_to' => auth()->id(),
        ]);

        $this->dispatch('successAlert', ['success' => __('mma.admin.purchase_requests.messages.assigned')]);
    }

    /**
     * Gestiona close request dentro de la tabla de solicitudes de compra.
     */
    public function closeRequest(int $id): void
    {
        Gate::authorize('purchase_requests.close');

        PurchaseRequest::query()->findOrFail($id)->update([
            'status' => 4,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        $this->dispatch('successAlert', ['success' => __('mma.admin.purchase_requests.messages.closed')]);
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('purchase_requests.delete');

        $purchaseRequest = PurchaseRequest::query()->findOrFail($id);
        $this->pendingDeleteId = $purchaseRequest->id;
        $this->deleteName = $purchaseRequest->contact_name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('purchase_requests.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $purchaseRequest = PurchaseRequest::query()->findOrFail($this->pendingDeleteId);

        if (filled($purchaseRequest->payment_proof_path)) {
            Storage::disk((string) config('uploads.payment_proofs.disk', 'local'))
                ->delete($purchaseRequest->payment_proof_path);
        }

        $purchaseRequest->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.purchase_requests.messages.deleted')]);
    }

    /**
     * Cierra el modal principal y limpia su estado.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Cierra el modal de eliminación.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
    }

    /**
     * Limpia filtros y reinicia la paginación.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'requestType', 'preferredChannel', 'assignedTo', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones de estado permitidas.
     */
    public function statusOptions(): array
    {
        return [
            0 => __('mma.admin.purchase_requests.status.pending'),
            1 => __('mma.admin.purchase_requests.status.in_review'),
            2 => __('mma.admin.purchase_requests.status.contacted'),
            3 => __('mma.admin.purchase_requests.status.converted'),
            4 => __('mma.admin.purchase_requests.status.closed'),
            5 => __('mma.admin.purchase_requests.status.rejected'),
        ];
    }

    /**
     * Devuelve los tipos de solicitud permitidos.
     */
    public function requestTypeOptions(): array
    {
        return [
            'general_contact' => __('mma.admin.purchase_requests.request_types.general_contact'),
            'event_ticket' => __('mma.admin.purchase_requests.request_types.event_ticket'),
            'subscription' => __('mma.admin.purchase_requests.request_types.subscription'),
            'payment_proof' => __('mma.admin.purchase_requests.request_types.payment_proof'),
        ];
    }

    /**
     * Devuelve las opciones disponibles de channel.
     */
    public function channelOptions(): array
    {
        return [
            'whatsapp' => __('mma.admin.purchase_requests.channels.whatsapp'),
            'phone' => __('mma.admin.purchase_requests.channels.phone'),
            'email' => __('mma.admin.purchase_requests.channels.email'),
        ];
    }

    /**
     * Traduce el estado del registro.
     */
    public function statusLabel(int $status): string
    {
        return $this->statusOptions()[$status] ?? (string) $status;
    }

    /**
     * Define la clase visual según el estado.
     */
    public function statusBadge(int $status): string
    {
        return [
            0 => 'tw-badge-gray',
            1 => 'tw-badge-blue',
            2 => 'tw-badge-blue',
            3 => 'tw-badge-green',
            4 => 'tw-badge-gray',
            5 => 'tw-badge-red',
        ][$status] ?? 'tw-badge-gray';
    }

    /**
     * Devuelve la etiqueta visible de request type.
     */
    public function requestTypeLabel(?string $requestType): string
    {
        return $this->requestTypeOptions()[$requestType] ?? __('mma.admin.common.not_available');
    }

    /**
     * Devuelve la etiqueta visible de channel.
     */
    public function channelLabel(?string $channel): string
    {
        return $this->channelOptions()[$channel] ?? __('mma.admin.common.not_available');
    }

    /**
     * Formatea el tamaño del archivo para mostrarlo en pantalla.
     */
    public function fileSizeLabel(?int $bytes): string
    {
        if (! $bytes) {
            return __('mma.admin.common.not_available');
        }

        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 2).' MB'
            : number_format($bytes / 1024, 1).' KB';
    }

    /**
     * Renderiza la tabla de solicitudes de compra con filtros activos.
     */
    public function render()
    {
        $purchaseRequests = PurchaseRequest::query()
            ->with([
                'event:id,name,slug,starts_at',
                'plan:id,name,slug',
                'assignedUser:id,name,lastname,email',
                'user:id,name,lastname,email',
            ])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('uuid', 'like', "%{$this->search}%")
                        ->orWhere('contact_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_email', 'like', "%{$this->search}%")
                        ->orWhere('contact_phone', 'like', "%{$this->search}%")
                        ->orWhere('contact_whatsapp', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->requestType !== '', fn ($query) => $query->where('request_type', $this->requestType))
            ->when($this->preferredChannel !== '', fn ($query) => $query->where('preferred_channel', $this->preferredChannel))
            ->when($this->assignedTo === 'unassigned', fn ($query) => $query->whereNull('assigned_to'))
            ->when($this->assignedTo === 'me', fn ($query) => $query->where('assigned_to', auth()->id()))
            ->when(ctype_digit($this->assignedTo), fn ($query) => $query->where('assigned_to', (int) $this->assignedTo))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate($this->perPage);

        $assignableUsers = User::query()
            ->role(['super_manager', 'admin', 'manager', 'sales'])
            ->select(['id', 'name', 'lastname', 'email'])
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        return view('livewire.admin.purchase-requests.purchase-request-table', compact('purchaseRequests', 'assignableUsers'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'form.notes' => ['nullable', 'string', 'max:3000'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.status' => __('mma.admin.purchase_requests.form.status'),
            'form.assigned_to' => __('mma.admin.purchase_requests.form.assigned_to'),
            'form.notes' => __('mma.admin.purchase_requests.form.notes'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->selectedSummary = [];
        $this->form = [
            'status' => 0,
            'assigned_to' => null,
            'notes' => '',
        ];
        $this->resetErrorBag();
    }
}

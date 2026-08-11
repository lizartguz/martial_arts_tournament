<?php

namespace App\Livewire\Admin\Subscribers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriberTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $state = '';
    public string $subscriptionStatus = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public array $form = [
        'name' => '',
        'lastname' => '',
        'email' => '',
        'number_phone' => '',
        'identity_document' => '',
        'state' => 1,
    ];

    /**
     * Inicializa el componente de suscriptores.
     */
    public function mount(): void
    {
        Gate::authorize('subscribers.view');
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
    public function updatingState(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el estado de suscripción.
     */
    public function updatingSubscriptionStatus(): void
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
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('subscribers.update');

        $subscriber = User::role('subscriber')->findOrFail($id);

        $this->editingId = $subscriber->id;
        $this->form = [
            'name' => $subscriber->name,
            'lastname' => $subscriber->lastname ?? '',
            'email' => $subscriber->email,
            'number_phone' => $subscriber->number_phone ?? '',
            'identity_document' => $subscriber->identity_document ?? '',
            'state' => (int) $subscriber->state,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        Gate::authorize('subscribers.update');

        $subscriber = User::role('subscriber')->findOrFail($this->editingId);
        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];

        $this->normalizeNullableFields($validated);
        $subscriber->fill($validated);
        $subscriber->save();

        $this->dispatch('successAlert', ['success' => __('mma.admin.subscribers.messages.updated')]);
        $this->closeModal();
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
     * Limpia filtros y reinicia la paginación.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'state', 'subscriptionStatus']);
        $this->resetPage();
    }

    /**
     * Devuelve la etiqueta visible de state.
     */
    public function stateLabel(int $state): string
    {
        return $state === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    /**
     * Devuelve las opciones disponibles de subscription status.
     */
    public function subscriptionStatusOptions(): array
    {
        return [
            0 => __('mma.admin.subscribers.subscription_status.pending'),
            1 => __('mma.admin.subscribers.subscription_status.active'),
            2 => __('mma.admin.subscribers.subscription_status.expired'),
            3 => __('mma.admin.subscribers.subscription_status.cancelled'),
            4 => __('mma.admin.subscribers.subscription_status.suspended'),
        ];
    }

    /**
     * Devuelve la etiqueta visible de subscription status.
     */
    public function subscriptionStatusLabel(?int $status): string
    {
        return $status === null
            ? __('mma.admin.subscribers.subscription_status.none')
            : ($this->subscriptionStatusOptions()[$status] ?? (string) $status);
    }

    /**
     * Devuelve la clase visual de subscription status.
     */
    public function subscriptionStatusBadge(?int $status): string
    {
        return [
            null => 'tw-badge-gray',
            0 => 'tw-badge-blue',
            1 => 'tw-badge-green',
            2 => 'tw-badge-gray',
            3 => 'tw-badge-red',
            4 => 'tw-badge-red',
        ][$status] ?? 'tw-badge-gray';
    }

    /**
     * Renderiza la tabla de suscriptores con filtros activos.
     */
    public function render()
    {
        $subscribers = User::query()
            ->role('subscriber')
            ->with(['latestSubscription.plan:id,name,slug'])
            ->withCount(['userSubscriptions', 'subscriptionPayments', 'purchaseRequests'])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('lastname', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('number_phone', 'like', "%{$this->search}%")
                        ->orWhere('identity_document', 'like', "%{$this->search}%");
                });
            })
            ->when($this->state !== '', fn ($query) => $query->where('state', (int) $this->state))
            ->when($this->subscriptionStatus !== '', fn ($query) => $query->whereHas(
                'latestSubscription',
                fn ($subscriptionQuery) => $subscriptionQuery->where('status', (int) $this->subscriptionStatus)
            ))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.subscribers.subscriber-table', compact('subscribers'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:100'],
            'form.lastname' => ['nullable', 'string', 'max:100'],
            'form.email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->editingId)],
            'form.number_phone' => ['nullable', 'string', 'max:30'],
            'form.identity_document' => ['nullable', 'string', 'max:40'],
            'form.state' => ['required', Rule::in([0, 1])],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.subscribers.form.name'),
            'form.lastname' => __('mma.admin.subscribers.form.lastname'),
            'form.email' => __('mma.admin.subscribers.form.email'),
            'form.number_phone' => __('mma.admin.subscribers.form.number_phone'),
            'form.identity_document' => __('mma.admin.subscribers.form.identity_document'),
            'form.state' => __('mma.admin.subscribers.form.state'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'name' => '',
            'lastname' => '',
            'email' => '',
            'number_phone' => '',
            'identity_document' => '',
            'state' => 1,
        ];
        $this->resetErrorBag();
    }

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['lastname', 'number_phone', 'identity_document'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['state'] = (int) $validated['state'];
    }
}

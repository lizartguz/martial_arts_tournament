<?php

namespace App\Livewire\Admin\UserSubscriptions;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserSubscriptionTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $planId = '';
    public string $status = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public bool $showModal = false;
    public bool $showCancelModal = false;
    public ?int $editingId = null;
    public ?int $pendingCancelId = null;
    public string $cancelName = '';
    public array $form = [
        'user_id' => '',
        'subscription_plan_id' => '',
        'starts_at' => '',
        'ends_at' => '',
        'trial_ends_at' => '',
        'renewal_at' => '',
        'status' => 1,
        'source' => 'manual',
        'metadata_note' => '',
    ];

    /**
     * Inicializa el componente de suscripciones de usuario.
     */
    public function mount(): void
    {
        Gate::authorize('user_subscriptions.view');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el plan.
     */
    public function updatingPlanId(): void
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
     * Abre el formulario para crear un registro.
     */
    public function create(): void
    {
        Gate::authorize('user_subscriptions.create');

        $this->resetForm();
        $this->form['starts_at'] = now()->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('user_subscriptions.update');

        $subscription = UserSubscription::query()
            ->with(['user:id,name,lastname,email', 'plan:id,name'])
            ->findOrFail($id);

        $this->editingId = $subscription->id;
        $this->form = [
            'user_id' => (string) $subscription->user_id,
            'subscription_plan_id' => (string) $subscription->subscription_plan_id,
            'starts_at' => $this->dateTimeLocal($subscription->starts_at),
            'ends_at' => $this->dateTimeLocal($subscription->ends_at),
            'trial_ends_at' => $this->dateTimeLocal($subscription->trial_ends_at),
            'renewal_at' => $this->dateTimeLocal($subscription->renewal_at),
            'status' => (int) $subscription->status,
            'source' => $subscription->source ?? 'manual',
            'metadata_note' => (string) data_get($subscription->metadata, 'note', ''),
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        Gate::authorize($this->editingId ? 'user_subscriptions.update' : 'user_subscriptions.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];

        if (! User::role('subscriber')->whereKey($validated['user_id'])->exists()) {
            $this->addError('form.user_id', __('mma.admin.user_subscriptions.validation.subscriber_required'));

            return;
        }

        if (! $this->validateDateOrder($validated)) {
            return;
        }

        $subscription = $this->editingId
            ? UserSubscription::query()->findOrFail($this->editingId)
            : new UserSubscription(['created_by' => auth()->id()]);

        $this->normalizeSubscriptionFields($validated, $subscription);
        $subscription->fill($validated);
        $subscription->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.user_subscriptions.messages.updated')
                : __('mma.admin.user_subscriptions.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Gestiona confirm cancel dentro de la tabla de suscripciones de usuario.
     */
    public function confirmCancel(int $id): void
    {
        Gate::authorize('user_subscriptions.update');

        $subscription = UserSubscription::query()
            ->with(['user:id,name,lastname,email', 'plan:id,name'])
            ->findOrFail($id);

        $this->pendingCancelId = $subscription->id;
        $this->cancelName = trim(($subscription->user?->name ?? '').' '.($subscription->user?->lastname ?? ''));
        $this->cancelName = $this->cancelName !== '' ? $this->cancelName : (string) $subscription->user?->email;
        $this->cancelName .= ' - '.($subscription->plan?->name ?? __('mma.admin.common.not_available'));
        $this->showCancelModal = true;
    }

    /**
     * Valida si el usuario puede cel.
     */
    public function cancel(): void
    {
        Gate::authorize('user_subscriptions.update');

        if (! $this->pendingCancelId) {
            return;
        }

        $subscription = UserSubscription::query()->findOrFail($this->pendingCancelId);
        $subscription->forceFill([
            'status' => 3,
            'cancelled_at' => now(),
        ])->save();

        $this->closeCancelModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.user_subscriptions.messages.cancelled')]);
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
     * Cierra el modal de anulación.
     */
    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->pendingCancelId = null;
        $this->cancelName = '';
    }

    /**
     * Limpia filtros y reinicia la paginación.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'planId', 'status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones de estado permitidas.
     */
    public function statusOptions(): array
    {
        return [
            0 => __('mma.admin.user_subscriptions.status.pending'),
            1 => __('mma.admin.user_subscriptions.status.active'),
            2 => __('mma.admin.user_subscriptions.status.expired'),
            3 => __('mma.admin.user_subscriptions.status.cancelled'),
            4 => __('mma.admin.user_subscriptions.status.suspended'),
        ];
    }

    /**
     * Devuelve las opciones disponibles de source.
     */
    public function sourceOptions(): array
    {
        return [
            'manual' => __('mma.admin.user_subscriptions.sources.manual'),
            'admin' => __('mma.admin.user_subscriptions.sources.admin'),
            'purchase_request' => __('mma.admin.user_subscriptions.sources.purchase_request'),
            'import' => __('mma.admin.user_subscriptions.sources.import'),
            'other' => __('mma.admin.user_subscriptions.sources.other'),
        ];
    }

    /**
     * Traduce el estado del registro.
     */
    public function statusLabel(?int $status): string
    {
        return $status === null
            ? __('mma.admin.common.not_available')
            : ($this->statusOptions()[$status] ?? (string) $status);
    }

    /**
     * Devuelve la etiqueta visible de source.
     */
    public function sourceLabel(?string $source): string
    {
        return $source ? ($this->sourceOptions()[$source] ?? $source) : __('mma.admin.common.not_available');
    }

    /**
     * Define la clase visual según el estado.
     */
    public function statusBadge(?int $status): string
    {
        return [
            0 => 'tw-badge-blue',
            1 => 'tw-badge-green',
            2 => 'tw-badge-gray',
            3 => 'tw-badge-red',
            4 => 'tw-badge-red',
        ][$status] ?? 'tw-badge-gray';
    }

    /**
     * Renderiza la tabla de suscripciones de usuario con filtros activos.
     */
    public function render()
    {
        $subscriptions = UserSubscription::query()
            ->with([
                'user:id,name,lastname,email,number_phone',
                'plan:id,name,slug,price,currency,billing_period,status',
            ])
            ->withCount('payments')
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    /**
                     * Agrega condiciones sobre la relacion consultada.
                     */
                    $subQuery->whereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', "%{$this->search}%")
                            ->orWhere('lastname', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%")
                            ->orWhere('number_phone', 'like', "%{$this->search}%");
                    /**
                     * Agrega condiciones sobre la relacion consultada.
                     */
                    })->orWhereHas('plan', function ($planQuery) {
                        $planQuery->where('name', 'like', "%{$this->search}%")
                            ->orWhere('slug', 'like', "%{$this->search}%");
                    });
                });
            })
            ->when($this->planId !== '', fn ($query) => $query->where('subscription_plan_id', (int) $this->planId))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('starts_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('starts_at', '<=', $this->dateTo))
            ->orderByDesc('starts_at')
            ->paginate($this->perPage);

        $plans = SubscriptionPlan::query()
            ->select('id', 'name', 'price', 'currency', 'billing_period', 'status')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $subscribers = User::query()
            ->role('subscriber')
            ->select('id', 'name', 'lastname', 'email')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        return view('livewire.admin.user-subscriptions.user-subscription-table', compact('subscriptions', 'plans', 'subscribers'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.user_id' => ['required', 'integer', 'exists:users,id'],
            'form.subscription_plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'form.starts_at' => ['required', 'date'],
            'form.ends_at' => ['nullable', 'date'],
            'form.trial_ends_at' => ['nullable', 'date'],
            'form.renewal_at' => ['nullable', 'date'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.source' => ['nullable', 'string', 'max:40', Rule::in(array_keys($this->sourceOptions()))],
            'form.metadata_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.user_id' => __('mma.admin.user_subscriptions.form.user_id'),
            'form.subscription_plan_id' => __('mma.admin.user_subscriptions.form.subscription_plan_id'),
            'form.starts_at' => __('mma.admin.user_subscriptions.form.starts_at'),
            'form.ends_at' => __('mma.admin.user_subscriptions.form.ends_at'),
            'form.trial_ends_at' => __('mma.admin.user_subscriptions.form.trial_ends_at'),
            'form.renewal_at' => __('mma.admin.user_subscriptions.form.renewal_at'),
            'form.status' => __('mma.admin.user_subscriptions.form.status'),
            'form.source' => __('mma.admin.user_subscriptions.form.source'),
            'form.metadata_note' => __('mma.admin.user_subscriptions.form.metadata_note'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'user_id' => '',
            'subscription_plan_id' => '',
            'starts_at' => '',
            'ends_at' => '',
            'trial_ends_at' => '',
            'renewal_at' => '',
            'status' => 1,
            'source' => 'manual',
            'metadata_note' => '',
        ];
        $this->resetErrorBag();
    }

    /**
     * Gestiona normalize subscription fields dentro de la tabla de suscripciones de usuario.
     */
    protected function normalizeSubscriptionFields(array &$data, UserSubscription $subscription): void
    {
        foreach (['ends_at', 'trial_ends_at', 'renewal_at', 'source', 'metadata_note'] as $field) {
            if (($data[$field] ?? null) === '') {
                $data[$field] = null;
            }
        }

        foreach (['starts_at', 'ends_at', 'trial_ends_at', 'renewal_at'] as $field) {
            $data[$field] = $data[$field] ? Carbon::parse($data[$field]) : null;
        }

        $data['user_id'] = (int) $data['user_id'];
        $data['subscription_plan_id'] = (int) $data['subscription_plan_id'];
        $data['status'] = (int) $data['status'];
        $data['metadata'] = $data['metadata_note'] ? ['note' => $data['metadata_note']] : null;
        unset($data['metadata_note']);

        if ($data['status'] === 3 && ! $subscription->cancelled_at) {
            $data['cancelled_at'] = now();
        }

        if ($data['status'] !== 3) {
            $data['cancelled_at'] = null;
        }
    }

    /**
     * Gestiona validate date order dentro de la tabla de suscripciones de usuario.
     */
    protected function validateDateOrder(array $data): bool
    {
        $startsAt = Carbon::parse($data['starts_at']);

        foreach (['ends_at', 'trial_ends_at', 'renewal_at'] as $field) {
            if (($data[$field] ?? '') === '') {
                continue;
            }

            if (Carbon::parse($data[$field])->lt($startsAt)) {
                $this->addError("form.{$field}", __('mma.admin.user_subscriptions.validation.'.$field.'_after_start'));

                return false;
            }
        }

        return true;
    }

    /**
     * Convierte fechas persistidas al formato de input local.
     */
    protected function dateTimeLocal($dateTime): string
    {
        return $dateTime ? $dateTime->format('Y-m-d\TH:i') : '';
    }
}

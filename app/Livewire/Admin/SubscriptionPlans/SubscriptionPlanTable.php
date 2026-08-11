<?php

namespace App\Livewire\Admin\SubscriptionPlans;

use App\Models\SubscriptionPlan;
use App\Services\SlugGeneratorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionPlanTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $billingPeriod = '';
    public string $status = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public bool $slugTouched = false;
    public array $featureRows = [];
    public array $form = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'price' => '0.00',
        'currency' => 'BOB',
        'billing_period' => 'monthly',
        'duration_days' => 30,
        'discount_percentage' => '0.00',
        'display_order' => 0,
        'status' => 1,
    ];

    /**
     * Inicializa el componente de planes de suscripción.
     */
    public function mount(): void
    {
        Gate::authorize('subscription_plans.view');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el período de cobro.
     */
    public function updatingBillingPeriod(): void
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
     * Reinicia la paginación al cambiar el tamaño de página.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Sincroniza el formulario al cambiar el nombre.
     */
    public function updatedFormName(string $value): void
    {
        if (! $this->slugTouched) {
            $this->form['slug'] = Str::slug($value);
        }
    }

    /**
     * Sincroniza el formulario al cambiar el slug.
     */
    public function updatedFormSlug(string $value): void
    {
        $this->slugTouched = true;
        $this->form['slug'] = Str::slug($value);
    }

    /**
     * Abre el formulario para crear un registro.
     */
    public function create(): void
    {
        Gate::authorize('subscription_plans.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('subscription_plans.update');

        $plan = SubscriptionPlan::query()
            ->with('planFeatures')
            ->findOrFail($id);

        $this->editingId = $plan->id;
        $this->slugTouched = true;
        $this->form = [
            'name' => $plan->name,
            'slug' => $plan->slug,
            'description' => $plan->description ?? '',
            'price' => (string) $plan->price,
            'currency' => $plan->currency,
            'billing_period' => $plan->billing_period,
            'duration_days' => $plan->duration_days,
            'discount_percentage' => (string) $plan->discount_percentage,
            'display_order' => (int) $plan->display_order,
            'status' => (int) $plan->status,
        ];
        $this->featureRows = $plan->planFeatures
            ->map(fn ($feature) => [
                'id' => $feature->id,
                'name' => $feature->name,
                'description' => $feature->description ?? '',
                'feature_key' => $feature->feature_key ?? '',
                'value' => $feature->value ?? '',
                'display_order' => (int) $feature->display_order,
                'status' => (int) $feature->status,
            ])
            ->values()
            ->all();

        if ($this->featureRows === []) {
            $this->addFeatureRow();
        }

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        Gate::authorize($this->editingId ? 'subscription_plans.update' : 'subscription_plans.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes());
        $planData = $validated['form'];
        $plan = $this->editingId
            ? SubscriptionPlan::query()->findOrFail($this->editingId)
            : new SubscriptionPlan(['created_by' => auth()->id()]);

        $slugs = app(SlugGeneratorService::class);
        $planData['slug'] = $this->slugTouched && $planData['slug'] !== ''
            ? $planData['slug']
            : $slugs->uniqueForModel(SubscriptionPlan::class, $planData['name'], 'slug', $this->editingId);

        $features = $this->normalizedFeatures();
        $this->normalizePlanFields($planData, $features);

        $plan->fill($planData);
        $plan->save();
        $this->syncFeatures($plan, $features);

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.subscription_plans.messages.updated')
                : __('mma.admin.subscription_plans.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('subscription_plans.delete');

        $plan = SubscriptionPlan::query()->findOrFail($id);

        $this->pendingDeleteId = $plan->id;
        $this->deleteName = $plan->name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('subscription_plans.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $plan = SubscriptionPlan::query()
            ->withCount(['userSubscriptions', 'purchaseRequests'])
            ->findOrFail($this->pendingDeleteId);

        if ($plan->user_subscriptions_count || $plan->purchase_requests_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', ['failed' => __('mma.admin.subscription_plans.messages.delete_blocked')]);

            return;
        }

        $plan->delete();
        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.subscription_plans.messages.deleted')]);
    }

    /**
     * Agrega una fila vacía de beneficio al plan.
     */
    public function addFeatureRow(): void
    {
        $this->featureRows[] = [
            'id' => null,
            'name' => '',
            'description' => '',
            'feature_key' => '',
            'value' => '',
            'display_order' => count($this->featureRows) + 1,
            'status' => 1,
        ];
    }

    /**
     * Quita una fila de beneficio del plan.
     */
    public function removeFeatureRow(int $index): void
    {
        unset($this->featureRows[$index]);
        $this->featureRows = array_values($this->featureRows);
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
        $this->reset(['search', 'billingPeriod', 'status']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones disponibles de billing period.
     */
    public function billingPeriodOptions(): array
    {
        return [
            'monthly' => __('mma.admin.subscription_plans.billing_periods.monthly'),
            'quarterly' => __('mma.admin.subscription_plans.billing_periods.quarterly'),
            'yearly' => __('mma.admin.subscription_plans.billing_periods.yearly'),
            'one_time' => __('mma.admin.subscription_plans.billing_periods.one_time'),
            'lifetime' => __('mma.admin.subscription_plans.billing_periods.lifetime'),
        ];
    }

    /**
     * Devuelve las monedas habilitadas.
     */
    public function currencyOptions(): array
    {
        return ['BOB' => 'BOB', 'USD' => 'USD'];
    }

    /**
     * Devuelve la etiqueta visible de billing period.
     */
    public function billingPeriodLabel(?string $period): string
    {
        return $period ? ($this->billingPeriodOptions()[$period] ?? $period) : __('mma.admin.common.not_available');
    }

    /**
     * Traduce el estado del registro.
     */
    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    /**
     * Renderiza la tabla de planes de suscripción con filtros activos.
     */
    public function render()
    {
        $plans = SubscriptionPlan::query()
            ->withCount(['planFeatures', 'userSubscriptions', 'purchaseRequests'])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->billingPeriod !== '', fn ($query) => $query->where('billing_period', $this->billingPeriod))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderBy('display_order')
            ->orderBy('price')
            ->paginate($this->perPage);

        return view('livewire.admin.subscription-plans.subscription-plan-table', compact('plans'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:120'],
            'form.slug' => [
                'nullable',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('subscription_plans', 'slug')->ignore($this->editingId),
            ],
            'form.description' => ['nullable', 'string', 'max:3000'],
            'form.price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'form.currency' => ['required', Rule::in(array_keys($this->currencyOptions()))],
            'form.billing_period' => ['required', Rule::in(array_keys($this->billingPeriodOptions()))],
            'form.duration_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'form.discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'form.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'form.status' => ['required', Rule::in([0, 1])],
            'featureRows' => ['array'],
            'featureRows.*.id' => ['nullable', 'integer'],
            'featureRows.*.name' => ['nullable', 'string', 'max:120'],
            'featureRows.*.description' => ['nullable', 'string', 'max:255'],
            'featureRows.*.feature_key' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9_\\-\\.]+$/'],
            'featureRows.*.value' => ['nullable', 'string', 'max:120'],
            'featureRows.*.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'featureRows.*.status' => ['required', Rule::in([0, 1])],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.subscription_plans.form.name'),
            'form.slug' => __('mma.admin.subscription_plans.form.slug'),
            'form.description' => __('mma.admin.subscription_plans.form.description'),
            'form.price' => __('mma.admin.subscription_plans.form.price'),
            'form.currency' => __('mma.admin.subscription_plans.form.currency'),
            'form.billing_period' => __('mma.admin.subscription_plans.form.billing_period'),
            'form.duration_days' => __('mma.admin.subscription_plans.form.duration_days'),
            'form.discount_percentage' => __('mma.admin.subscription_plans.form.discount_percentage'),
            'form.display_order' => __('mma.admin.subscription_plans.form.display_order'),
            'form.status' => __('mma.admin.subscription_plans.form.status'),
            'featureRows.*.name' => __('mma.admin.subscription_plans.features.name'),
            'featureRows.*.description' => __('mma.admin.subscription_plans.features.description'),
            'featureRows.*.feature_key' => __('mma.admin.subscription_plans.features.feature_key'),
            'featureRows.*.value' => __('mma.admin.subscription_plans.features.value'),
            'featureRows.*.display_order' => __('mma.admin.subscription_plans.features.display_order'),
            'featureRows.*.status' => __('mma.admin.subscription_plans.features.status'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->featureRows = [];
        $this->form = [
            'name' => '',
            'slug' => '',
            'description' => '',
            'price' => '0.00',
            'currency' => 'BOB',
            'billing_period' => 'monthly',
            'duration_days' => 30,
            'discount_percentage' => '0.00',
            'display_order' => 0,
            'status' => 1,
        ];
        $this->addFeatureRow();
        $this->resetErrorBag();
    }

    /**
     * Normaliza campos del plan antes de persistirlo.
     */
    protected function normalizePlanFields(array &$planData, array $features): void
    {
        foreach (['description', 'duration_days'] as $field) {
            if (($planData[$field] ?? null) === '') {
                $planData[$field] = null;
            }
        }

        $planData['price'] = round((float) $planData['price'], 2);
        $planData['discount_percentage'] = round((float) $planData['discount_percentage'], 2);
        $planData['duration_days'] = $planData['duration_days'] ? (int) $planData['duration_days'] : null;
        $planData['display_order'] = (int) $planData['display_order'];
        $planData['status'] = (int) $planData['status'];
        $planData['features'] = collect($features)
            ->where('status', 1)
            ->pluck('name')
            ->values()
            ->all();
        $planData['limits'] = null;
        $planData['updated_by'] = auth()->id();
    }

    /**
     * Normaliza beneficios antes de guardarlos en el plan.
     */
    protected function normalizedFeatures(): array
    {
        return collect($this->featureRows)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'name' => trim((string) ($row['name'] ?? '')),
                'description' => trim((string) ($row['description'] ?? '')),
                'feature_key' => trim((string) ($row['feature_key'] ?? '')),
                'value' => trim((string) ($row['value'] ?? '')),
                'display_order' => (int) ($row['display_order'] ?? 0),
                'status' => (int) ($row['status'] ?? 1),
            ])
            ->filter(fn ($row) => $row['name'] !== '')
            ->values()
            ->all();
    }

    /**
     * Sincroniza beneficios visibles con el arreglo persistido.
     */
    protected function syncFeatures(SubscriptionPlan $plan, array $features): void
    {
        $keptIds = [];

        foreach ($features as $feature) {
            $record = $feature['id']
                ? $plan->planFeatures()->whereKey($feature['id'])->first()
                : $plan->planFeatures()->make();

            if (! $record) {
                $record = $plan->planFeatures()->make();
            }

            $record->fill([
                'name' => $feature['name'],
                'description' => $feature['description'] !== '' ? $feature['description'] : null,
                'feature_key' => $feature['feature_key'] !== '' ? $feature['feature_key'] : null,
                'value' => $feature['value'] !== '' ? $feature['value'] : null,
                'display_order' => $feature['display_order'],
                'status' => $feature['status'],
            ]);
            $record->save();
            $keptIds[] = $record->id;
        }

        $plan->planFeatures()
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))
            ->delete();
    }
}

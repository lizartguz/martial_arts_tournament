<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Event;
use App\Models\PurchaseRequest;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $reportType = 'events';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $status = '';
    public int $perPage = 10;

    protected array $queryString = [
        'reportType' => ['as' => 'type', 'except' => 'events'],
        'dateFrom' => ['as' => 'from', 'except' => ''],
        'dateTo' => ['as' => 'to', 'except' => ''],
        'status' => ['except' => ''],
    ];

    /**
     * Inicializa el componente de reports dashboard.
     */
    public function mount(): void
    {
        $this->reportType = request()->query('type', $this->reportType);

        if (! array_key_exists($this->reportType, $this->typePermissions())) {
            $this->reportType = 'events';
        }

        if (! $this->canViewType($this->reportType)) {
            $firstAllowedType = array_key_first($this->typeOptions());
            abort_if($firstAllowedType === null, 403);

            $this->reportType = $firstAllowedType;
        }
    }

    /**
     * Sincroniza el estado al cambiar el tipo de reporte.
     */
    public function updatedReportType(): void
    {
        abort_unless($this->canViewType($this->reportType), 403);
        $this->status = '';
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
     * Limpia filtros y reinicia la paginación.
     */
    public function resetFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'status']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones disponibles de type.
     */
    public function typeOptions(): array
    {
        return collect([
            'events' => __('mma.admin.reports.types.events'),
            'subscriptions' => __('mma.admin.reports.types.subscriptions'),
            'sales' => __('mma.admin.reports.types.sales'),
        ])->filter(fn ($label, $type) => $this->canViewType($type))->all();
    }

    /**
     * Devuelve las opciones de estado permitidas.
     */
    public function statusOptions(): array
    {
        return match ($this->reportType) {
            'events' => [
                0 => __('mma.admin.events.status.draft'),
                1 => __('mma.admin.events.status.published'),
                2 => __('mma.admin.events.status.archived'),
                3 => __('mma.admin.events.status.cancelled'),
            ],
            'subscriptions' => [
                0 => __('mma.admin.user_subscriptions.status.pending'),
                1 => __('mma.admin.user_subscriptions.status.active'),
                2 => __('mma.admin.user_subscriptions.status.expired'),
                3 => __('mma.admin.user_subscriptions.status.cancelled'),
                4 => __('mma.admin.user_subscriptions.status.suspended'),
            ],
            'sales' => [
                0 => __('mma.admin.subscription_payments.status.pending'),
                1 => __('mma.admin.subscription_payments.status.paid'),
                2 => __('mma.admin.subscription_payments.status.failed'),
                3 => __('mma.admin.subscription_payments.status.refunded'),
                4 => __('mma.admin.subscription_payments.status.expired'),
            ],
            default => [],
        };
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
     * Renderiza la tabla de reports dashboard con filtros activos.
     */
    public function render()
    {
        abort_unless($this->canViewType($this->reportType), 403);

        return view('livewire.admin.reports.reports-dashboard', [
            'stats' => $this->stats(),
            'rows' => $this->rows(),
        ]);
    }

    /**
     * Gestiona stats dentro de la tabla de reports dashboard.
     */
    protected function stats(): array
    {
        return match ($this->reportType) {
            'events' => $this->eventStats(),
            'subscriptions' => $this->subscriptionStats(),
            'sales' => $this->salesStats(),
            default => [],
        };
    }

    /**
     * Gestiona rows dentro de la tabla de reports dashboard.
     */
    protected function rows()
    {
        return match ($this->reportType) {
            'events' => $this->eventRows(),
            'subscriptions' => $this->subscriptionRows(),
            'sales' => $this->salesRows(),
            default => collect(),
        };
    }

    /**
     * Gestiona event stats dentro de la tabla de reports dashboard.
     */
    protected function eventStats(): array
    {
        $query = $this->applyEventFilters(Event::query());

        return [
            'total' => (clone $query)->count(),
            'published' => (clone $query)->where('status', 1)->count(),
            'featured' => (clone $query)->where('is_featured', true)->count(),
            'requests' => PurchaseRequest::query()
                ->whereNotNull('event_id')
                ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
                ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
                ->count(),
        ];
    }

    /**
     * Gestiona subscription stats dentro de la tabla de reports dashboard.
     */
    protected function subscriptionStats(): array
    {
        $query = $this->applySubscriptionFilters(UserSubscription::query());

        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', 1)->count(),
            'pending' => (clone $query)->where('status', 0)->count(),
            'expired' => (clone $query)->where('status', 2)->count(),
        ];
    }

    /**
     * Gestiona sales stats dentro de la tabla de reports dashboard.
     */
    protected function salesStats(): array
    {
        $query = $this->applyPaymentFilters(SubscriptionPayment::query());

        return [
            'total' => (clone $query)->count(),
            'paid' => (clone $query)->where('status', 1)->count(),
            'pending' => (clone $query)->where('status', 0)->count(),
            'amount' => (float) (clone $query)->where('status', 1)->sum('amount'),
        ];
    }

    /**
     * Gestiona event rows dentro de la tabla de reports dashboard.
     */
    protected function eventRows()
    {
        return $this->applyEventFilters(Event::query())
            ->with('venue:id,name')
            ->withCount(['fights', 'purchaseRequests', 'ticketLinks'])
            ->orderByDesc('starts_at')
            ->paginate($this->perPage);
    }

    /**
     * Gestiona subscription rows dentro de la tabla de reports dashboard.
     */
    protected function subscriptionRows()
    {
        return $this->applySubscriptionFilters(UserSubscription::query())
            ->with(['user:id,name,lastname,email', 'plan:id,name,slug,price,currency'])
            ->withCount('payments')
            ->latest('id')
            ->paginate($this->perPage);
    }

    /**
     * Gestiona sales rows dentro de la tabla de reports dashboard.
     */
    protected function salesRows()
    {
        return $this->applyPaymentFilters(SubscriptionPayment::query())
            ->with(['user:id,name,lastname,email', 'subscription.plan:id,name,slug'])
            ->latest('id')
            ->paginate($this->perPage);
    }

    /**
     * Gestiona apply event filters dentro de la tabla de reports dashboard.
     */
    protected function applyEventFilters(Builder $query): Builder
    {
        return $query
            ->when($this->status !== '', fn ($q) => $q->where('status', (int) $this->status))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('starts_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('starts_at', '<=', $this->dateTo));
    }

    /**
     * Gestiona apply subscription filters dentro de la tabla de reports dashboard.
     */
    protected function applySubscriptionFilters(Builder $query): Builder
    {
        return $query
            ->when($this->status !== '', fn ($q) => $q->where('status', (int) $this->status))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('starts_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('starts_at', '<=', $this->dateTo));
    }

    /**
     * Gestiona apply payment filters dentro de la tabla de reports dashboard.
     */
    protected function applyPaymentFilters(Builder $query): Builder
    {
        return $query
            ->when($this->status !== '', fn ($q) => $q->where('status', (int) $this->status))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));
    }

    /**
     * Valida si el usuario puede view type.
     */
    protected function canViewType(string $type): bool
    {
        $permission = $this->typePermissions()[$type] ?? null;

        return $permission !== null && auth()->user()?->can($permission);
    }

    /**
     * Gestiona type permissions dentro de la tabla de reports dashboard.
     */
    protected function typePermissions(): array
    {
        return [
            'events' => 'reports.events.view',
            'subscriptions' => 'reports.subscriptions.view',
            'sales' => 'reports.sales.view',
        ];
    }
}

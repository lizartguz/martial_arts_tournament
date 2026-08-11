<?php

namespace App\Livewire\Admin\SubscriptionPayments;

use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SubscriptionPaymentTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $status = '';
    public string $paymentMethod = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public bool $showModal = false;
    public bool $showConfirmModal = false;
    public bool $showCancelModal = false;
    public ?int $editingId = null;
    public ?int $pendingConfirmId = null;
    public ?int $pendingCancelId = null;
    public string $actionName = '';
    public $proofFile = null;
    public array $form = [
        'user_id' => '',
        'user_subscription_id' => '',
        'amount' => '0.00',
        'currency' => 'BOB',
        'payment_method' => 'manual_transfer',
        'provider' => '',
        'provider_transaction_id' => '',
        'payment_url' => '',
        'paid_at' => '',
        'expires_at' => '',
        'status' => 0,
        'notes' => '',
    ];

    public function mount(): void
    {
        Gate::authorize('subscription_payments.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentMethod(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFormUserSubscriptionId($value): void
    {
        if (! $value) {
            return;
        }

        $subscription = UserSubscription::query()
            ->with('plan:id,price,currency')
            ->find($value);

        if (! $subscription) {
            return;
        }

        $this->form['user_id'] = (string) $subscription->user_id;

        if ((float) $this->form['amount'] <= 0 && $subscription->plan) {
            $this->form['amount'] = number_format((float) $subscription->plan->price, 2, '.', '');
            $this->form['currency'] = $subscription->plan->currency;
        }
    }

    public function create(): void
    {
        Gate::authorize('subscription_payments.upload_proof');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('subscription_payments.upload_proof');

        $payment = SubscriptionPayment::query()->findOrFail($id);

        $this->editingId = $payment->id;
        $this->form = [
            'user_id' => (string) $payment->user_id,
            'user_subscription_id' => (string) ($payment->user_subscription_id ?? ''),
            'amount' => (string) $payment->amount,
            'currency' => $payment->currency,
            'payment_method' => $payment->payment_method,
            'provider' => $payment->provider ?? '',
            'provider_transaction_id' => $payment->provider_transaction_id ?? '',
            'payment_url' => $payment->payment_url ?? '',
            'paid_at' => $this->dateTimeLocal($payment->paid_at),
            'expires_at' => $this->dateTimeLocal($payment->expires_at),
            'status' => (int) $payment->status,
            'notes' => $payment->notes ?? '',
        ];
        $this->proofFile = null;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(FileUploadService $uploads): void
    {
        Gate::authorize('subscription_payments.upload_proof');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];

        if (! User::role('subscriber')->whereKey($validated['user_id'])->exists()) {
            $this->addError('form.user_id', __('mma.admin.subscription_payments.validation.subscriber_required'));

            return;
        }

        if (! $this->validateSubscriptionBelongsToUser($validated)) {
            return;
        }

        $payment = $this->editingId
            ? SubscriptionPayment::query()->findOrFail($this->editingId)
            : new SubscriptionPayment();

        $this->normalizePaymentFields($validated);
        $payment->fill($validated);

        if ($this->proofFile) {
            try {
                $this->replaceProof($payment, $uploads);
            } catch (\InvalidArgumentException $exception) {
                $this->addError('proofFile', $exception->getMessage());

                return;
            }
        }

        $payment->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.subscription_payments.messages.updated')
                : __('mma.admin.subscription_payments.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmPayment(int $id): void
    {
        Gate::authorize('subscription_payments.confirm');

        $payment = SubscriptionPayment::query()
            ->with(['user:id,name,lastname,email'])
            ->findOrFail($id);

        $this->pendingConfirmId = $payment->id;
        $this->actionName = $this->paymentActionName($payment);
        $this->showConfirmModal = true;
    }

    public function markAsPaid(): void
    {
        Gate::authorize('subscription_payments.confirm');

        if (! $this->pendingConfirmId) {
            return;
        }

        SubscriptionPayment::query()->findOrFail($this->pendingConfirmId)->update([
            'status' => 1,
            'paid_at' => now(),
        ]);

        $this->closeConfirmModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.subscription_payments.messages.confirmed')]);
    }

    public function cancelPayment(int $id): void
    {
        Gate::authorize('subscription_payments.cancel');

        $payment = SubscriptionPayment::query()
            ->with(['user:id,name,lastname,email'])
            ->findOrFail($id);

        $this->pendingCancelId = $payment->id;
        $this->actionName = $this->paymentActionName($payment);
        $this->showCancelModal = true;
    }

    public function markAsFailed(): void
    {
        Gate::authorize('subscription_payments.cancel');

        if (! $this->pendingCancelId) {
            return;
        }

        SubscriptionPayment::query()->findOrFail($this->pendingCancelId)->update([
            'status' => 2,
        ]);

        $this->closeCancelModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.subscription_payments.messages.cancelled')]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->pendingConfirmId = null;
        $this->actionName = '';
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->pendingCancelId = null;
        $this->actionName = '';
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'paymentMethod', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function statusOptions(): array
    {
        return [
            0 => __('mma.admin.subscription_payments.status.pending'),
            1 => __('mma.admin.subscription_payments.status.paid'),
            2 => __('mma.admin.subscription_payments.status.failed'),
            3 => __('mma.admin.subscription_payments.status.refunded'),
            4 => __('mma.admin.subscription_payments.status.expired'),
        ];
    }

    public function paymentMethodOptions(): array
    {
        return [
            'manual_transfer' => __('mma.admin.subscription_payments.payment_methods.manual_transfer'),
            'cash' => __('mma.admin.subscription_payments.payment_methods.cash'),
            'qr' => __('mma.admin.subscription_payments.payment_methods.qr'),
            'whatsapp' => __('mma.admin.subscription_payments.payment_methods.whatsapp'),
            'gateway' => __('mma.admin.subscription_payments.payment_methods.gateway'),
            'other' => __('mma.admin.subscription_payments.payment_methods.other'),
        ];
    }

    public function currencyOptions(): array
    {
        return ['BOB' => 'BOB', 'USD' => 'USD'];
    }

    public function statusLabel(int $status): string
    {
        return $this->statusOptions()[$status] ?? (string) $status;
    }

    public function statusBadge(int $status): string
    {
        return [
            0 => 'tw-badge-blue',
            1 => 'tw-badge-green',
            2 => 'tw-badge-red',
            3 => 'tw-badge-gray',
            4 => 'tw-badge-gray',
        ][$status] ?? 'tw-badge-gray';
    }

    public function paymentMethodLabel(?string $method): string
    {
        return $method ? ($this->paymentMethodOptions()[$method] ?? $method) : __('mma.admin.common.not_available');
    }

    public function fileSizeLabel(?int $bytes): string
    {
        if (! $bytes) {
            return __('mma.admin.common.not_available');
        }

        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 2).' MB'
            : number_format($bytes / 1024, 1).' KB';
    }

    public function render()
    {
        $payments = SubscriptionPayment::query()
            ->with([
                'user:id,name,lastname,email,number_phone',
                'subscription:id,user_id,subscription_plan_id,starts_at,ends_at,status',
                'subscription.plan:id,name,slug',
            ])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('provider_transaction_id', 'like', "%{$this->search}%")
                        ->orWhere('provider', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', "%{$this->search}%")
                                ->orWhere('lastname', 'like', "%{$this->search}%")
                                ->orWhere('email', 'like', "%{$this->search}%")
                                ->orWhere('number_phone', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('subscription.plan', function ($planQuery) {
                            $planQuery->where('name', 'like', "%{$this->search}%")
                                ->orWhere('slug', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->paymentMethod !== '', fn ($query) => $query->where('payment_method', $this->paymentMethod))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate($this->perPage);

        $subscribers = User::query()
            ->role('subscriber')
            ->select('id', 'name', 'lastname', 'email')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        $subscriptions = UserSubscription::query()
            ->with(['user:id,name,lastname,email', 'plan:id,name,price,currency'])
            ->latest('starts_at')
            ->get();

        return view('livewire.admin.subscription-payments.subscription-payment-table', compact('payments', 'subscribers', 'subscriptions'));
    }

    protected function rules(): array
    {
        return [
            'form.user_id' => ['required', 'integer', 'exists:users,id'],
            'form.user_subscription_id' => ['nullable', 'integer', 'exists:user_subscriptions,id'],
            'form.amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'form.currency' => ['required', Rule::in(array_keys($this->currencyOptions()))],
            'form.payment_method' => ['required', Rule::in(array_keys($this->paymentMethodOptions()))],
            'form.provider' => ['nullable', 'string', 'max:80'],
            'form.provider_transaction_id' => ['nullable', 'string', 'max:180'],
            'form.payment_url' => ['nullable', 'url', 'max:500'],
            'form.paid_at' => ['nullable', 'date'],
            'form.expires_at' => ['nullable', 'date'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.notes' => ['nullable', 'string', 'max:3000'],
            'proofFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:'.((int) config('uploads.payment_proofs.max_mb', 5) * 1024)],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.user_id' => __('mma.admin.subscription_payments.form.user_id'),
            'form.user_subscription_id' => __('mma.admin.subscription_payments.form.user_subscription_id'),
            'form.amount' => __('mma.admin.subscription_payments.form.amount'),
            'form.currency' => __('mma.admin.subscription_payments.form.currency'),
            'form.payment_method' => __('mma.admin.subscription_payments.form.payment_method'),
            'form.provider' => __('mma.admin.subscription_payments.form.provider'),
            'form.provider_transaction_id' => __('mma.admin.subscription_payments.form.provider_transaction_id'),
            'form.payment_url' => __('mma.admin.subscription_payments.form.payment_url'),
            'form.paid_at' => __('mma.admin.subscription_payments.form.paid_at'),
            'form.expires_at' => __('mma.admin.subscription_payments.form.expires_at'),
            'form.status' => __('mma.admin.subscription_payments.form.status'),
            'form.notes' => __('mma.admin.subscription_payments.form.notes'),
            'proofFile' => __('mma.admin.subscription_payments.form.payment_proof'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->proofFile = null;
        $this->form = [
            'user_id' => '',
            'user_subscription_id' => '',
            'amount' => '0.00',
            'currency' => 'BOB',
            'payment_method' => 'manual_transfer',
            'provider' => '',
            'provider_transaction_id' => '',
            'payment_url' => '',
            'paid_at' => '',
            'expires_at' => '',
            'status' => 0,
            'notes' => '',
        ];
        $this->resetErrorBag();
    }

    protected function normalizePaymentFields(array &$data): void
    {
        foreach (['user_subscription_id', 'provider', 'provider_transaction_id', 'payment_url', 'paid_at', 'expires_at', 'notes'] as $field) {
            if (($data[$field] ?? null) === '') {
                $data[$field] = null;
            }
        }

        $data['user_id'] = (int) $data['user_id'];
        $data['user_subscription_id'] = $data['user_subscription_id'] ? (int) $data['user_subscription_id'] : null;
        $data['amount'] = round((float) $data['amount'], 2);
        $data['status'] = (int) $data['status'];
        $data['paid_at'] = $data['paid_at'] ? Carbon::parse($data['paid_at']) : null;
        $data['expires_at'] = $data['expires_at'] ? Carbon::parse($data['expires_at']) : null;
    }

    protected function validateSubscriptionBelongsToUser(array $data): bool
    {
        if (blank($data['user_subscription_id'] ?? null)) {
            return true;
        }

        $exists = UserSubscription::query()
            ->whereKey($data['user_subscription_id'])
            ->where('user_id', $data['user_id'])
            ->exists();

        if (! $exists) {
            $this->addError('form.user_subscription_id', __('mma.admin.subscription_payments.validation.subscription_user_mismatch'));

            return false;
        }

        return true;
    }

    protected function replaceProof(SubscriptionPayment $payment, FileUploadService $uploads): void
    {
        $stored = $uploads->replacePaymentProof($payment, $this->proofFile, 'subscription-payment');
        $payment->payment_proof_path = $stored['path'];
        $payment->payment_proof_mime = $stored['mime'];
        $payment->payment_proof_size = $stored['size'];
    }

    protected function paymentActionName(SubscriptionPayment $payment): string
    {
        $name = trim(($payment->user?->name ?? '').' '.($payment->user?->lastname ?? ''));
        $name = $name !== '' ? $name : (string) $payment->user?->email;

        return $name.' - '.$payment->currency.' '.number_format((float) $payment->amount, 2);
    }

    protected function dateTimeLocal($dateTime): string
    {
        return $dateTime ? $dateTime->format('Y-m-d\TH:i') : '';
    }
}

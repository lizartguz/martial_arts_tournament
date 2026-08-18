<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.subscription_payments.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.subscription_payments.table_subtitle') }}</div>
        </div>
        @can('subscription_payments.upload_proof')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.subscription_payments.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_180px_190px_140px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.subscription_payments.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.status') }}</label>
            <select class="tw-input-sm" wire:model.live="status">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->statusOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.subscription_payments.filters.payment_method') }}</label>
            <select class="tw-input-sm" wire:model.live="paymentMethod">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->paymentMethodOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.subscription_payments.filters.from') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateFrom">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.subscription_payments.filters.to') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateTo">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.per_page') }}</label>
            <select class="tw-input-sm" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="button" class="tw-btn tw-btn-outline" wire:click="resetFilters">
                <i class="fas fa-eraser text-xs"></i>{{ __('mma.admin.common.filters.clear') }}
            </button>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('mma.admin.subscription_payments.columns.subscriber') }}</th>
                    <th>{{ __('mma.admin.subscription_payments.columns.subscription') }}</th>
                    <th>{{ __('mma.admin.subscription_payments.columns.amount') }}</th>
                    <th>{{ __('mma.admin.subscription_payments.columns.method') }}</th>
                    <th>{{ __('mma.admin.subscription_payments.columns.proof') }}</th>
                    <th>{{ __('mma.admin.subscription_payments.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr wire:key="subscription-payment-{{ $payment->id }}">
                        <td>
                            <div class="min-w-[220px]">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ trim(($payment->user?->name ?? '').' '.($payment->user?->lastname ?? '')) ?: __('mma.admin.common.not_available') }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $payment->user?->email ?? __('mma.admin.common.not_available') }}</div>
                                @if ($payment->user?->number_phone)
                                    <div class="mt-1 text-xs text-gray-500">{{ $payment->user->number_phone }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $payment->subscription?->plan?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($payment->subscription)
                                <div class="text-xs text-gray-500">
                                    {{ $payment->subscription->starts_at?->format('Y-m-d') }}
                                    @if ($payment->subscription->ends_at)
                                        - {{ $payment->subscription->ends_at->format('Y-m-d') }}
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="font-semibold">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $payment->paid_at ? __('mma.admin.subscription_payments.paid_at_value', ['date' => $payment->paid_at->format('Y-m-d H:i')]) : __('mma.admin.subscription_payments.not_paid') }}
                            </div>
                        </td>
                        <td>
                            <div>{{ $this->paymentMethodLabel($payment->payment_method) }}</div>
                            @if ($payment->provider_transaction_id)
                                <div class="text-xs text-gray-500">{{ $payment->provider_transaction_id }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($payment->payment_proof_path)
                                <a href="{{ route('admin.subscription-payments.proof', $payment) }}" target="_blank"
                                   class="tw-btn-xs tw-btn-outline">
                                    <i class="fas fa-file-alt text-xs"></i>{{ __('mma.admin.subscription_payments.proof.open') }}
                                </a>
                                <div class="mt-1 text-xs text-gray-500">{{ $this->fileSizeLabel($payment->payment_proof_size) }}</div>
                            @else
                                <span class="tw-badge tw-badge-gray">{{ __('mma.admin.subscription_payments.proof.none') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge {{ $this->statusBadge((int) $payment->status) }}">
                                {{ $this->statusLabel((int) $payment->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('subscription_payments.upload_proof')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $payment->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('subscription_payments.confirm')
                                        @if ((int) $payment->status !== 1)
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                    wire:click="confirmPayment({{ $payment->id }})" @click="open = false">
                                                <i class="fas fa-check mr-2 text-emerald-500"></i>{{ __('mma.admin.subscription_payments.actions.confirm') }}
                                            </button>
                                        @endif
                                    @endcan
                                    @can('subscription_payments.cancel')
                                        @if (! in_array((int) $payment->status, [1, 2], true))
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-gray-700"
                                                    wire:click="cancelPayment({{ $payment->id }})" @click="open = false">
                                                <i class="fas fa-ban mr-2"></i>{{ __('mma.admin.subscription_payments.actions.cancel') }}
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($payments->hasPages())
        <div class="mt-3">{{ $payments->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.subscription_payments.edit') : __('mma.admin.subscription_payments.create')" icon="fas fa-receipt">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.user_subscription_id') }}</label>
                    <select class="tw-input @error('form.user_subscription_id') border-red-500 @enderror" wire:model.live="form.user_subscription_id">
                        <option value="">{{ __('mma.admin.subscription_payments.form.no_subscription') }}</option>
                        @foreach ($subscriptions as $subscription)
                            <option value="{{ $subscription->id }}">
                                {{ trim(($subscription->user?->name ?? '').' '.($subscription->user?->lastname ?? '')) }} - {{ $subscription->plan?->name ?? __('mma.admin.common.not_available') }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.user_subscription_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.user_id') }}</label>
                    <select class="tw-input @error('form.user_id') border-red-500 @enderror" wire:model.live="form.user_id">
                        <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                        @foreach ($subscribers as $subscriber)
                            <option value="{{ $subscriber->id }}">{{ trim($subscriber->name.' '.$subscriber->lastname) }} - {{ $subscriber->email }}</option>
                        @endforeach
                    </select>
                    @error('form.user_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.amount') }}</label>
                    <input type="number" step="0.01" min="0.01" class="tw-input @error('form.amount') border-red-500 @enderror" wire:model.live="form.amount">
                    @error('form.amount') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.currency') }}</label>
                    <select class="tw-input @error('form.currency') border-red-500 @enderror" wire:model.live="form.currency">
                        @foreach ($this->currencyOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.currency') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.payment_method') }}</label>
                    <select class="tw-input @error('form.payment_method') border-red-500 @enderror" wire:model.live="form.payment_method">
                        @foreach ($this->paymentMethodOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.payment_method') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.status') }}</label>
                    <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                        @foreach ($this->statusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.provider') }}</label>
                    <input type="text" class="tw-input @error('form.provider') border-red-500 @enderror" wire:model.live.debounce.300ms="form.provider">
                    @error('form.provider') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.provider_transaction_id') }}</label>
                    <input type="text" class="tw-input @error('form.provider_transaction_id') border-red-500 @enderror" wire:model.live.debounce.300ms="form.provider_transaction_id">
                    @error('form.provider_transaction_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.paid_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.paid_at') border-red-500 @enderror" wire:model.live="form.paid_at">
                    @error('form.paid_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.expires_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.expires_at') border-red-500 @enderror" wire:model.live="form.expires_at">
                    @error('form.expires_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.payment_url') }}</label>
                    <input type="url" class="tw-input @error('form.payment_url') border-red-500 @enderror" wire:model.live.debounce.300ms="form.payment_url">
                    @error('form.payment_url') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.payment_proof') }}</label>
                    <input type="file" class="tw-input @error('proofFile') border-red-500 @enderror" wire:model="proofFile" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="mt-1 text-xs text-gray-500">{{ __('mma.admin.subscription_payments.proof.help') }}</div>
                    @error('proofFile') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.subscription_payments.form.notes') }}</label>
                    <textarea class="tw-input @error('form.notes') border-red-500 @enderror" rows="3" wire:model.live.debounce.300ms="form.notes"></textarea>
                    @error('form.notes') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
            </div>

            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-emerald" wire:click="save" wire:loading.attr="disabled">
                    <i class="fas fa-save text-xs"></i>{{ __('messages.actions.save') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif

    @if ($showConfirmModal)
        <x-tw-confirm-modal
            close="closeConfirmModal"
            confirm="markAsPaid"
            :title="__('mma.admin.subscription_payments.confirm_title')"
            :message="__('mma.admin.subscription_payments.confirm_warning')"
            :target-name="$actionName"
            :confirm-text="__('mma.admin.subscription_payments.actions.confirm')"
            variant="success"
        />
    @endif

    @if ($showCancelModal)
        <x-tw-confirm-modal
            close="closeCancelModal"
            confirm="markAsFailed"
            :title="__('mma.admin.subscription_payments.cancel_title')"
            :message="__('mma.admin.subscription_payments.cancel_warning')"
            :target-name="$actionName"
            :confirm-text="__('mma.admin.subscription_payments.actions.cancel')"
        />
    @endif
</div>

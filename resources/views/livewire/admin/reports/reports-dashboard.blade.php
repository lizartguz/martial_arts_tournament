<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.reports.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.reports.table_subtitle') }}</div>
        </div>
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[220px_160px_160px_180px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.reports.filters.type') }}</label>
            <select class="tw-input-sm" wire:model.live="reportType">
                @foreach ($this->typeOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.reports.filters.from') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateFrom">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.reports.filters.to') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateTo">
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

    <div class="mb-4 grid gap-3 md:grid-cols-4">
        @foreach ($stats as $key => $value)
            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">{{ __('mma.admin.reports.stats.'.$key) }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    @if ($key === 'amount')
                        BOB {{ number_format((float) $value, 2) }}
                    @else
                        {{ number_format((int) $value) }}
                    @endif
                </p>
            </div>
        @endforeach
    </div>

    <div class="tw-table-wrap">
        @if ($reportType === 'events')
            <table class="tw-table">
                <thead class="themed-thead">
                    <tr>
                        <th>{{ __('mma.admin.reports.columns.event') }}</th>
                        <th>{{ __('mma.admin.reports.columns.venue') }}</th>
                        <th>{{ __('mma.admin.reports.columns.date') }}</th>
                        <th>{{ __('mma.admin.reports.columns.fights') }}</th>
                        <th>{{ __('mma.admin.reports.columns.requests') }}</th>
                        <th>{{ __('mma.admin.common.filters.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $event)
                        <tr>
                            <td class="font-semibold">{{ $event->name }}</td>
                            <td>{{ $event->venue?->name ?? __('mma.admin.common.not_available') }}</td>
                            <td>{{ $event->starts_at?->format('Y-m-d H:i') ?? __('mma.admin.common.not_available') }}</td>
                            <td>{{ $event->fights_count }}</td>
                            <td>{{ $event->purchase_requests_count }}</td>
                            <td>{{ $this->statusLabel((int) $event->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($reportType === 'subscriptions')
            <table class="tw-table">
                <thead class="themed-thead">
                    <tr>
                        <th>{{ __('mma.admin.reports.columns.subscriber') }}</th>
                        <th>{{ __('mma.admin.reports.columns.plan') }}</th>
                        <th>{{ __('mma.admin.reports.columns.period') }}</th>
                        <th>{{ __('mma.admin.reports.columns.payments') }}</th>
                        <th>{{ __('mma.admin.common.filters.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $subscription)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ trim(($subscription->user?->name ?? '').' '.($subscription->user?->lastname ?? '')) ?: __('mma.admin.common.not_available') }}</div>
                                <div class="text-xs text-gray-500">{{ $subscription->user?->email }}</div>
                            </td>
                            <td>{{ $subscription->plan?->name ?? __('mma.admin.common.not_available') }}</td>
                            <td>{{ $subscription->starts_at?->format('Y-m-d') }} - {{ $subscription->ends_at?->format('Y-m-d') ?? __('mma.admin.user_subscriptions.open_ended') }}</td>
                            <td>{{ $subscription->payments_count }}</td>
                            <td>{{ $this->statusLabel((int) $subscription->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <table class="tw-table">
                <thead class="themed-thead">
                    <tr>
                        <th>{{ __('mma.admin.reports.columns.subscriber') }}</th>
                        <th>{{ __('mma.admin.reports.columns.concept') }}</th>
                        <th>{{ __('mma.admin.reports.columns.amount') }}</th>
                        <th>{{ __('mma.admin.reports.columns.method') }}</th>
                        <th>{{ __('mma.admin.reports.columns.date') }}</th>
                        <th>{{ __('mma.admin.common.filters.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $payment)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ trim(($payment->user?->name ?? '').' '.($payment->user?->lastname ?? '')) ?: __('mma.admin.common.not_available') }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->user?->email }}</div>
                            </td>
                            <td>{{ $payment->subscription?->plan?->name ?? __('mma.admin.common.not_available') }}</td>
                            <td>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ $payment->paid_at?->format('Y-m-d H:i') ?? $payment->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $this->statusLabel((int) $payment->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    @if ($rows->hasPages())
        <div class="mt-3">{{ $rows->links() }}</div>
    @endif
</div>

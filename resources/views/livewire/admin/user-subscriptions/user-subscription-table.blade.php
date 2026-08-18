<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.user_subscriptions.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.user_subscriptions.table_subtitle') }}</div>
        </div>
        @can('user_subscriptions.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.user_subscriptions.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_220px_150px_140px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.user_subscriptions.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.user_subscriptions.filters.plan') }}</label>
            <select class="tw-input-sm" wire:model.live="planId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                @endforeach
            </select>
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
            <label class="tw-label">{{ __('mma.admin.user_subscriptions.filters.from') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateFrom">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.user_subscriptions.filters.to') }}</label>
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
                    <th>{{ __('mma.admin.user_subscriptions.columns.subscriber') }}</th>
                    <th>{{ __('mma.admin.user_subscriptions.columns.plan') }}</th>
                    <th>{{ __('mma.admin.user_subscriptions.columns.period') }}</th>
                    <th>{{ __('mma.admin.user_subscriptions.columns.status') }}</th>
                    <th>{{ __('mma.admin.user_subscriptions.columns.payments') }}</th>
                    <th>{{ __('mma.admin.user_subscriptions.columns.source') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscriptions as $subscription)
                    <tr wire:key="user-subscription-{{ $subscription->id }}">
                        <td>
                            <div class="min-w-[220px]">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ trim(($subscription->user?->name ?? '').' '.($subscription->user?->lastname ?? '')) ?: __('mma.admin.common.not_available') }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $subscription->user?->email ?? __('mma.admin.common.not_available') }}</div>
                                @if ($subscription->user?->number_phone)
                                    <div class="mt-1 text-xs text-gray-500">{{ $subscription->user->number_phone }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $subscription->plan?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($subscription->plan)
                                <div class="text-xs text-gray-500">
                                    {{ $subscription->plan->currency }} {{ number_format((float) $subscription->plan->price, 2) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm">
                                {{ __('mma.admin.user_subscriptions.period_value', [
                                    'start' => $subscription->starts_at?->format('Y-m-d H:i'),
                                    'end' => $subscription->ends_at?->format('Y-m-d H:i') ?? __('mma.admin.user_subscriptions.open_ended'),
                                ]) }}
                            </div>
                            @if ($subscription->renewal_at)
                                <div class="text-xs text-gray-500">{{ __('mma.admin.user_subscriptions.renewal_value', ['date' => $subscription->renewal_at->format('Y-m-d H:i')]) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge {{ $this->statusBadge((int) $subscription->status) }}">
                                {{ $this->statusLabel((int) $subscription->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="tw-badge tw-badge-blue">
                                {{ __('mma.admin.user_subscriptions.payments_summary', ['count' => $subscription->payments_count]) }}
                            </span>
                        </td>
                        <td>{{ $this->sourceLabel($subscription->source) }}</td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('user_subscriptions.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $subscription->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                        @if ((int) $subscription->status !== 3)
                                            <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-gray-700"
                                                    wire:click="confirmCancel({{ $subscription->id }})" @click="open = false">
                                                <i class="fas fa-ban mr-2"></i>{{ __('mma.admin.user_subscriptions.actions.cancel') }}
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

    @if ($subscriptions->hasPages())
        <div class="mt-3">{{ $subscriptions->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.user_subscriptions.edit') : __('mma.admin.user_subscriptions.create')" icon="fas fa-id-card">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.user_id') }}</label>
                    <select class="tw-input @error('form.user_id') border-red-500 @enderror" wire:model.live="form.user_id">
                        <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                        @foreach ($subscribers as $subscriber)
                            <option value="{{ $subscriber->id }}">{{ trim($subscriber->name.' '.$subscriber->lastname) }} - {{ $subscriber->email }}</option>
                        @endforeach
                    </select>
                    @error('form.user_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.subscription_plan_id') }}</label>
                    <select class="tw-input @error('form.subscription_plan_id') border-red-500 @enderror" wire:model.live="form.subscription_plan_id">
                        <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->currency }} {{ number_format((float) $plan->price, 2) }}</option>
                        @endforeach
                    </select>
                    @error('form.subscription_plan_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.starts_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.starts_at') border-red-500 @enderror" wire:model.live="form.starts_at">
                    @error('form.starts_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.ends_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.ends_at') border-red-500 @enderror" wire:model.live="form.ends_at">
                    @error('form.ends_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.trial_ends_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.trial_ends_at') border-red-500 @enderror" wire:model.live="form.trial_ends_at">
                    @error('form.trial_ends_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.renewal_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.renewal_at') border-red-500 @enderror" wire:model.live="form.renewal_at">
                    @error('form.renewal_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.status') }}</label>
                    <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                        @foreach ($this->statusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.source') }}</label>
                    <select class="tw-input @error('form.source') border-red-500 @enderror" wire:model.live="form.source">
                        @foreach ($this->sourceOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.source') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.user_subscriptions.form.metadata_note') }}</label>
                    <textarea class="tw-input @error('form.metadata_note') border-red-500 @enderror" rows="3" wire:model.live.debounce.300ms="form.metadata_note"></textarea>
                    @error('form.metadata_note') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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

    @if ($showCancelModal)
        <x-tw-confirm-modal
            close="closeCancelModal"
            confirm="cancel"
            :title="__('mma.admin.user_subscriptions.cancel_title')"
            :message="__('mma.admin.user_subscriptions.cancel_warning')"
            :target-name="$cancelName"
            :confirm-text="__('mma.admin.user_subscriptions.actions.cancel')"
        />
    @endif
</div>

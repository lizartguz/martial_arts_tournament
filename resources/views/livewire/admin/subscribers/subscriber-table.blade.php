<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.subscribers.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.subscribers.table_subtitle') }}</div>
        </div>
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_180px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.subscribers.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.subscribers.filters.subscription_status') }}</label>
            <select class="tw-input-sm" wire:model.live="subscriptionStatus">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->subscriptionStatusOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.status') }}</label>
            <select class="tw-input-sm" wire:model.live="state">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                <option value="1">{{ __('mma.admin.common.active') }}</option>
                <option value="0">{{ __('mma.admin.common.inactive') }}</option>
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

    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('mma.admin.subscribers.columns.subscriber') }}</th>
                    <th>{{ __('mma.admin.subscribers.columns.contact') }}</th>
                    <th>{{ __('mma.admin.subscribers.columns.subscription') }}</th>
                    <th>{{ __('mma.admin.subscribers.columns.activity') }}</th>
                    <th>{{ __('mma.admin.subscribers.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $subscriber)
                    <tr wire:key="subscriber-{{ $subscriber->id }}">
                        <td>
                            <div class="min-w-[240px]">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ trim($subscriber->name.' '.$subscriber->lastname) }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $subscriber->email }}</div>
                                @if ($subscriber->identity_document)
                                    <div class="mt-1 text-xs text-gray-500">{{ __('mma.admin.subscribers.identity_value', ['value' => $subscriber->identity_document]) }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>{{ $subscriber->number_phone ?: __('mma.admin.common.not_available') }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $subscriber->last_login_at ? __('mma.admin.subscribers.last_login_value', ['date' => $subscriber->last_login_at->format('Y-m-d H:i')]) : __('mma.admin.subscribers.last_login_empty') }}
                            </div>
                        </td>
                        <td>
                            @if ($subscriber->latestSubscription)
                                <div class="font-medium">{{ $subscriber->latestSubscription->plan?->name ?? __('mma.admin.common.not_available') }}</div>
                                <span class="tw-badge {{ $this->subscriptionStatusBadge((int) $subscriber->latestSubscription->status) }}">
                                    {{ $this->subscriptionStatusLabel((int) $subscriber->latestSubscription->status) }}
                                </span>
                            @else
                                <span class="tw-badge tw-badge-gray">{{ $this->subscriptionStatusLabel(null) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge tw-badge-blue">
                                {{ __('mma.admin.subscribers.activity_summary', ['subscriptions' => $subscriber->user_subscriptions_count, 'payments' => $subscriber->subscription_payments_count, 'requests' => $subscriber->purchase_requests_count]) }}
                            </span>
                        </td>
                        <td>
                            <span class="tw-badge {{ $subscriber->state === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->stateLabel((int) $subscriber->state) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('subscribers.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $subscriber->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($subscribers->hasPages())
        <div class="mt-3">{{ $subscribers->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="4xl" :title="__('mma.admin.subscribers.edit')" icon="fas fa-user-check">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscribers.form.name') }}</label>
                    <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                    @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscribers.form.lastname') }}</label>
                    <input type="text" class="tw-input @error('form.lastname') border-red-500 @enderror" wire:model.live.debounce.300ms="form.lastname">
                    @error('form.lastname') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscribers.form.email') }}</label>
                    <input type="email" class="tw-input @error('form.email') border-red-500 @enderror" wire:model.live.debounce.300ms="form.email">
                    @error('form.email') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscribers.form.number_phone') }}</label>
                    <input type="text" class="tw-input @error('form.number_phone') border-red-500 @enderror" wire:model.live.debounce.300ms="form.number_phone">
                    @error('form.number_phone') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscribers.form.identity_document') }}</label>
                    <input type="text" class="tw-input @error('form.identity_document') border-red-500 @enderror" wire:model.live.debounce.300ms="form.identity_document">
                    @error('form.identity_document') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.subscribers.form.state') }}</label>
                    <select class="tw-input @error('form.state') border-red-500 @enderror" wire:model.live="form.state">
                        <option value="1">{{ __('mma.admin.common.active') }}</option>
                        <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                    </select>
                    @error('form.state') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
</div>

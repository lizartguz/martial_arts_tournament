<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.subscription_plans.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.subscription_plans.table_subtitle') }}</div>
        </div>
        @can('subscription_plans.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.subscription_plans.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_170px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.subscription_plans.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.subscription_plans.filters.billing_period') }}</label>
            <select class="tw-input-sm" wire:model.live="billingPeriod">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->billingPeriodOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.status') }}</label>
            <select class="tw-input-sm" wire:model.live="status">
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
                    <th>{{ __('mma.admin.subscription_plans.columns.plan') }}</th>
                    <th>{{ __('mma.admin.subscription_plans.columns.price') }}</th>
                    <th>{{ __('mma.admin.subscription_plans.columns.period') }}</th>
                    <th>{{ __('mma.admin.subscription_plans.columns.usage') }}</th>
                    <th>{{ __('mma.admin.subscription_plans.columns.order') }}</th>
                    <th>{{ __('mma.admin.subscription_plans.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                    <tr wire:key="subscription-plan-{{ $plan->id }}">
                        <td>
                            <div class="min-w-[280px]">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $plan->name }}</div>
                                <div class="text-xs text-gray-500">{{ $plan->slug }}</div>
                                @if ($plan->description)
                                    <div class="mt-1 max-w-xl text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($plan->description, 120) }}</div>
                                @endif
                                <span class="tw-badge tw-badge-blue mt-2">
                                    {{ __('mma.admin.subscription_plans.features_summary', ['count' => $plan->plan_features_count]) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="font-semibold">{{ number_format((float) $plan->price, 2) }} {{ $plan->currency }}</div>
                            @if ((float) $plan->discount_percentage > 0)
                                <div class="text-xs text-emerald-600 dark:text-emerald-300">
                                    {{ __('mma.admin.subscription_plans.discount_summary', ['discount' => number_format((float) $plan->discount_percentage, 2)]) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $this->billingPeriodLabel($plan->billing_period) }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $plan->duration_days ? __('mma.admin.subscription_plans.duration_summary', ['days' => $plan->duration_days]) : __('mma.admin.common.not_available') }}
                            </div>
                        </td>
                        <td>
                            <span class="tw-badge tw-badge-gray">
                                {{ __('mma.admin.subscription_plans.usage_summary', ['subscriptions' => $plan->user_subscriptions_count, 'requests' => $plan->purchase_requests_count]) }}
                            </span>
                        </td>
                        <td>{{ $plan->display_order }}</td>
                        <td>
                            <span class="tw-badge {{ $plan->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $plan->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('subscription_plans.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $plan->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('subscription_plans.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $plan->id }})" @click="open = false">
                                            <i class="fas fa-trash mr-2"></i>{{ __('messages.actions.delete') }}
                                        </button>
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

    @if ($plans->hasPages())
        <div class="mt-3">{{ $plans->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="7xl" :title="$editingId ? __('mma.admin.subscription_plans.edit') : __('mma.admin.subscription_plans.create')" icon="fas fa-tags">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(360px,0.8fr)]">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.name') }}</label>
                        <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.name">
                        @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.price') }}</label>
                        <input type="number" step="0.01" min="0" class="tw-input @error('form.price') border-red-500 @enderror" wire:model.live="form.price">
                        @error('form.price') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.currency') }}</label>
                        <select class="tw-input @error('form.currency') border-red-500 @enderror" wire:model.live="form.currency">
                            @foreach ($this->currencyOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.currency') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.billing_period') }}</label>
                        <select class="tw-input @error('form.billing_period') border-red-500 @enderror" wire:model.live="form.billing_period">
                            @foreach ($this->billingPeriodOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.billing_period') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.duration_days') }}</label>
                        <input type="number" min="1" max="3650" class="tw-input @error('form.duration_days') border-red-500 @enderror" wire:model.live="form.duration_days">
                        @error('form.duration_days') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.discount_percentage') }}</label>
                        <input type="number" step="0.01" min="0" max="100" class="tw-input @error('form.discount_percentage') border-red-500 @enderror" wire:model.live="form.discount_percentage">
                        @error('form.discount_percentage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.display_order') }}</label>
                        <input type="number" min="0" max="9999" class="tw-input @error('form.display_order') border-red-500 @enderror" wire:model.live="form.display_order">
                        @error('form.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="tw-label">{{ __('mma.admin.subscription_plans.form.description') }}</label>
                        <textarea rows="5" class="tw-input @error('form.description') border-red-500 @enderror" wire:model.live.debounce.400ms="form.description"></textarea>
                        @error('form.description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <label class="tw-label mb-0">{{ __('mma.admin.subscription_plans.features.title') }}</label>
                        <button type="button" class="tw-btn-xs tw-btn-outline" wire:click="addFeatureRow">
                            <i class="fas fa-plus"></i>{{ __('mma.admin.subscription_plans.features.add') }}
                        </button>
                    </div>
                    <div class="space-y-3">
                        @foreach ($featureRows as $index => $feature)
                            <div class="rounded border border-gray-200 p-3 dark:border-gray-700" wire:key="plan-feature-{{ $index }}">
                                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_74px_36px]">
                                    <div>
                                        <label class="tw-label">{{ __('mma.admin.subscription_plans.features.name') }}</label>
                                        <input type="text" class="tw-input-sm @error('featureRows.'.$index.'.name') border-red-500 @enderror" wire:model.live.debounce.300ms="featureRows.{{ $index }}.name">
                                        @error('featureRows.'.$index.'.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="tw-label">{{ __('mma.admin.subscription_plans.features.display_order') }}</label>
                                        <input type="number" min="0" max="9999" class="tw-input-sm @error('featureRows.'.$index.'.display_order') border-red-500 @enderror" wire:model.live="featureRows.{{ $index }}.display_order">
                                        @error('featureRows.'.$index.'.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="tw-btn-xs tw-btn-outline text-red-600" wire:click="removeFeatureRow({{ $index }})" title="{{ __('messages.actions.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="tw-label">{{ __('mma.admin.subscription_plans.features.feature_key') }}</label>
                                        <input type="text" class="tw-input-sm @error('featureRows.'.$index.'.feature_key') border-red-500 @enderror" wire:model.live.debounce.300ms="featureRows.{{ $index }}.feature_key">
                                        @error('featureRows.'.$index.'.feature_key') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="tw-label">{{ __('mma.admin.subscription_plans.features.value') }}</label>
                                        <input type="text" class="tw-input-sm @error('featureRows.'.$index.'.value') border-red-500 @enderror" wire:model.live.debounce.300ms="featureRows.{{ $index }}.value">
                                        @error('featureRows.'.$index.'.value') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="tw-label">{{ __('mma.admin.subscription_plans.features.status') }}</label>
                                        <select class="tw-input-sm @error('featureRows.'.$index.'.status') border-red-500 @enderror" wire:model.live="featureRows.{{ $index }}.status">
                                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                                        </select>
                                        @error('featureRows.'.$index.'.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="tw-label">{{ __('mma.admin.subscription_plans.features.description') }}</label>
                                        <input type="text" class="tw-input-sm @error('featureRows.'.$index.'.description') border-red-500 @enderror" wire:model.live.debounce.300ms="featureRows.{{ $index }}.description">
                                        @error('featureRows.'.$index.'.description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.subscription_plans.features.help') }}</p>
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

    @if ($showDeleteModal)
        <x-tw-confirm-modal
            close="closeDeleteModal"
            confirm="delete"
            :title="__('mma.admin.subscription_plans.delete_title')"
            :message="__('mma.admin.subscription_plans.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>

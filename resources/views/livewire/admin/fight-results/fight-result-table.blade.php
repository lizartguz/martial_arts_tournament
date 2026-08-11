<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.fight_results.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fight_results.table_subtitle') }}</div>
        </div>
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_220px_170px_160px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.fight_results.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fight_results.filters.event') }}</label>
            <select class="tw-input-sm" wire:model.live="eventId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fight_results.filters.result_type') }}</label>
            <select class="tw-input-sm" wire:model.live="resultType">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->resultTypeOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fight_results.filters.result_state') }}</label>
            <select class="tw-input-sm" wire:model.live="resultState">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->resultStateOptions() as $value => $label)
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

    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('mma.admin.fight_results.columns.fight') }}</th>
                    <th>{{ __('mma.admin.fight_results.columns.event') }}</th>
                    <th>{{ __('mma.admin.fight_results.columns.result') }}</th>
                    <th>{{ __('mma.admin.fight_results.columns.winner') }}</th>
                    <th>{{ __('mma.admin.fight_results.columns.round_time') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fights as $fight)
                    <tr wire:key="fight-result-{{ $fight->id }}">
                        <td>
                            <div class="min-w-[280px]">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $this->fighterName($fight->cornerRed) }}
                                    <span class="text-xs text-gray-500">{{ __('mma.landing.vs') }}</span>
                                    {{ $this->fighterName($fight->cornerBlue) }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $fight->title ?? __('mma.admin.fight_results.no_custom_title') }}</div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $fight->event?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($fight->event?->starts_at)
                                <div class="text-xs text-gray-500">{{ $fight->event->starts_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge {{ $this->resultBadge($fight->result?->result_type) }}">
                                {{ $this->resultTypeLabel($fight->result?->result_type) }}
                            </span>
                            @if ($fight->result?->method)
                                <div class="mt-1 text-xs text-gray-500">{{ $fight->result->method }}</div>
                            @endif
                        </td>
                        <td>{{ $this->fighterName($fight->result?->winner) }}</td>
                        <td>
                            @if ($fight->result?->round || $fight->result?->time)
                                {{ __('mma.admin.fight_results.round_time_value', ['round' => $fight->result?->round ?? '-', 'time' => $fight->result?->time ?? '-']) }}
                            @else
                                {{ __('mma.admin.fight_results.pending') }}
                            @endif
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" class="tw-btn-xs tw-btn-outline" @click="open = !open">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     class="absolute z-20 mt-8 w-48 rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('fight_results.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="manage({{ $fight->id }})" @click="open = false">
                                            <i class="fas fa-trophy mr-2 text-amber-500"></i>{{ __('mma.admin.fight_results.actions.manage') }}
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

    @if ($fights->hasPages())
        <div class="mt-3">{{ $fights->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="__('mma.admin.fight_results.modal_title')" icon="fas fa-trophy">
            <div class="mb-4 rounded border border-gray-200 bg-gray-50 p-3 text-sm dark:border-gray-700 dark:bg-gray-900/30">
                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $selectedFightLabel }}</div>
                <div class="text-xs text-gray-500">{{ $selectedEventLabel }}</div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="tw-label">{{ __('mma.admin.fight_results.form.result_type') }}</label>
                    <select class="tw-input @error('form.result_type') border-red-500 @enderror" wire:model.live="form.result_type">
                        @foreach ($this->resultTypeOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.result_type') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.fight_results.form.winner_fighter_id') }}</label>
                    <select class="tw-input @error('form.winner_fighter_id') border-red-500 @enderror"
                            wire:model.live="form.winner_fighter_id"
                            @disabled(in_array($form['result_type'], ['draw', 'no_contest'], true))>
                        <option value="">{{ __('mma.admin.fight_results.form.no_winner') }}</option>
                        @foreach ($winnerOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('form.winner_fighter_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.fight_results.form.method') }}</label>
                    <input type="text" class="tw-input @error('form.method') border-red-500 @enderror" wire:model.live.debounce.300ms="form.method">
                    @error('form.method') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fight_results.form.round') }}</label>
                        <input type="number" min="1" max="12" class="tw-input @error('form.round') border-red-500 @enderror" wire:model.live="form.round">
                        @error('form.round') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fight_results.form.time') }}</label>
                        <input type="text" class="tw-input @error('form.time') border-red-500 @enderror" placeholder="5:00" wire:model.live.debounce.300ms="form.time">
                        @error('form.time') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.fight_results.form.official_notes') }}</label>
                    <textarea rows="5" class="tw-input @error('form.official_notes') border-red-500 @enderror" wire:model.live.debounce.400ms="form.official_notes"></textarea>
                    @error('form.official_notes') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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

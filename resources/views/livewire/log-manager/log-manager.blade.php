<div>
    <h5 class="mb-4 text-center text-base font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.logs.page') }}</h5>

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-green">
            <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
            <span class="flex-1">{{ session('success') }}</span>
            <button @click="show = false"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif

    {{-- Panel header --}}
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('messages.logs.header') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ __('messages.logs.file_size') }}: {{ $metadata['formatted_size'] }}
                @if ($metadata['last_modified'])
                    | {{ __('messages.logs.last_modified') }}: {{ $metadata['last_modified'] }}
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('logs.download') }}"
               class="tw-btn tw-btn-outline-emerald {{ $metadata['exists'] ? '' : 'pointer-events-none opacity-50' }}">
                <i class="fas fa-download text-xs"></i>{{ __('messages.logs.download') }}
            </a>
            <button type="button" class="tw-btn tw-btn-outline-red" wire:click="confirmClear">
                <i class="fas fa-trash-alt text-xs"></i>{{ __('messages.logs.clear') }}
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="mb-4 flex flex-wrap gap-3">
        <div class="w-full sm:w-64">
            <label class="tw-label">{{ __('messages.logs.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('messages.logs.filters.search_placeholder') }}">
        </div>
        <div class="w-full sm:w-36">
            <label class="tw-label">{{ __('messages.logs.filters.level') }}</label>
            <select class="tw-input-sm" wire:model.live="level">
                <option value="">{{ __('messages.logs.filters.all_levels') }}</option>
                @foreach ($levels as $levelOption)
                    <option value="{{ $levelOption }}">{{ strtoupper($levelOption) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-36">
            <label class="tw-label">{{ __('messages.logs.filters.from_date') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="fromDate">
        </div>
        <div class="w-full sm:w-36">
            <label class="tw-label">{{ __('messages.logs.filters.to_date') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="toDate">
        </div>
        <div class="w-full sm:w-24">
            <label class="tw-label">{{ __('messages.logs.filters.per_page') }}</label>
            <select class="tw-input-sm" wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Estadísticas + reset --}}
    <div class="mb-3 flex flex-col items-start justify-between gap-2 sm:flex-row sm:items-center">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ __('messages.logs.total_entries') }}: {{ $totalEntries }} |
            {{ __('messages.logs.filtered_entries') }}: {{ $filteredEntries }}
        </div>
        <button type="button" class="tw-btn-link text-sm" wire:click="resetFilters">
            {{ __('messages.logs.reset_filters') }}
        </button>
    </div>

    {{-- Tabla --}}
    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('messages.logs.columns.datetime') }}</th>
                    <th>{{ __('messages.logs.columns.level') }}</th>
                    <th>{{ __('messages.logs.columns.env') }}</th>
                    <th>{{ __('messages.logs.columns.message') }}</th>
                    <th class="text-center">{{ __('messages.logs.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $entry)
                    <tr>
                        <td class="whitespace-nowrap">{{ $entry['datetime'] }}</td>
                        <td>
                            <span class="tw-badge log-level-{{ $entry['level'] }}">{{ strtoupper($entry['level']) }}</span>
                        </td>
                        <td>{{ $entry['env'] }}</td>
                        <td>
                            <div class="log-message-preview">{{ $entry['message'] }}</div>
                            @if ($entry['context_json'])
                                <small class="text-gray-500">{{ __('messages.logs.has_context') }}</small>
                            @endif
                            @if ($entry['trace'])
                                <small class="text-gray-500">{{ __('messages.logs.has_trace') }}</small>
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" class="tw-btn-xs tw-btn-outline-blue"
                                        wire:click="openEntry('{{ $entry['id'] }}')">
                                    {{ __('messages.logs.view_detail') }}
                                </button>
                                <button type="button" class="tw-btn-xs tw-btn-outline-red"
                                        wire:click="confirmDeleteEntry('{{ $entry['id'] }}')">
                                    {{ __('messages.logs.delete_entry') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('messages.logs.no_results') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
        <div class="mt-3">{{ $logs->links() }}</div>
    @endif

    {{-- Modal: detalle de entrada --}}
    @if ($selectedEntry)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 p-4 pt-16">
            <div class="w-full max-w-4xl rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between rounded-t-xl bg-emerald-600 px-5 py-4">
                    <h5 class="text-base font-semibold text-white">{{ __('messages.logs.detail_title') }}</h5>
                    <button wire:click="closeEntry" class="text-white/80 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                        <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('messages.logs.columns.datetime') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $selectedEntry['datetime'] }}</dd>
                        <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('messages.logs.columns.level') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ strtoupper($selectedEntry['level']) }}</dd>
                        <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('messages.logs.columns.message') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $selectedEntry['message'] }}</dd>
                    </dl>

                    @if ($selectedEntry['context_json'])
                        <h6 class="mb-2 mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.logs.context') }}</h6>
                        <pre class="log-detail">{{ $selectedEntry['context_json'] }}</pre>
                    @endif
                    @if ($selectedEntry['trace'])
                        <h6 class="mb-2 mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.logs.trace') }}</h6>
                        <pre class="log-detail">{{ $selectedEntry['trace'] }}</pre>
                    @endif
                    <h6 class="mb-2 mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.logs.raw') }}</h6>
                    <pre class="log-detail">{{ $selectedEntry['raw'] }}</pre>
                </div>
                <div class="flex justify-end border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                    <button type="button" class="tw-btn tw-btn-gray" wire:click="closeEntry">{{ __('messages.actions.close') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: confirmar limpiar log --}}
    @if ($showClearModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between rounded-t-xl bg-red-600 px-5 py-4">
                    <h5 class="text-base font-semibold text-white">{{ __('messages.logs.clear_modal_title') }}</h5>
                    <button wire:click="cancelClear" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5">
                    <p class="mb-3 text-sm text-gray-700 dark:text-gray-300">{{ __('messages.logs.clear_warning') }}</p>
                    <label class="tw-label">{{ __('messages.logs.clear_confirmation_label') }}</label>
                    <input type="text"
                           class="tw-input @error('clearConfirmation') border-red-500 @enderror"
                           wire:model.live="clearConfirmation">
                    @error('clearConfirmation')
                        <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                    <button type="button" class="tw-btn tw-btn-gray" wire:click="cancelClear">{{ __('messages.actions.cancel') }}</button>
                    <button type="button" class="tw-btn tw-btn-red" wire:click="clearLog" wire:loading.attr="disabled">
                        {{ __('messages.logs.clear_confirm_button') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: confirmar eliminar entrada --}}
    @if ($showDeleteEntryModal && $entryToDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between rounded-t-xl bg-red-600 px-5 py-4">
                    <h5 class="text-base font-semibold text-white">{{ __('messages.logs.delete_entry_modal_title') }}</h5>
                    <button wire:click="cancelDeleteEntry" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5">
                    <p class="mb-3 text-sm text-gray-700 dark:text-gray-300">{{ __('messages.logs.delete_entry_warning') }}</p>
                    <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1.5 text-sm">
                        <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('messages.logs.columns.datetime') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $entryToDelete['datetime'] }}</dd>
                        <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('messages.logs.columns.level') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ strtoupper($entryToDelete['level']) }}</dd>
                        <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('messages.logs.columns.message') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $entryToDelete['message'] }}</dd>
                    </dl>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                    <button type="button" class="tw-btn tw-btn-gray" wire:click="cancelDeleteEntry">{{ __('messages.actions.cancel') }}</button>
                    <button type="button" class="tw-btn tw-btn-red" wire:click="deleteEntry" wire:loading.attr="disabled">
                        {{ __('messages.logs.delete_entry_confirm_button') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .log-message-preview {
            max-width: 720px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .log-detail {
            max-height: 320px;
            overflow: auto;
            padding: 1rem;
            color: #212529;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            white-space: pre-wrap;
        }
        [data-theme="dark"] .log-detail {
            color: #e2e8f0;
            background: #1e293b;
            border-color: #334155;
        }
        .log-level-debug, .log-level-info, .log-level-notice { background: #17a2b8; color: #fff; }
        .log-level-warning { background: #ffc107; color: #212529; }
        .log-level-error, .log-level-critical, .log-level-alert, .log-level-emergency { background: #dc3545; color: #fff; }
    </style>
</div>

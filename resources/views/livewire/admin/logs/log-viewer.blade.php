<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.logs.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ __('mma.admin.logs.file_size') }}: {{ $metadata['formatted_size'] }}
                @if ($metadata['last_modified'])
                    | {{ __('mma.admin.logs.last_modified') }}: {{ $metadata['last_modified'] }}
                @endif
            </div>
        </div>
        @can('logs.download')
            <a href="{{ route('admin.logs.download') }}"
               class="tw-btn tw-btn-outline-emerald {{ $metadata['exists'] ? '' : 'pointer-events-none opacity-50' }}">
                <i class="fas fa-download text-xs"></i>{{ __('mma.admin.logs.actions.download') }}
            </a>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(240px,1fr)_160px_150px_150px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.logs.filters.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.logs.filters.level') }}</label>
            <select class="tw-input-sm" wire:model.live="level">
                <option value="">{{ __('mma.admin.logs.filters.all_levels') }}</option>
                @foreach ($levels as $levelOption)
                    <option value="{{ $levelOption }}">{{ strtoupper($levelOption) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.logs.filters.from_date') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="fromDate">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.logs.filters.to_date') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="toDate">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.per_page') }}</label>
            <select class="tw-input-sm" wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="button" class="tw-btn tw-btn-outline" wire:click="resetFilters">
                <i class="fas fa-eraser text-xs"></i>{{ __('mma.admin.common.filters.clear') }}
            </button>
        </div>
    </div>

    <div class="mb-3 text-xs text-gray-500 dark:text-gray-400">
        {{ __('mma.admin.logs.total_entries') }}: {{ $totalEntries }} |
        {{ __('mma.admin.logs.filtered_entries') }}: {{ $filteredEntries }}
    </div>

    <div class="tw-table-wrap">
        <table class="tw-table">
            <thead class="themed-thead">
                <tr>
                    <th>{{ __('mma.admin.logs.columns.datetime') }}</th>
                    <th>{{ __('mma.admin.logs.columns.level') }}</th>
                    <th>{{ __('mma.admin.logs.columns.env') }}</th>
                    <th>{{ __('mma.admin.logs.columns.message') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $entry)
                    <tr wire:key="log-entry-{{ $entry['id'] }}">
                        <td class="whitespace-nowrap">{{ $entry['datetime'] }}</td>
                        <td><span class="tw-badge tw-badge-gray">{{ strtoupper($entry['level']) }}</span></td>
                        <td>{{ $entry['env'] }}</td>
                        <td>
                            <div class="max-w-[720px] truncate">{{ $entry['message'] }}</div>
                            @if ($entry['context_json'])
                                <small class="text-gray-500">{{ __('mma.admin.logs.has_context') }}</small>
                            @endif
                            @if ($entry['trace'])
                                <small class="text-gray-500">{{ __('mma.admin.logs.has_trace') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="tw-btn-xs tw-btn-outline-blue" wire:click="openEntry('{{ $entry['id'] }}')">
                                <i class="fas fa-eye text-xs"></i>{{ __('mma.admin.logs.actions.view_detail') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
        <div class="mt-3">{{ $logs->links() }}</div>
    @endif

    @if ($selectedEntry)
        <x-tw-modal close="closeEntry" max-width="5xl" :title="__('mma.admin.logs.detail_title')" icon="fas fa-file-alt">
            <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
                <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('mma.admin.logs.columns.datetime') }}</dt>
                <dd class="text-gray-800 dark:text-gray-200">{{ $selectedEntry['datetime'] }}</dd>
                <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('mma.admin.logs.columns.level') }}</dt>
                <dd class="text-gray-800 dark:text-gray-200">{{ strtoupper($selectedEntry['level']) }}</dd>
                <dt class="font-semibold text-gray-600 dark:text-gray-400">{{ __('mma.admin.logs.columns.message') }}</dt>
                <dd class="text-gray-800 dark:text-gray-200">{{ $selectedEntry['message'] }}</dd>
            </dl>

            @if ($selectedEntry['context_json'])
                <h6 class="mb-2 mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('mma.admin.logs.context') }}</h6>
                <pre class="max-h-80 overflow-auto rounded-md bg-gray-100 p-4 text-xs text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ $selectedEntry['context_json'] }}</pre>
            @endif
            @if ($selectedEntry['trace'])
                <h6 class="mb-2 mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('mma.admin.logs.trace') }}</h6>
                <pre class="max-h-80 overflow-auto rounded-md bg-gray-100 p-4 text-xs text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ $selectedEntry['trace'] }}</pre>
            @endif
            <h6 class="mb-2 mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('mma.admin.logs.raw') }}</h6>
            <pre class="max-h-80 overflow-auto rounded-md bg-gray-100 p-4 text-xs text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ $selectedEntry['raw'] }}</pre>

            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeEntry">{{ __('messages.actions.close') }}</button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>

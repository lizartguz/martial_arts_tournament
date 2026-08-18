<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.ticket_links.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.ticket_links.table_subtitle') }}</div>
        </div>
        @can('ticket_links.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.ticket_links.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_260px_180px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.ticket_links.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.ticket_links.filters.event') }}</label>
            <select class="tw-input-sm" wire:model.live="eventId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.ticket_links.filters.sale_channel') }}</label>
            <select class="tw-input-sm" wire:model.live="saleChannel">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->saleChannelOptions() as $value => $label)
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
                    <th>{{ __('mma.admin.ticket_links.columns.link') }}</th>
                    <th>{{ __('mma.admin.ticket_links.columns.event') }}</th>
                    <th>{{ __('mma.admin.ticket_links.columns.channel') }}</th>
                    <th>{{ __('mma.admin.ticket_links.columns.price') }}</th>
                    <th>{{ __('mma.admin.ticket_links.columns.window') }}</th>
                    <th>{{ __('mma.admin.ticket_links.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ticketLinks as $ticketLink)
                    <tr wire:key="ticket-link-{{ $ticketLink->id }}">
                        <td>
                            <div class="min-w-[240px]">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $ticketLink->label }}</div>
                                <div class="text-xs text-gray-500">{{ $ticketLink->provider_name }}</div>
                                <a href="{{ $ticketLink->url }}" target="_blank" rel="noopener" class="mt-1 inline-flex max-w-[280px] truncate text-xs text-blue-600 hover:underline">
                                    {{ $ticketLink->url }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $ticketLink->event?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($ticketLink->event?->starts_at)
                                <div class="text-xs text-gray-500">{{ $ticketLink->event->starts_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge tw-badge-blue">{{ $this->saleChannelLabel($ticketLink->sale_channel) }}</span>
                        </td>
                        <td>
                            @if ($ticketLink->price_from !== null)
                                {{ $ticketLink->currency }} {{ number_format((float) $ticketLink->price_from, 2) }}
                            @else
                                {{ __('mma.admin.common.not_available') }}
                            @endif
                        </td>
                        <td>
                            <div class="text-sm">{{ $ticketLink->starts_at?->format('Y-m-d H:i') ?? __('mma.admin.ticket_links.open_start') }}</div>
                            <div class="text-xs text-gray-500">{{ $ticketLink->ends_at?->format('Y-m-d H:i') ?? __('mma.admin.ticket_links.open_end') }}</div>
                        </td>
                        <td>
                            <span class="tw-badge {{ (int) $ticketLink->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $ticketLink->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('ticket_links.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $ticketLink->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('ticket_links.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-gray-700"
                                                wire:click="confirmDelete({{ $ticketLink->id }})" @click="open = false">
                                            <i class="fas fa-trash-alt mr-2"></i>{{ __('messages.actions.delete') }}
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

    @if ($ticketLinks->hasPages())
        <div class="mt-3">{{ $ticketLinks->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="$editingId ? __('mma.admin.ticket_links.edit') : __('mma.admin.ticket_links.create')" icon="fas fa-link">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.event_id') }}</label>
                    <select class="tw-input @error('form.event_id') border-red-500 @enderror" wire:model.live="form.event_id">
                        <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                    @error('form.event_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.provider_name') }}</label>
                    <input type="text" class="tw-input @error('form.provider_name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.provider_name">
                    @error('form.provider_name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.label') }}</label>
                    <input type="text" class="tw-input @error('form.label') border-red-500 @enderror" wire:model.live.debounce.300ms="form.label">
                    @error('form.label') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.sale_channel') }}</label>
                    <select class="tw-input @error('form.sale_channel') border-red-500 @enderror" wire:model.live="form.sale_channel">
                        @foreach ($this->saleChannelOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.sale_channel') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.url') }}</label>
                    <input type="url" class="tw-input @error('form.url') border-red-500 @enderror" wire:model.live.debounce.300ms="form.url">
                    @error('form.url') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.price_from') }}</label>
                    <input type="number" step="0.01" min="0" class="tw-input @error('form.price_from') border-red-500 @enderror" wire:model.live="form.price_from">
                    @error('form.price_from') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.currency') }}</label>
                    <select class="tw-input @error('form.currency') border-red-500 @enderror" wire:model.live="form.currency">
                        @foreach ($this->currencyOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.currency') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.starts_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.starts_at') border-red-500 @enderror" wire:model.live="form.starts_at">
                    @error('form.starts_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.ends_at') }}</label>
                    <input type="datetime-local" class="tw-input @error('form.ends_at') border-red-500 @enderror" wire:model.live="form.ends_at">
                    @error('form.ends_at') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.display_order') }}</label>
                    <input type="number" min="0" class="tw-input @error('form.display_order') border-red-500 @enderror" wire:model.live="form.display_order">
                    @error('form.display_order') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="tw-label">{{ __('mma.admin.ticket_links.form.status') }}</label>
                    <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                        <option value="1">{{ __('mma.admin.common.active') }}</option>
                        <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                    </select>
                    @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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
        <x-tw-modal close="closeDeleteModal" max-width="md" :title="__('mma.admin.ticket_links.delete_title')" icon="fas fa-trash" header-class="bg-red-600 text-white">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                {{ __('mma.admin.ticket_links.delete_warning') }} <strong>{{ $deleteName }}</strong>
            </p>

            <x-slot:footer>
                <button type="button" class="tw-btn tw-btn-gray" wire:click="closeDeleteModal">{{ __('messages.actions.cancel') }}</button>
                <button type="button" class="tw-btn tw-btn-red" wire:click="delete" wire:loading.attr="disabled">
                    <i class="fas fa-trash-alt text-xs"></i>{{ __('messages.actions.delete') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>

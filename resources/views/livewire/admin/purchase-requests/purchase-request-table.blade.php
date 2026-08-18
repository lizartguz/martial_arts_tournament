<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.purchase_requests.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.purchase_requests.table_subtitle') }}</div>
        </div>
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_150px_170px_140px_150px_140px_140px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.purchase_requests.search_placeholder') }}">
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
            <label class="tw-label">{{ __('mma.admin.purchase_requests.filters.request_type') }}</label>
            <select class="tw-input-sm" wire:model.live="requestType">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->requestTypeOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.purchase_requests.filters.channel') }}</label>
            <select class="tw-input-sm" wire:model.live="preferredChannel">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->channelOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.purchase_requests.filters.assigned_to') }}</label>
            <select class="tw-input-sm" wire:model.live="assignedTo">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                <option value="unassigned">{{ __('mma.admin.purchase_requests.assignment.unassigned') }}</option>
                <option value="me">{{ __('mma.admin.purchase_requests.assignment.me') }}</option>
                @foreach ($assignableUsers as $user)
                    <option value="{{ $user->id }}">{{ trim($user->name.' '.$user->lastname) ?: $user->email }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.purchase_requests.filters.from') }}</label>
            <input type="date" class="tw-input-sm" wire:model.live="dateFrom">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.purchase_requests.filters.to') }}</label>
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
                    <th>{{ __('mma.admin.purchase_requests.columns.request') }}</th>
                    <th>{{ __('mma.admin.purchase_requests.columns.contact') }}</th>
                    <th>{{ __('mma.admin.purchase_requests.columns.related_to') }}</th>
                    <th>{{ __('mma.admin.purchase_requests.columns.status') }}</th>
                    <th>{{ __('mma.admin.purchase_requests.columns.assigned_to') }}</th>
                    <th>{{ __('mma.admin.purchase_requests.columns.created_at') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchaseRequests as $purchaseRequest)
                    @php
                        $assignedName = $purchaseRequest->assignedUser
                            ? (trim($purchaseRequest->assignedUser->name.' '.$purchaseRequest->assignedUser->lastname) ?: $purchaseRequest->assignedUser->email)
                            : null;
                    @endphp
                    <tr wire:key="purchase-request-{{ $purchaseRequest->id }}">
                        <td>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $this->requestTypeLabel($purchaseRequest->request_type) }}</div>
                            <div class="text-xs text-gray-500">{{ $purchaseRequest->uuid }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $this->channelLabel($purchaseRequest->preferred_channel) }}</div>
                        </td>
                        <td>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $purchaseRequest->contact_name }}</div>
                            @if ($purchaseRequest->contact_email)
                                <div class="text-xs text-gray-500">{{ $purchaseRequest->contact_email }}</div>
                            @endif
                            @if ($purchaseRequest->contact_phone || $purchaseRequest->contact_whatsapp)
                                <div class="text-xs text-gray-500">
                                    {{ $purchaseRequest->contact_whatsapp ?: $purchaseRequest->contact_phone }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm text-gray-800 dark:text-gray-100">{{ $purchaseRequest->event?->name ?? $purchaseRequest->plan?->name ?? __('mma.admin.common.not_available') }}</div>
                            @if ($purchaseRequest->payment_proof_path)
                                <a href="{{ route('admin.purchase-requests.proof', $purchaseRequest) }}" target="_blank"
                                   class="mt-1 inline-flex items-center gap-1 text-xs text-emerald-700 hover:underline dark:text-emerald-300">
                                    <i class="fas fa-paperclip"></i>{{ __('mma.admin.purchase_requests.proof.available') }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <span class="tw-badge {{ $this->statusBadge((int) $purchaseRequest->status) }}">
                                {{ $this->statusLabel((int) $purchaseRequest->status) }}
                            </span>
                        </td>
                        <td>{{ $assignedName ?? __('mma.admin.purchase_requests.assignment.unassigned') }}</td>
                        <td>
                            <div>{{ $purchaseRequest->created_at->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500">{{ $purchaseRequest->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('purchase_requests.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="manage({{ $purchaseRequest->id }})" @click="open = false">
                                            <i class="fas fa-clipboard-check mr-2 text-blue-500"></i>{{ __('mma.admin.purchase_requests.actions.manage') }}
                                        </button>
                                    @endcan
                                    @can('purchase_requests.assign')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="assignToMe({{ $purchaseRequest->id }})" @click="open = false">
                                            <i class="fas fa-user-check mr-2 text-emerald-500"></i>{{ __('mma.admin.purchase_requests.actions.assign_to_me') }}
                                        </button>
                                    @endcan
                                    @can('purchase_requests.close')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="closeRequest({{ $purchaseRequest->id }})" @click="open = false">
                                            <i class="fas fa-check-circle mr-2 text-gray-500"></i>{{ __('mma.admin.purchase_requests.actions.close') }}
                                        </button>
                                    @endcan
                                    @can('purchase_requests.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $purchaseRequest->id }})" @click="open = false">
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

    @if ($purchaseRequests->hasPages())
        <div class="mt-3">{{ $purchaseRequests->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="5xl" :title="__('mma.admin.purchase_requests.modal_title')" icon="fas fa-inbox">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="rounded border border-gray-200 p-3 dark:border-gray-700">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.purchase_requests.details.title') }}</h3>
                    <dl class="grid gap-3 text-sm md:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.contact') }}</dt>
                            <dd>{{ $selectedSummary['contact_name'] ?? '' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.request_type') }}</dt>
                            <dd>{{ $this->requestTypeLabel($selectedSummary['request_type'] ?? null) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.email') }}</dt>
                            <dd>{{ $selectedSummary['contact_email'] ?? __('mma.admin.common.not_available') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.channel') }}</dt>
                            <dd>{{ $this->channelLabel($selectedSummary['preferred_channel'] ?? null) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.phone') }}</dt>
                            <dd>{{ $selectedSummary['contact_whatsapp'] ?? $selectedSummary['contact_phone'] ?? __('mma.admin.common.not_available') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.related_to') }}</dt>
                            <dd>{{ $selectedSummary['event'] ?? $selectedSummary['plan'] ?? __('mma.admin.common.not_available') }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.message') }}</dt>
                            <dd class="whitespace-pre-line">{{ $selectedSummary['message'] ?? __('mma.admin.common.not_available') }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-xs font-semibold uppercase text-gray-500">{{ __('mma.admin.purchase_requests.details.proof') }}</dt>
                            <dd>
                                @if ($selectedSummary['payment_proof_path'] ?? null)
                                    <a href="{{ route('admin.purchase-requests.proof', $editingId) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-emerald-700 hover:underline dark:text-emerald-300">
                                        <i class="fas fa-paperclip"></i>{{ __('mma.admin.purchase_requests.proof.open') }}
                                    </a>
                                    <span class="ml-2 text-xs text-gray-500">{{ $this->fileSizeLabel($selectedSummary['payment_proof_size'] ?? null) }}</span>
                                @else
                                    {{ __('mma.admin.purchase_requests.proof.none') }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.purchase_requests.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            @foreach ($this->statusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.purchase_requests.form.assigned_to') }}</label>
                        <select class="tw-input @error('form.assigned_to') border-red-500 @enderror" wire:model.live="form.assigned_to">
                            <option value="">{{ __('mma.admin.purchase_requests.assignment.unassigned') }}</option>
                            @foreach ($assignableUsers as $user)
                                <option value="{{ $user->id }}">{{ trim($user->name.' '.$user->lastname) ?: $user->email }}</option>
                            @endforeach
                        </select>
                        @error('form.assigned_to') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.purchase_requests.form.notes') }}</label>
                        <textarea rows="8" class="tw-input @error('form.notes') border-red-500 @enderror" wire:model.live.debounce.400ms="form.notes"></textarea>
                        @error('form.notes') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
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
            :title="__('mma.admin.purchase_requests.delete_title')"
            :message="__('mma.admin.purchase_requests.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>

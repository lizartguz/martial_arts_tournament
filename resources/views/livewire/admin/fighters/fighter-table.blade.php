<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="tw-panel-header">
        <div>
            <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.fighters.table_title') }}</strong>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fighters.table_subtitle') }}</div>
        </div>
        @can('fighters.create')
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('mma.admin.fighters.create') }}
            </button>
        @endcan
    </div>

    <div class="mb-4 grid gap-3 xl:grid-cols-[minmax(220px,1fr)_140px_140px_180px_180px_110px_auto]">
        <div>
            <label class="tw-label">{{ __('mma.admin.common.filters.search') }}</label>
            <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('mma.admin.fighters.search_placeholder') }}">
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fighters.filters.gender') }}</label>
            <select class="tw-input-sm" wire:model.live="gender">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($this->genderOptions() as $value => $label)
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
            <label class="tw-label">{{ __('mma.admin.fighters.filters.weight_class') }}</label>
            <select class="tw-input-sm" wire:model.live="weightClassId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($weightClasses as $weightClass)
                    <option value="{{ $weightClass->id }}">{{ $weightClass->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="tw-label">{{ __('mma.admin.fighters.filters.team') }}</label>
            <select class="tw-input-sm" wire:model.live="teamId">
                <option value="">{{ __('mma.admin.common.filters.all') }}</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
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
                    <th>{{ __('mma.admin.fighters.columns.fighter') }}</th>
                    <th>{{ __('mma.admin.fighters.columns.team') }}</th>
                    <th>{{ __('mma.admin.fighters.columns.weight_class') }}</th>
                    <th>{{ __('mma.admin.fighters.columns.record') }}</th>
                    <th>{{ __('mma.admin.fighters.columns.fights') }}</th>
                    <th>{{ __('mma.admin.fighters.columns.status') }}</th>
                    <th class="text-center">{{ __('mma.admin.common.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fighters as $fighter)
                    @php
                        $fullName = trim($fighter->first_name.' '.$fighter->last_name);
                        $fightsCount = $fighter->red_corner_fights_count + $fighter->blue_corner_fights_count;
                    @endphp
                    <tr wire:key="fighter-{{ $fighter->id }}">
                        <td>
                            <div class="flex min-w-[260px] items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-700">
                                    @if ($fighter->profile_image)
                                        <img src="{{ $fighter->profileImageUrl() }}" alt="{{ $fullName }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $fullName }}</div>
                                    <div class="text-xs text-gray-500">{{ $fighter->nickname ? '"'.$fighter->nickname.'"' : $fighter->slug }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $this->genderLabel($fighter->gender) }} · {{ $fighter->country?->name ?? __('mma.admin.common.not_available') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $fighter->team?->name ?? __('mma.admin.common.not_available') }}</td>
                        <td>{{ $fighter->weightClass?->name ?? __('mma.admin.common.not_available') }}</td>
                        <td>
                            <span class="tw-badge tw-badge-blue">
                                {{ __('mma.admin.fighters.record_summary', ['wins' => $fighter->wins, 'losses' => $fighter->losses, 'draws' => $fighter->draws, 'nc' => $fighter->no_contests]) }}
                            </span>
                        </td>
                        <td>{{ $fightsCount }}</td>
                        <td>
                            <span class="tw-badge {{ (int) $fighter->status === 1 ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                {{ $this->statusLabel((int) $fighter->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center" x-data="tableActionMenu()" @click.outside="close()">
                                <button type="button" class="tw-btn-xs tw-btn-outline" x-ref="trigger" @click="toggle($refs.trigger)">
                                    <i class="fas fa-ellipsis-v"></i>{{ __('mma.admin.common.columns.actions') }}
                                </button>
                                <div x-show="open" x-cloak
                                     x-bind:style="style" class="fixed z-[80] rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    @can('fighters.update')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                                                wire:click="edit({{ $fighter->id }})" @click="open = false">
                                            <i class="fas fa-pencil-alt mr-2 text-blue-500"></i>{{ __('messages.actions.edit') }}
                                        </button>
                                    @endcan
                                    @can('fighters.delete')
                                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                wire:click="confirmDelete({{ $fighter->id }})" @click="open = false">
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

    @if ($fighters->hasPages())
        <div class="mt-3">{{ $fighters->links() }}</div>
    @endif

    @if ($showModal)
        <x-tw-modal close="closeModal" max-width="7xl" :title="$editingId ? __('mma.admin.fighters.edit') : __('mma.admin.fighters.create')" icon="fas fa-user">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.first_name') }}</label>
                        <input type="text" class="tw-input @error('form.first_name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.first_name">
                        @error('form.first_name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.last_name') }}</label>
                        <input type="text" class="tw-input @error('form.last_name') border-red-500 @enderror" wire:model.live.debounce.300ms="form.last_name">
                        @error('form.last_name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.nickname') }}</label>
                        <input type="text" class="tw-input @error('form.nickname') border-red-500 @enderror" wire:model.live.debounce.300ms="form.nickname">
                        @error('form.nickname') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.slug') }}</label>
                        <input type="text" class="tw-input @error('form.slug') border-red-500 @enderror" wire:model.live.debounce.300ms="form.slug">
                        @error('form.slug') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.gender') }}</label>
                        <select class="tw-input @error('form.gender') border-red-500 @enderror" wire:model.live="form.gender">
                            @foreach ($this->genderOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.gender') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.status') }}</label>
                        <select class="tw-input @error('form.status') border-red-500 @enderror" wire:model.live="form.status">
                            <option value="1">{{ __('mma.admin.common.active') }}</option>
                            <option value="0">{{ __('mma.admin.common.inactive') }}</option>
                        </select>
                        @error('form.status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.country_id') }}</label>
                        <select class="tw-input @error('form.country_id') border-red-500 @enderror" wire:model.live="form.country_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('form.country_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.city_id') }}</label>
                        <select class="tw-input @error('form.city_id') border-red-500 @enderror" wire:model.live="form.city_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('form.city_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.fighter_team_id') }}</label>
                        <select class="tw-input @error('form.fighter_team_id') border-red-500 @enderror" wire:model.live="form.fighter_team_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('form.fighter_team_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.weight_class_id') }}</label>
                        <select class="tw-input @error('form.weight_class_id') border-red-500 @enderror" wire:model.live="form.weight_class_id">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($weightClasses as $weightClass)
                                <option value="{{ $weightClass->id }}">{{ $weightClass->name }} · {{ $this->genderLabel($weightClass->gender) }}</option>
                            @endforeach
                        </select>
                        @error('form.weight_class_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.birthdate') }}</label>
                        <input type="date" class="tw-input @error('form.birthdate') border-red-500 @enderror" wire:model.live="form.birthdate">
                        @error('form.birthdate') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.stance') }}</label>
                        <select class="tw-input @error('form.stance') border-red-500 @enderror" wire:model.live="form.stance">
                            <option value="">{{ __('mma.admin.common.not_available') }}</option>
                            @foreach ($this->stanceOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.stance') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.height_cm') }}</label>
                        <input type="number" step="0.01" min="0" class="tw-input @error('form.height_cm') border-red-500 @enderror" wire:model.live="form.height_cm">
                        @error('form.height_cm') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.reach_cm') }}</label>
                        <input type="number" step="0.01" min="0" class="tw-input @error('form.reach_cm') border-red-500 @enderror" wire:model.live="form.reach_cm">
                        @error('form.reach_cm') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.wins') }}</label>
                        <input type="number" min="0" class="tw-input @error('form.wins') border-red-500 @enderror" wire:model.live="form.wins">
                        @error('form.wins') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.losses') }}</label>
                        <input type="number" min="0" class="tw-input @error('form.losses') border-red-500 @enderror" wire:model.live="form.losses">
                        @error('form.losses') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.draws') }}</label>
                        <input type="number" min="0" class="tw-input @error('form.draws') border-red-500 @enderror" wire:model.live="form.draws">
                        @error('form.draws') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.no_contests') }}</label>
                        <input type="number" min="0" class="tw-input @error('form.no_contests') border-red-500 @enderror" wire:model.live="form.no_contests">
                        @error('form.no_contests') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="tw-label">{{ __('mma.admin.fighters.form.bio') }}</label>
                        <textarea rows="5" class="tw-input @error('form.bio') border-red-500 @enderror" wire:model.live.debounce.400ms="form.bio"></textarea>
                        @error('form.bio') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.profile_image') }}</label>
                        <input type="file" class="tw-input @error('profileImage') border-red-500 @enderror" wire:model="profileImage" accept="image/jpeg,image/png,image/webp">
                        @error('profileImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if ($profileImage)
                            <img src="{{ $profileImage->temporaryUrl() }}" alt="{{ __('mma.admin.fighters.form.profile_image') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @elseif ($currentProfileImage)
                            <img src="{{ \App\Support\PublicMedia::url($currentProfileImage) }}" alt="{{ __('mma.admin.fighters.form.profile_image') }}" class="mt-2 h-40 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @endif
                    </div>
                    <div>
                        <label class="tw-label">{{ __('mma.admin.fighters.form.cover_image') }}</label>
                        <input type="file" class="tw-input @error('coverImage') border-red-500 @enderror" wire:model="coverImage" accept="image/jpeg,image/png,image/webp">
                        @error('coverImage') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if ($coverImage)
                            <img src="{{ $coverImage->temporaryUrl() }}" alt="{{ __('mma.admin.fighters.form.cover_image') }}" class="mt-2 h-32 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @elseif ($currentCoverImage)
                            <img src="{{ \App\Support\PublicMedia::url($currentCoverImage) }}" alt="{{ __('mma.admin.fighters.form.cover_image') }}" class="mt-2 h-32 w-full rounded border border-gray-200 object-cover dark:border-gray-700">
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.fighters.image_help') }}</p>
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
            :title="__('mma.admin.fighters.delete_title')"
            :message="__('mma.admin.fighters.delete_warning')"
            :target-name="$deleteName"
        />
    @endif
</div>

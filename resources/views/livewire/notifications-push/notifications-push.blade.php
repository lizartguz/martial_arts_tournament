<div>
    {{-- Overlay de reenvío --}}
    <div class="notification-resend-overlay" wire:loading.flex wire:target="resendPush" aria-live="assertive" aria-busy="true">
        <div class="notification-resend-status">
            <span class="tw-spinner"></span>
            <div>
                <strong>{{ __('messages.notices.resend_overlay.title') }}</strong>
                <small>{{ __('messages.notices.resend_overlay.subtitle') }}</small>
            </div>
        </div>
    </div>

    {{-- Filtros de tabla (siempre visibles) --}}
    <div class="tw-panel-header">
        <div class="flex flex-wrap items-end gap-3 flex-1">
            <div>
                <label for="perPage" class="tw-label">{{ __('messages.notices.filters.per_page') }}</label>
                <select id="perPage" wire:model.live="perPage" class="tw-input-sm" style="width:auto" @if($formAdd) disabled @endif>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div>
                <label for="stateChange" class="tw-label">{{ __('messages.notices.filters.status') }}</label>
                <select id="stateChange" wire:model.live="stateChange" class="tw-input-sm" style="width:auto" @if($formAdd) disabled @endif>
                    <option value="all">{{ __('messages.notices.filters.all') }}</option>
                    <option value="1">{{ __('messages.notices.filters.active') }}</option>
                    <option value="0">{{ __('messages.notices.filters.inactive') }}</option>
                </select>
            </div>
            <div class="sm:flex-1">
                <label for="searchTitle" class="tw-label">{{ __('messages.notices.filters.search') }}</label>
                <input type="text" id="searchTitle" wire:model.live="searchTitle"
                       class="tw-input-sm w-full" placeholder="{{ __('messages.notices.filters.search_placeholder') }}"
                       @if($formAdd) disabled @endif>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div wire:loading wire:target="saveNotification,changeStatus,deleteNotif,visibleAdd,noVisibleAdd,editNotif">
                <span class="tw-spinner"></span>
            </div>
            @if(!$formAdd)
                <button class="tw-btn tw-btn-emerald" wire:click="visibleAdd">
                    <i class="fa fa-plus text-xs"></i> {{ __('messages.notices.buttons.new') }}
                </button>
            @else
                <button class="tw-btn tw-btn-gray" wire:click="noVisibleAdd">{{ __('messages.notices.buttons.close_form') }}</button>
            @endif
        </div>
    </div>

    @if($formAdd)
        {{-- Formulario de creación/edición --}}
        <div class="notification-form-shell" wire:key="notifications-form-card">
            <div class="notification-form-hero">
                <div>
                    <span class="notification-form-kicker">{{ __('messages.notices.form.kicker') }}</span>
                    <h4>{{ $formUpdate ? __('messages.notices.form.title_edit') : __('messages.notices.form.title_new') }}</h4>
                    <p>{{ __('messages.notices.form.intro') }}</p>
                </div>
                <div class="notification-form-status">
                    <span class="status-dot {{ $stateAU == 1 ? 'active' : 'inactive' }}"></span>
                    {{ $stateAU == 1 ? __('messages.notices.status.active') : __('messages.notices.status.inactive') }}
                </div>
            </div>

            <form class="notification-form-body"
                  action="{{ route('notificationsp') }}"
                  wire:submit.prevent="saveNotification"
                  enctype="multipart/form-data">

                {{-- Sección: contenido principal --}}
                <div class="notification-form-section">
                    <div class="notification-section-title">
                        <i class="fa fa-bullhorn"></i>
                        <div>
                            <strong>{{ __('messages.notices.form.section_main') }}</strong>
                            <small>{{ __('messages.notices.form.section_main_hint') }}</small>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <div class="w-full md:flex-1">
                            <label for="titleAU" class="tw-label">{{ __('messages.notices.form.field_title') }}</label>
                            <input type="text" id="titleAU" wire:model="titleAU" class="tw-input" maxlength="255" required>
                            @error('titleAU') <small class="text-red-600">{{ $message }}</small> @enderror
                        </div>
                        <div class="w-full sm:w-44">
                            <label for="scheduledAtAU" class="tw-label">{{ __('messages.notices.form.field_scheduled') }}</label>
                            <input type="datetime-local" id="scheduledAtAU" wire:model="scheduledAtAU" class="tw-input">
                            @error('scheduledAtAU') <small class="text-red-600">{{ $message }}</small> @enderror
                        </div>
                        <div class="w-full sm:w-36">
                            <label for="deadlineAU" class="tw-label">{{ __('messages.notices.form.field_deadline') }}</label>
                            <input type="date" id="deadlineAU" wire:model="deadlineAU" class="tw-input" required>
                            @error('deadlineAU') <small class="text-red-600">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="descriptionAU" class="tw-label">{{ __('messages.notices.form.field_description') }}</label>
                        <textarea id="descriptionAU" wire:model="descriptionAU" class="tw-input" rows="5" maxlength="500" required></textarea>
                        <div class="flex justify-between">
                            @error('descriptionAU')
                                <small class="text-red-600">{{ $message }}</small>
                            @else
                                <small class="text-gray-500">{{ __('messages.notices.form.description_hint') }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: destino y comportamiento --}}
                <div class="notification-form-section compact">
                    <div class="notification-section-title">
                        <i class="fa fa-sliders-h"></i>
                        <div>
                            <strong>{{ __('messages.notices.form.section_target') }}</strong>
                            <small>{{ __('messages.notices.form.section_target_hint') }}</small>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <div class="w-full md:flex-1">
                            <label for="linkAU" class="tw-label">{{ __('messages.notices.form.field_link') }}</label>
                            <input type="url" id="linkAU" wire:model="linkAU" class="tw-input" placeholder="https://...">
                            @error('linkAU') <small class="text-red-600">{{ $message }}</small> @enderror
                        </div>
                        <div class="w-full sm:w-36">
                            <label for="stateAU" class="tw-label">{{ __('messages.notices.form.field_status') }}</label>
                            <select id="stateAU" class="tw-input" wire:model="stateAU">
                                <option value="1">{{ __('messages.notices.status.active') }}</option>
                                <option value="0">{{ __('messages.notices.status.inactive') }}</option>
                            </select>
                            @error('stateAU') <small class="text-red-600">{{ $message }}</small> @enderror
                        </div>
                        <div class="w-full sm:w-40">
                            <label for="deliveryPlatformAU" class="tw-label">{{ __('messages.notices.form.field_delivery') }}</label>
                            <select id="deliveryPlatformAU" class="tw-input" wire:model="deliveryPlatformAU">
                                <option value="all">{{ __('messages.notices.form.delivery_all') }}</option>
                                <option value="mobile">{{ __('messages.notices.form.delivery_mobile') }}</option>
                                <option value="web">{{ __('messages.notices.form.delivery_web') }}</option>
                            </select>
                            @error('deliveryPlatformAU') <small class="text-red-600">{{ $message }}</small> @enderror
                        </div>
                        <div class="w-full sm:w-48">
                            <label class="tw-label">{{ __('messages.notices.form.field_recipients') }}</label>
                            <div class="notification-recipient-summary">
                                @if(count($selectedUsersPreview) > 0)
                                    <strong>{{ count($selectedUsersPreview) }}</strong>&nbsp;{{ __('messages.notices.form.recipients_added') }}
                                @else
                                    <strong>{{ __('messages.notices.form.recipients_all') }}</strong>
                                @endif
                            </div>
                            @error('selectedUserIds.*') <small class="block text-red-600">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-4">
                        <div class="w-full sm:w-52">
                            <label for="roleFilter" class="tw-label">{{ __('messages.notices.form.field_role_filter') }}</label>
                            <select id="roleFilter" class="tw-input" wire:model.live="roleFilter">
                                <option value="">{{ __('messages.notices.form.role_all') }}</option>
                                @foreach($roleOptions as $role)
                                    <option value="{{ $role['value'] }}">{{ $role['label'] }}</option>
                                @endforeach
                            </select>
                            <small class="text-gray-500">{{ __('messages.notices.form.role_filter_hint') }}</small>
                        </div>
                        <div class="flex-1">
                            <label for="userSearch" class="tw-label">{{ __('messages.notices.form.field_user_search') }}</label>
                            <input type="text" id="userSearch"
                                   wire:model.live.debounce.300ms="userSearch"
                                   class="tw-input mb-1"
                                   placeholder="{{ __('messages.notices.form.user_search_placeholder') }}">
                            <small class="text-gray-500">{{ __('messages.notices.form.user_search_hint') }}</small>

                            <div class="notification-user-search-box mt-2" aria-live="polite">
                                <div wire:loading.flex wire:target="userSearch,roleFilter" class="notification-user-search-loading">
                                    <span class="tw-spinner-sm mr-2"></span>
                                    <span>{{ __('messages.notices.form.searching_users') }}</span>
                                </div>
                                <div wire:loading.remove wire:target="userSearch,roleFilter">
                                    @if(trim($userSearch) === '' && $roleFilter === '')
                                        <div class="notification-user-search-empty">
                                            {{ __('messages.notices.form.search_start_hint') }}
                                        </div>
                                    @elseif(count($userSearchResults) === 0)
                                        <div class="notification-user-search-empty">
                                            {{ __('messages.notices.form.no_users_found') }}
                                        </div>
                                    @else
                                        <div class="notification-user-search-status">
                                            <i class="fa fa-check-circle mr-1"></i>
                                            {{ count($userSearchResults) }}
                                            {{ count($userSearchResults) === 1 ? __('messages.notices.form.match_found') : __('messages.notices.form.matches_found') }}
                                        </div>
                                        <div class="notification-user-search-results">
                                            @foreach($userSearchResults as $user)
                                                <div class="notification-user-result" wire:key="notification-user-result-{{ $user['id'] }}">
                                                    <div class="notification-user-result-body">
                                                        <strong>{{ $user['label'] }}</strong>
                                                        <small>
                                                            {{ $user['email'] ?: __('messages.notices.form.no_email') }}
                                                            @if(!empty($user['roles']))
                                                                | {{ implode(', ', $user['roles']) }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <button type="button"
                                                            class="tw-btn-xs tw-btn-outline-emerald"
                                                            wire:click="addSelectedUser({{ $user['id'] }})"
                                                            title="{{ __('messages.notices.form.add_user') }}">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="tw-label">{{ __('messages.notices.form.added_users') }}</label>
                        <div class="notification-selected-users">
                            @forelse($selectedUsersPreview as $user)
                                <span class="notification-user-chip" wire:key="notification-selected-user-{{ $user['id'] }}">
                                    <span>
                                        {{ $user['label'] }}
                                        @if($user['email'])
                                            <small>{{ $user['email'] }}</small>
                                        @endif
                                    </span>
                                    <button type="button"
                                            class="notification-user-chip-remove"
                                            wire:click="removeSelectedUser({{ $user['id'] }})"
                                            title="{{ __('messages.notices.form.remove_user') }}">&times;</button>
                                </span>
                            @empty
                                <div class="notification-user-search-empty">
                                    {{ __('messages.notices.form.no_users_hint') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sección: adjuntos visuales --}}
                <div class="notification-form-section media">
                    <div>
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <div class="notification-section-title mb-0">
                                <i class="fa fa-images"></i>
                                <div>
                                    <strong>{{ __('messages.notices.form.section_media') }}</strong>
                                    <small>{{ __('messages.notices.form.section_media_hint') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @php
                                $imageSlots = [
                                    ['number' => 1, 'slot' => 'image',   'model' => 'imageAU',  'current' => $currentImage,  'label' => __('messages.notices.form.image_label', ['number' => 1])],
                                    ['number' => 2, 'slot' => 'image_1', 'model' => 'image1AU', 'current' => $currentImage1, 'label' => __('messages.notices.form.image_label', ['number' => 2])],
                                    ['number' => 3, 'slot' => 'image_2', 'model' => 'image2AU', 'current' => $currentImage2, 'label' => __('messages.notices.form.image_label', ['number' => 3])],
                                ];
                            @endphp
                            @foreach($imageSlots as $slot)
                                @php
                                    $selectedImage = match ($slot['number']) { 1 => $imageAU, 2 => $image1AU, default => $image2AU };
                                    $previewUrl = null;
                                    if ($selectedImage) { try { $previewUrl = $selectedImage->temporaryUrl(); } catch (\Throwable $th) {} }
                                    $currentUrl = $slot['current'] ? $this->getImageUrl($slot['slot']) : null;
                                    $imageUrl = $previewUrl ?: $currentUrl;
                                    $inputId = 'notification-' . $slot['model'];
                                @endphp
                                <div wire:key="notification-image-slot-{{ $slot['model'] }}">
                                    <div class="notification-image-card relative overflow-hidden rounded-xl border">
                                        <input type="file" id="{{ $inputId }}"
                                               data-notification-image-input="{{ $slot['model'] }}"
                                               wire:model="{{ $slot['model'] }}"
                                               accept="image/jpeg,image/png,image/webp,image/gif"
                                               class="hidden">
                                        <label for="{{ $inputId }}" class="notification-image-dropzone mb-0">
                                            <span class="notification-image-local-preview hidden" data-notification-image-preview="{{ $slot['model'] }}">
                                                <img src="" alt="{{ $slot['label'] }}" class="notification-image-preview">
                                                <span class="notification-image-overlay show">
                                                    <i class="fa fa-check-circle mr-1"></i> {{ __('messages.notices.form.image_selected') }}
                                                </span>
                                            </span>
                                            <span data-notification-image-server="{{ $slot['model'] }}">
                                                @if($imageUrl)
                                                    <img src="{{ $imageUrl }}" alt="{{ $slot['label'] }}" class="notification-image-preview">
                                                    <span class="notification-image-overlay">
                                                        <i class="fa fa-camera mr-1"></i> {{ __('messages.notices.form.image_replace') }}
                                                    </span>
                                                @elseif($selectedImage)
                                                    <span class="notification-image-selected">
                                                        <i class="fa fa-check-circle"></i>
                                                        <strong>{{ __('messages.notices.form.image_selected') }}</strong>
                                                        <small>{{ __('messages.notices.form.image_optimize_hint') }}</small>
                                                    </span>
                                                @else
                                                    <span class="notification-image-empty">
                                                        <i class="fa fa-cloud-upload-alt"></i>
                                                        <strong>{{ $slot['label'] }}</strong>
                                                        <small>{{ __('messages.notices.form.image_click_select') }}</small>
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="notification-image-selected hidden" data-notification-image-selected="{{ $slot['model'] }}">
                                                <i class="fa fa-check-circle"></i>
                                                <strong>{{ __('messages.notices.form.image_selected') }}</strong>
                                                <small>{{ __('messages.notices.form.image_optimize_hint') }}</small>
                                            </span>
                                            @if($selectedImage && !$previewUrl)
                                                <span class="notification-image-selected">
                                                    <i class="fa fa-check-circle"></i>
                                                    <strong>{{ __('messages.notices.form.image_selected') }}</strong>
                                                    <small>{{ __('messages.notices.form.image_optimize_hint') }}</small>
                                                </span>
                                            @endif
                                        </label>
                                        <div class="notification-image-footer">
                                            <div>
                                                <strong>{{ $slot['label'] }}</strong>
                                                <small class="block text-gray-500">
                                                    @if($selectedImage && $previewUrl) {{ __('messages.notices.form.image_preview_ready') }}
                                                    @elseif($selectedImage) {{ __('messages.notices.form.image_new_ready') }}
                                                    @elseif($currentUrl) {{ __('messages.notices.form.image_current') }}
                                                    @else {{ __('messages.notices.form.image_none') }}
                                                    @endif
                                                </small>
                                            </div>
                                            <label for="{{ $inputId }}" class="tw-btn-xs tw-btn-outline mb-0 cursor-pointer">
                                                <i class="fa fa-camera text-xs"></i>
                                                {{ $imageUrl ? __('messages.notices.form.image_change') : __('messages.notices.form.image_select') }}
                                            </label>
                                        </div>
                                        <div wire:loading wire:target="{{ $slot['model'] }}" class="notification-image-loading">
                                            <span class="tw-spinner-sm"></span>
                                            <span>{{ __('messages.notices.form.image_loading') }}</span>
                                        </div>
                                    </div>
                                    @if($slot['number'] === 1) @error('imageAU') <small class="mt-1 block text-red-600">{{ $message }}</small> @enderror
                                    @elseif($slot['number'] === 2) @error('image1AU') <small class="mt-1 block text-red-600">{{ $message }}</small> @enderror
                                    @else @error('image2AU') <small class="mt-1 block text-red-600">{{ $message }}</small> @enderror
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="notification-form-actions">
                    <button type="button" class="tw-btn tw-btn-outline" wire:click="noVisibleAdd">
                        <i class="fa fa-times text-xs"></i> {{ __('messages.notices.buttons.cancel') }}
                    </button>
                    <button type="submit" class="btn-save-notification">
                        {{ $formUpdate ? __('messages.notices.buttons.update') : __('messages.notices.buttons.save') }}
                    </button>
                </div>
            </form>
        </div>
    @else
        {{-- Tabla de avisos --}}
        <div wire:key="notifications-list-card">
            <div class="tw-panel-header border-b-0 pb-2">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">{{ __('messages.notices.table.heading') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.notices.table.subheading') }}</p>
                </div>
                <span class="notification-table-pill">{{ $data->total() }} {{ __('messages.notices.table.count') }}</span>
            </div>

            <div class="tw-table-wrap">
                <table class="tw-table notification-table">
                    <thead class="themed-thead">
                        <tr>
                            <th>{{ __('messages.notices.table.number') }}</th>
                            <th>{{ __('messages.notices.table.title') }}</th>
                            <th>{{ __('messages.notices.table.description') }}</th>
                            <th>{{ __('messages.notices.table.schedule') }}</th>
                            <th>{{ __('messages.notices.table.target') }}</th>
                            <th>{{ __('messages.notices.table.status') }}</th>
                            <th>{{ __('messages.notices.table.registered_by') }}</th>
                            <th class="text-center">{{ __('messages.notices.table.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            @php
                                $metadata = is_array($item->metadata) ? $item->metadata : [];
                                $targetCount = count($metadata['target_user_ids'] ?? []);
                                $galleryImages = collect(['image', 'image_1', 'image_2'])
                                    ->filter(fn ($slot) => $item->{$slot})
                                    ->map(fn ($slot) => route('notifications.images.show', [$item, $slot]))
                                    ->values();
                            @endphp
                            <tr>
                                <td class="text-gray-500">{{ $loop->iteration + (($data->currentPage() - 1) * $data->perPage()) }}</td>
                                <td>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->title }}</div>
                                    @if($item->link)
                                        <a href="{{ $item->link }}" target="_blank" class="mt-1 block text-xs text-blue-600 hover:underline dark:text-blue-400">
                                            {{ \Illuminate\Support\Str::limit($item->link, 42) }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($item->description, 110) }}</td>
                                <td>
                                    <small class="block text-gray-500">{{ __('messages.notices.table.visible') }}: {{ $item->scheduled_at ? $item->scheduled_at->format('Y-m-d H:i') : __('messages.notices.table.immediate') }}</small>
                                    <small class="block text-gray-500">{{ __('messages.notices.table.expires') }}: {{ $item->deadline ? $item->deadline->format('Y-m-d') : '-' }}</small>
                                    <small class="block text-gray-500">{{ __('messages.notices.table.push') }}: {{ $item->push_sent_at ? $item->push_sent_at->format('Y-m-d H:i') : __('messages.notices.table.pending') }}</small>
                                </td>
                                <td>
                                    @php
                                        $deliveryLabel = match ($item->delivery_platform) {
                                            'mobile' => __('messages.notices.form.delivery_mobile'),
                                            'web' => __('messages.notices.form.delivery_web'),
                                            default => __('messages.notices.form.delivery_all'),
                                        };
                                    @endphp
                                    <span class="tw-badge tw-badge-gray mb-1">{{ $deliveryLabel }}</span><br>
                                    @if($targetCount > 0)
                                        <span class="tw-badge tw-badge-blue">{{ $targetCount }} {{ __('messages.notices.table.users') }}</span>
                                    @else
                                        <span class="tw-badge tw-badge-green">{{ __('messages.notices.table.all') }}</span>
                                    @endif
                                    @if($item->push_last_error_message === 'No existen tokens activos para el destino configurado.')
                                        <small class="mt-1 block text-amber-600"><i class="fa fa-exclamation-triangle mr-1"></i>{{ __('messages.notices.table.no_devices') }}</small>
                                    @elseif(\Illuminate\Support\Str::contains($item->push_last_error_message, ['firebase-adminsdk', '/storage/app/firebase', '\\storage\\app\\firebase']))
                                        <small class="mt-1 block text-red-600"><i class="fa fa-times-circle mr-1"></i>{{ __('messages.notices.table.firebase_credentials_error') }}</small>
                                    @elseif($item->push_last_error_message)
                                        <small class="mt-1 block text-red-600"><i class="fa fa-times-circle mr-1"></i>{{ \Illuminate\Support\Str::limit($item->push_last_error_message, 120) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($item->state == 1)
                                        <span class="tw-badge tw-badge-green">{{ __('messages.notices.status.active') }}</span>
                                    @else
                                        <span class="tw-badge tw-badge-gray">{{ __('messages.notices.status.inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->user->name ?? '' }}</td>
                                <td>
                                    <div class="notification-table-actions">
                                        @if($galleryImages->isNotEmpty())
                                            <button type="button"
                                                    class="tw-btn-xs tw-btn-outline-blue notification-gallery-trigger"
                                                    data-gallery-title="{{ $item->title }}"
                                                    data-gallery-images='@json($galleryImages)'
                                                    wire:loading.attr="disabled" wire:target="resendPush">
                                                <i class="fa fa-images mr-1"></i> {{ __('messages.notices.buttons.images') }}
                                            </button>
                                        @endif
                                        <button type="button"
                                                class="tw-btn-xs tw-btn-outline-blue"
                                                wire:click="resendPush({{ $item->id }})"
                                                wire:loading.attr="disabled" wire:target="resendPush"
                                                wire:confirm="{{ __('messages.notices.confirm.resend') }}">
                                            <span wire:loading.remove wire:target="resendPush({{ $item->id }})">
                                                <i class="fa fa-paper-plane mr-1"></i> {{ __('messages.notices.buttons.resend') }}
                                            </span>
                                            <span wire:loading.inline wire:target="resendPush({{ $item->id }})">
                                                <span class="tw-spinner-sm mr-1"></span> {{ __('messages.notices.buttons.sending') }}
                                            </span>
                                        </button>
                                        <button type="button"
                                                class="tw-btn-xs {{ $item->state == 1 ? 'tw-btn-outline' : 'tw-btn-outline-emerald' }}"
                                                wire:click="changeStatus({{ $item->id }}, {{ $item->state }})"
                                                wire:loading.attr="disabled" wire:target="resendPush"
                                                wire:confirm="{{ $item->state == 1 ? __('messages.notices.confirm.deactivate') : __('messages.notices.confirm.activate') }}">
                                            {{ $item->state == 1 ? __('messages.notices.buttons.deactivate') : __('messages.notices.buttons.activate') }}
                                        </button>
                                        <button type="button"
                                                class="tw-btn-xs tw-btn-outline-red"
                                                wire:click="deleteNotif({{ $item->id }})"
                                                wire:loading.attr="disabled" wire:target="resendPush"
                                                wire:confirm="{{ __('messages.notices.confirm.delete') }}">
                                            <i class="fa fa-trash-alt mr-1"></i> {{ __('messages.notices.buttons.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center">
                                    <strong class="mb-1 block text-gray-700 dark:text-gray-300">{{ __('messages.notices.table.empty') }}</strong>
                                    <span class="text-sm text-gray-500">{{ __('messages.notices.table.empty_hint') }}</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $data->links() }}</div>
        </div>

        {{-- Modal de galería (Tailwind/Alpine) --}}
        <div x-data="{ open: false }"
             x-on:open-notification-gallery.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-show="open" x-cloak
             class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
            <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
                <div class="notification-gallery-modal relative w-full max-w-3xl rounded-lg bg-white shadow-xl dark:bg-gray-800">
                    <div class="flex items-start justify-between p-4 pb-0">
                        <div>
                            <h5 class="m-0 text-base font-bold text-gray-800 dark:text-gray-100" id="notificationGalleryModalLabel">{{ __('messages.notices.gallery.title') }}</h5>
                            <p class="mb-0 text-sm text-gray-500" id="notificationGalleryModalDescription">{{ __('messages.notices.gallery.subtitle') }}</p>
                        </div>
                        <button type="button" class="text-gray-500 hover:text-gray-700" x-on:click="open = false" aria-label="{{ __('messages.notices.gallery.close') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="notification-gallery-feature" id="notificationGalleryFeatureWrap">
                            <img id="notificationGalleryFeature" src="" alt="{{ __('messages.notices.gallery.feature_alt') }}">
                        </div>
                        <div class="notification-gallery-stack" id="notificationGalleryStack"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .notification-resend-overlay {
            position: fixed; inset: 0; z-index: 2050;
            align-items: center; justify-content: center; padding: 1rem;
            background: rgba(15,23,42,0.48); backdrop-filter: blur(2px);
        }
        .notification-resend-status {
            display: flex; align-items: center; gap: 1rem;
            width: min(100%, 430px); padding: 1.2rem 1.35rem;
            border: 1px solid rgba(16,185,129,0.28); border-radius: 1rem;
            background: #fff; color: #0f172a;
            box-shadow: 0 24px 65px rgba(15,23,42,0.28);
        }
        .notification-resend-status strong, .notification-resend-status small { display: block; }
        .notification-resend-status small { margin-top: .18rem; color: #64748b; }

        .notification-table-card { border-radius: 1rem; }
        .notification-table-pill {
            background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;
            border-radius: 999px; font-size: .82rem; font-weight: 700; padding: .45rem .85rem;
        }
        .notification-table { border-collapse: separate; border-spacing: 0; }
        .notification-table tbody td { padding: .95rem .75rem; border-top: 1px solid #eef2f7; vertical-align: middle; }
        .notification-table tbody tr:hover { background-color: var(--theme-bg-hover, #ecfdf5) !important; }
        .notification-table-actions { display: inline-flex; flex-wrap: wrap; gap: .45rem; justify-content: center; }

        .notification-gallery-modal { border: 0; border-radius: 1rem; overflow: hidden; }
        .notification-gallery-feature {
            background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 1rem;
            display: flex; justify-content: center; align-items: center;
            min-height: 260px; margin-bottom: 1rem; overflow: hidden; padding: .75rem;
        }
        .notification-gallery-feature img { max-width: 100%; max-height: 420px; object-fit: contain; border-radius: .8rem; }
        .notification-gallery-stack { display: flex; flex-direction: column; gap: .9rem; }
        .notification-gallery-item {
            width: 100%; border: 1px solid #e5e7eb; border-radius: 1rem;
            background: #fff; padding: .75rem; cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .notification-gallery-item:hover, .notification-gallery-item.is-active {
            border-color: #86efac; box-shadow: 0 12px 30px rgba(22,101,52,.12); transform: translateY(-1px);
        }
        .notification-gallery-item img { width: 100%; max-height: 240px; object-fit: contain; border-radius: .8rem; background: #f8fafc; }

        .notification-form-shell {
            position: relative; overflow: hidden; border: 0; border-radius: 1.25rem;
            background: radial-gradient(circle at top right, rgba(16,185,129,.28), transparent 30%),
                        linear-gradient(145deg, #071b14 0%, #0f2f25 42%, #f8fafc 42.2%, #fff 100%);
            box-shadow: 0 24px 60px rgba(15,23,42,.18); margin-bottom: 1.5rem;
        }
        .notification-form-shell::before {
            content: ""; position: absolute; top: -80px; right: -70px;
            width: 220px; height: 220px; border-radius: 999px;
            border: 36px solid rgba(255,255,255,.07); pointer-events: none;
        }
        .notification-form-hero {
            position: relative; z-index: 1;
            display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
            padding: 1.65rem 1.75rem 1.25rem; color: #fff;
        }
        .notification-form-kicker {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .25rem .65rem; margin-bottom: .65rem;
            border: 1px solid rgba(255,255,255,.22); border-radius: 999px;
            background: rgba(255,255,255,.09); color: #bbf7d0;
            font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        }
        .notification-form-hero h4 { margin: 0; font-size: 1.55rem; font-weight: 800; letter-spacing: -.03em; }
        .notification-form-hero p { max-width: 680px; margin: .45rem 0 0; color: rgba(255,255,255,.74); }
        .notification-form-status {
            display: inline-flex; align-items: center; gap: .45rem; flex: 0 0 auto;
            padding: .55rem .8rem; border: 1px solid rgba(255,255,255,.18); border-radius: .8rem;
            background: rgba(2,6,23,.22); color: #fff; font-weight: 700;
        }
        .status-dot { width: .62rem; height: .62rem; border-radius: 999px; background: #94a3b8; }
        .status-dot.active  { background: #22c55e; box-shadow: 0 0 0 4px rgba(34,197,94,.22), 0 0 18px rgba(34,197,94,.7); }
        .status-dot.inactive { background: #f97316; box-shadow: 0 0 0 4px rgba(249,115,22,.22); }

        .notification-form-body {
            position: relative; z-index: 2; margin: 0 1rem 1rem;
            padding: 1rem; border-radius: 1rem;
            background: rgba(255,255,255,.96); box-shadow: 0 16px 36px rgba(15,23,42,.12);
        }
        .notification-form-section {
            padding: 1rem 1rem .55rem; margin-bottom: 1rem;
            border: 1px solid rgba(15,118,110,.12); border-radius: .95rem;
            background: linear-gradient(180deg, rgba(240,253,244,.82), rgba(255,255,255,.96)), #fff;
        }
        .notification-form-section.compact { background: linear-gradient(180deg, rgba(236,253,245,.58), rgba(255,255,255,.98)), #fff; }
        .notification-form-section.media  { padding-bottom: .15rem; background: radial-gradient(circle at top left, rgba(20,184,166,.12), transparent 32%), #fff; }
        .notification-section-title { display: flex; align-items: center; gap: .7rem; margin-bottom: 1rem; }
        .notification-section-title i {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: .75rem; background: #047857; color: #fff;
        }
        .notification-section-title strong { display: block; color: #0f172a; font-size: .95rem; }
        .notification-section-title small  { display: block; margin-top: .15rem; color: #64748b; }
        .notification-form-shell label { color: #0f172a; font-size: .78rem; font-weight: 800; letter-spacing: .02em; }
        .notification-recipient-summary {
            min-height: 46px; display: flex; align-items: center;
            padding: .75rem .9rem; border: 1px dashed #9dd7bd; border-radius: .8rem;
            background: #f8fffb; color: #0f5132; font-size: .92rem;
        }
        .notification-user-search-box { position: relative; border: 1px solid #dbe7e1; border-radius: .9rem; background: #fff; overflow: hidden; }
        .notification-user-search-results { max-height: 280px; overflow-y: auto; }
        .notification-user-search-status { padding: .65rem .95rem; border-bottom: 1px solid #d1fae5; background: #ecfdf5; color: #047857; font-size: .82rem; font-weight: 700; }
        .notification-user-result { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .85rem .95rem; border-top: 1px solid #edf2f7; }
        .notification-user-result:first-child { border-top: 0; }
        .notification-user-result:hover { background: #f6fff9; }
        .notification-user-result-body { min-width: 0; }
        .notification-user-result-body strong, .notification-user-result-body small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .notification-user-result-body small { margin-top: .2rem; color: #64748b; }
        .notification-user-search-empty { padding: .95rem 1rem; color: #64748b; background: #fbfefc; border-radius: .9rem; }
        .notification-user-search-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.76); color: #047857; font-weight: 700; z-index: 2; }
        .notification-selected-users { display: flex; flex-wrap: wrap; gap: .75rem; min-height: 54px; padding: .85rem; border: 1px dashed #cbd5e1; border-radius: .9rem; background: #fcfffd; }
        .notification-user-chip { display: inline-flex; align-items: center; gap: .65rem; max-width: 100%; padding: .45rem .55rem .45rem .85rem; border-radius: 999px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-weight: 700; }
        .notification-user-chip small { display: block; color: #0f766e; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .notification-user-chip-remove { width: 1.7rem; height: 1.7rem; border: 0; border-radius: 999px; background: #d1fae5; color: #065f46; font-size: 1.1rem; cursor: pointer; }
        .notification-user-chip-remove:hover { background: #a7f3d0; }

        .notification-form-actions { display: flex; align-items: center; justify-content: flex-end; gap: .75rem; padding: .2rem .2rem .1rem; }
        .btn-save-notification {
            border: 0; border-radius: 999px; padding: .72rem 1.35rem;
            background: linear-gradient(135deg, #052e26 0%, #047857 45%, #10b981 100%);
            color: #fff !important; font-weight: 800; cursor: pointer; transition: filter .16s ease;
        }
        .btn-save-notification:hover { filter: brightness(1.05); }

        .notification-image-card { min-height: 220px; background: #fff; border-color: rgba(15,118,110,.16); border-radius: 1rem; transition: border-color .2s ease; }
        .notification-image-card:hover { border-color: var(--theme-gradient-start, #047857); }
        .notification-image-dropzone { position: relative; display: block; height: 168px; cursor: pointer; background: #f8fafc; }
        .notification-image-preview { width: 100%; height: 168px; display: block; object-fit: cover; }
        .notification-image-empty  { height: 168px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .35rem; color: #64748b; text-align: center; padding: 1rem; }
        .notification-image-selected { height: 168px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .35rem; color: #0f766e; text-align: center; padding: 1rem; background: #ecfdf5; }
        .notification-image-empty .fa, .notification-image-selected .fa { font-size: 2.1rem; color: var(--theme-gradient-start, #047857); }
        .notification-image-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; background: rgba(15,23,42,.48); opacity: 0; transition: opacity .2s ease; }
        .notification-image-card:hover .notification-image-overlay { opacity: 1; }
        .notification-image-overlay.show { opacity: 1; }
        .notification-image-footer { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .75rem; background: #fff; border-top: 1px solid rgba(15,23,42,.08); }
        .notification-image-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: .5rem; background: rgba(255,255,255,.82); color: #0f766e; font-weight: 600; z-index: 5; }

        @media (max-width: 767.98px) {
            .notification-form-shell { border-radius: 1rem; }
            .notification-form-hero { flex-direction: column; padding: 1.25rem; }
            .notification-form-body { margin: 0 .65rem .65rem; padding: .75rem; }
            .notification-form-actions { flex-direction: column-reverse; align-items: stretch; }
            .notification-form-actions .tw-btn { width: 100%; justify-content: center; }
        }
    </style>
</div>

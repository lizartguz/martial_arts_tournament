<div>
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-green" role="alert">
            <i class="fas fa-check-circle"></i>
            <span class="flex-1"><strong>{{ session('success') }}</strong></span>
            <button @click="show=false" class="ml-auto hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('failed'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-amber" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="flex-1"><strong>{{ session('failed') }}</strong></span>
            <button @click="show=false" class="ml-auto hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-red" role="alert">
            <i class="fas fa-times-circle"></i>
            <span class="flex-1"><strong>{{ session('error') }}</strong></span>
            <button @click="show=false" class="ml-auto hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
    @endif


    <div class="col-span-12 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            @if($formAdd)
                <div class="grid grid-cols-12 gap-4 m-0 p-0">
                    <div class="col-span-12 md:col-span-3">
                        <div></div>
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <div class="text-center font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.titles.form_add') }}</div>
                    </div>
                    <div class="col-span-12 md:col-span-3">

                        <button class="tw-btn tw-btn-gray mt-0 ml-auto" wire:click='closeFormButton'>{{ __('messages.subscriptions.view.buttons.close_form') }}</button>
                    </div>
                </div>
            @elseif($formUpdate)
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-3">
                        <div>
                            <button class="tw-btn tw-btn-gray" wire:click='refreshUpdateData'>
                                <i class="fas fa-sync-alt"></i>
                              </button>
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <div class="text-center font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.titles.form_update') }}</div>
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <div class="flex items-center justify-end gap-2">
                            <div wire:loading class="pr-2">
                                <div class="tw-spinner" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </div>

                            <button class="tw-btn tw-btn-gray" wire:click='closeFormButton'>{{ __('messages.subscriptions.view.buttons.close_form') }}</button>
                        </div>
                    </div>
                </div>
            @elseif($formRenewal)
                    <div class="grid grid-cols-12 gap-4 m-0 p-0">
                        <div class="col-span-12 md:col-span-3">
                            <div></div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="text-center font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.titles.form_renewal') }}</div>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <button class="tw-btn tw-btn-gray mt-0 ml-auto" wire:click='closeFormButton'>{{ __('messages.subscriptions.view.buttons.close_form') }}</button>
                        </div>
                    </div>
            @elseif($formExtension)
                    <div class="grid grid-cols-12 gap-4 m-0 p-0">
                        <div class="col-span-12 md:col-span-3">
                            <div></div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="text-center font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.titles.form_extension') }}</div>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <button class="tw-btn tw-btn-gray mt-0 ml-auto" wire:click='closeFormButton'>{{ __('messages.subscriptions.view.buttons.close_form') }}</button>
                        </div>
                    </div>
            @else
                @if($formAddNewUser)
                    <div class="grid grid-cols-12 gap-4 m-0 p-0">
                        <div class="col-span-12 md:col-span-3">
                            <div></div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="text-center font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.titles.form_add_user') }}</div>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <button class="tw-btn tw-btn-gray mt-0 ml-auto" wire:click='closeFormButton'>{{ __('messages.subscriptions.view.buttons.close_form') }}</button>
                        </div>
                    </div>
                @endif
            @endif
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    @if($viewList)
                        <div class="grid min-w-[980px] grid-cols-5 items-end gap-3 overflow-x-auto" style="display: grid !important; grid-template-columns: repeat(5, minmax(0, 1fr)) !important; align-items: end; gap: 0.75rem; overflow-x: auto;">
                            <div class="min-w-0" style="min-width: 0;">
                                <div class="space-y-1">
                                    <label>{{ __('messages.subscriptions.view.filters.per_page_label') }}</label>
                                    <select wire:model.live="perPage" class="tw-input-sm w-full min-w-0">
                                        <option value="10">{{ __('messages.subscriptions.view.filters.per_page_option', ['count' => 10]) }}</option>
                                        <option value="25">{{ __('messages.subscriptions.view.filters.per_page_option', ['count' => 25]) }}</option>
                                        <option value="50">{{ __('messages.subscriptions.view.filters.per_page_option', ['count' => 50]) }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="min-w-0" style="min-width: 0;">
                                <div class="space-y-1">
                                    <label>{{ __('messages.subscriptions.view.filters.search_label') }}</label>
                                    <input type="text" placeholder="{{ __('messages.subscriptions.view.filters.search_placeholder') }}" wire:model.live="search" class="tw-input-sm w-full min-w-0">
                                </div>
                            </div>

                            <div class="min-w-0" style="min-width: 0;">
                                <div class="space-y-1">
                                    <label class="block truncate">{{ __('messages.subscriptions.view.filters.expiration_range') }}</label>
                                    <input type="date" wire:model.live="beginDateExpiration" class="tw-input-sm w-full min-w-0">
                                </div>
                            </div>

                            <div class="min-w-0" style="min-width: 0;">
                                <div class="space-y-1">
                                    <label class="block">&nbsp;</label>
                                    <input type="date" wire:model.live="endDateExpiration" class="tw-input-sm w-full min-w-0">
                                </div>
                            </div>

                            <div class="min-w-0" style="min-width: 0;">
                                <div class="flex items-center justify-end gap-3">
                                    <div wire:loading>
                                        <div class="tw-spinner" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </div>
                                    <a class="tw-btn tw-btn-emerald w-full justify-center whitespace-nowrap" target="_blank" href="{{ route('createpayaccount') }}">
                                        <i class="fas fa-plus mr-2"></i>{{ __('messages.subscriptions.view.buttons.new_subscriber') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="p-0">
            @if($viewList)
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <table class="tw-table" @if(count($data) == 1) style="height:30vh" @endif>
                            <thead class="themed-thead">
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.client') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.total') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.payment_type') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.discount') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.start') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.expiration') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.extension') }}</th>
                                <th style="cursor: pointer" >{{ __('messages.subscriptions.view.table.headers.debts') }}</th>
                                <th colspan="" class="text-center">{{ __('messages.subscriptions.view.table.headers.actions') }}</th>
                            </thead>
                            <tbody>
                                @if (count($data))
                                @php  $currentDate = date('Y-m-d'); @endphp
                                    @foreach ($data as $item)
                                        <tr class="subscription-row">
                                            <td>
                                                <div class="client-info">
                                                    <div class="client-name">
                                                        <i class="fas fa-user mr-2 text-gray-500 dark:text-gray-400"></i>
                                                        <strong>{{ $item->user->name }} {{ $item->user->lastname }}</strong>
                                                    </div>
                                                    <div class="mt-2">
                                                        <span class="inline-flex items-center rounded-full bg-gray-800 px-2 py-0.5 text-xs font-medium text-white">
                                                            <i class="fas fa-box mr-1"></i>
                                                            {{ $item->subscription->subscription->name }}
                                                        </span>
                                                        <span class="tw-badge tw-badge-gray ml-1">
                                                            <i class="fas fa-user-tag mr-1"></i>
                                                            {{ $item->user->user_type !== null ? __('messages.subscriptions.view.table.user_types.' . ($item->user->user_type == 1 ? 'owner' : ($item->user->user_type == 2 ? 'manager' : 'delegate'))) : '' }}
                                                        </span>
                                                    </div>
                                                    @if($item->subscription->station!==null)
                                                        <div class="mt-2">
                                                            <span class="tw-badge tw-badge-blue">
                                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                                {{ $item->subscription->station->name }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="contact-info mt-2">
                                                        <small class="block text-gray-500 dark:text-gray-400">
                                                            <i class="fas fa-envelope mr-1"></i>
                                                            {{ $item->user->email ?? __('messages.subscriptions.view.table.messages.no_email') }}
                                                        </small>
                                                        <small class="block text-gray-500 dark:text-gray-400">
                                                            <i class="fas fa-phone mr-1"></i>
                                                            {{ $item->user->number_phone ?? __('messages.subscriptions.view.table.messages.no_phone') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="amount-display">
                                                    <span class="blurred" onclick="reveal(this)">
                                                        <strong class="text-emerald-600 dark:text-emerald-400">${{ number_format( $item->subscription->cost, 2) }}</strong>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if( $item->subscription->payment_type_renewal===0)
                                                    <span class="tw-badge tw-badge-gray">{{ __('messages.subscriptions.view.table.payments.none') }}</span>
                                                @elseif($item->subscription->payment_type_renewal==1)
                                                    <span class="tw-badge tw-badge-green"><i class="fas fa-money-bill-wave mr-1"></i>{{ __('messages.subscriptions.view.table.payments.cash') }}</span>
                                                @elseif($item->subscription->payment_type_renewal==2)
                                                    <span class="tw-badge tw-badge-blue"><i class="fas fa-credit-card mr-1"></i>{{ __('messages.subscriptions.view.table.payments.card') }}</span>
                                                @elseif($item->subscription->payment_type_renewal==3)
                                                    <span class="tw-badge tw-badge-blue"><i class="fas fa-qrcode mr-1"></i>{{ __('messages.subscriptions.view.table.payments.qr') }}</span>
                                                @elseif($item->subscription->payment_type_renewal==4)
                                                    <span class="tw-badge tw-badge-gray"><i class="fas fa-gift mr-1"></i>{{ __('messages.subscriptions.view.table.payments.courtesy') }}</span>
                                                @elseif($item->subscription->payment_type_renewal==5)
                                                    <span class="tw-badge tw-badge-amber">{{ __('messages.subscriptions.view.table.payments.other') }}</span>
                                                @else
                                                    @if($item->subscription->payment_type_renewal===null)
                                                        <span class="tw-badge tw-badge-gray">{{ __('messages.subscriptions.view.table.badges.transparent') }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($item->subscription->is_discount)
                                                    <span class="tw-badge tw-badge-green">
                                                        <i class="fas fa-check-circle mr-1"></i>{{ __('messages.subscriptions.view.table.badges.discount_yes') }}
                                                    </span>
                                                @else
                                                    <span class="tw-badge tw-badge-gray">{{ __('messages.subscriptions.view.table.badges.discount_no') }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                               <div class="date-badge date-start">
                                                   <i class="fas fa-calendar-plus mr-2"></i>
                                                   <strong>{{ $item->subscription->start_date }}</strong>
                                               </div>
                                            </td>
                                            <td class="align-middle">
                                                @if($currentDate > $item->subscription->date_expiry)
                                                    <div class="date-badge date-expired">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        <div>
                                                            <strong>{{ $item->subscription->date_expiry }}</strong>
                                                            <span class="tw-badge tw-badge-red ml-2">{{ __('messages.subscriptions.view.table.status.expired') }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="date-badge date-active">
                                                        <i class="fas fa-calendar-check mr-2"></i>
                                                        <strong>{{ $item->subscription->date_expiry }}</strong>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($item->subscription->temporary_extension_start)
                                                    <small class="block text-gray-500 dark:text-gray-400">
                                                        <i class="fas fa-play-circle mr-1"></i>
                                                        {{ $item->subscription->temporary_extension_start }}
                                                    </small>
                                                    <small class="block text-gray-500 dark:text-gray-400">
                                                        <i class="fas fa-stop-circle mr-1"></i>
                                                        {{ $item->subscription->temporary_extension_end }}
                                                    </small>
                                                @else
                                                    <span class="text-gray-500 dark:text-gray-400">---</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <div class="debts-container">
                                                    <div class="debt-item mb-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.subscriptions.view.table.debts.new_users') }}</span>
                                                        @if($item->subscription->is_it_paid_user===0)
                                                            <span class="tw-badge tw-badge-amber ml-2">
                                                                <i class="fas fa-clock mr-1"></i>{{ __('messages.subscriptions.view.table.badges.pending') }}
                                                            </span>
                                                        @else
                                                            <span class="tw-badge tw-badge-gray ml-2">
                                                                <i class="fas fa-check mr-1"></i>{{ __('messages.subscriptions.view.table.badges.none') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="debt-item">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.subscriptions.view.table.debts.renewal') }}</span>
                                                        @if($item->subscription->is_it_paid_renewal===0)
                                                            <span class="tw-badge tw-badge-amber ml-2">
                                                                <i class="fas fa-clock mr-1"></i>{{ __('messages.subscriptions.view.table.badges.pending') }}
                                                            </span>
                                                        @else
                                                            <span class="tw-badge tw-badge-gray ml-2">
                                                                <i class="fas fa-check mr-1"></i>{{ __('messages.subscriptions.view.table.debts.none') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td width="" class="align-middle">
                                                <div class="action-buttons text-center">
                                                    <div class="group relative mb-1 inline-block text-left">
                                                        <button class="tw-btn tw-btn-outline-blue" type="button">
                                                            <i class="fas fa-cog mr-1"></i>{{ __('messages.subscriptions.view.buttons.manage') }}
                                                        </button>
                                                        <div class="invisible absolute right-0 z-20 mt-2 min-w-[14rem] rounded-lg border border-gray-200 bg-white p-1 text-sm shadow-lg group-focus-within:visible dark:border-gray-700 dark:bg-gray-800">
                                                            <h6 class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                                <i class="fas fa-edit mr-1"></i>{{ __('messages.subscriptions.view.dropdowns.options') }}
                                                            </h6>
                                                            <a class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700" wire:click='visibleUpdate({{$item->subscription->id}},{{$item->user->id}})' href="#">
                                                                <i class="fas fa-edit mr-2 text-amber-600 dark:text-amber-400"></i>{{ __('messages.subscriptions.view.dropdowns.modify') }}
                                                            </a>
                                                            @if($item->user->user_type==1)
                                                                <a class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700" target="_blank"  href="{{ route('payAndRenewC', ['userId' => $item->user->id, 'forPay' => true]) }}">
                                                                    <i class="fas fa-sync mr-2 text-emerald-600 dark:text-emerald-400"></i>{{ __('messages.subscriptions.view.dropdowns.renew') }}
                                                                </a>
                                                                <a class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700" wire:click='viewExtension({{$item->subscription->id}},{{$item->user->id}})' href="#">
                                                                    <i class="fas fa-clock mr-2 text-sky-600 dark:text-sky-400"></i>{{ __('messages.subscriptions.view.dropdowns.extension') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="group relative mb-1 inline-block text-left">
                                                        <button class="tw-btn tw-btn-outline" type="button">
                                                            <i class="fas fa-file-pdf mr-1"></i>{{ __('messages.subscriptions.view.buttons.reports') }}
                                                        </button>
                                                        <div class="invisible absolute right-0 z-20 mt-2 min-w-[14rem] rounded-lg border border-gray-200 bg-white p-1 text-sm shadow-lg group-focus-within:visible dark:border-gray-700 dark:bg-gray-800">
                                                            <h6 class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                                <i class="fas fa-file-pdf mr-1"></i>{{ __('messages.subscriptions.view.dropdowns.documents') }}
                                                            </h6>
                                                            <a class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700" href="{{ route('reportItem', ['id' => $item->subscription->id, 'type' => 'watch']) }}">
                                                                <i class="fas fa-eye mr-2 text-blue-600 dark:text-blue-400"></i>{{ __('messages.subscriptions.view.dropdowns.view_pdf') }}
                                                            </a>
                                                            <a class="flex items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700" href="{{ route('reportItem', ['id' => $item->subscription->id, 'type' => 'download']) }}">
                                                                <i class="fas fa-download mr-2 text-emerald-600 dark:text-emerald-400"></i>{{ __('messages.subscriptions.view.dropdowns.download_pdf') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <button class="tw-btn-xs tw-btn-outline-red" onclick="eliminarI({{$item->subscription->id}})">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ count($data) }}"><strong>{{ __('messages.subscriptions.view.table.messages.no_records') }}</strong></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="border-t border-gray-200 px-4 py-2 dark:border-gray-700">

                        </div>
                    </div>
                </div>
            @endif

            @if($formAdd)
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-5">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12">
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-8">
                                                <div class="space-y-1">
                                                <label class="p-0 m-0" for="business_name">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>

                                                    <input id="business_name" type="text" wire:model="business_name" class="tw-input-sm"  placeholder="" maxlength="40">

                                                    @error('business_name')
                                                    <span class="error " maxlength="50" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ&\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha_numeric') }}" style="font-size: 9pt; color:blue;" >
                                                        {{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-4">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="nit">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>

                                                    <input id="nit" type="text" wire:model="nit" class="tw-input-sm"  placeholder="" maxlength="40">


                                                    @error('nit')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-5">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0"  for="name">{{ __('messages.subscriptions.view.form.labels.name') }}:*</label>
                                                    @if($subscriber_user_id==null)
                                                    <input id="name" type="text" wire:model="name" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40">
                                                    @else
                                                    <input id="name" type="text" wire:model="name" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40"  disabled >
                                                    @endif
                                                    @error('name')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-5">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0"  for="lastname">{{ __('messages.subscriptions.view.form.labels.lastname') }}:*</label>
                                                    @if($subscriber_user_id==null)
                                                    <input id="lastname" type="text" wire:model="lastname" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40">
                                                    @else
                                                    <input id="lastname" type="text" wire:model="lastname" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40"  disabled >
                                                    @endif
                                                    @error('lastname')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-2">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="ci">{{ __('messages.subscriptions.view.form.labels.ci') }}:*</label>
                                                    @if($subscriber_user_id==null)
                                                    <input id="ci" type="text" wire:model="ci" class="tw-input-sm"  placeholder="" maxlength="40" >
                                                    @else
                                                    <input id="ci" type="text" wire:model="ci" class="tw-input-sm"  placeholder="" maxlength="40" disabled>
                                                    @endif
                                                    @error('ci')
                                                    <span class="error " maxlength="15" pattern="[a-zA-Z0-9]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 gap-4 w-100">
                                            <div class="col-span-12 md:col-span-4">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="number_phone">{{ __('messages.subscriptions.view.form.labels.phone') }}:*</label>
                                                    @if($subscriber_user_id==null)
                                                    <input id="number_phone" type="number" wire:model="number_phone" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                    @else
                                                    <input id="number_phone" type="number" wire:model="number_phone" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100"  disabled>
                                                    @endif
                                                    @error('number_phone')
                                                    <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-8">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:*</label>
                                                    <select wire:model="subscriptionTag" id="subscriptionTag" name="subscriptionTag" wire:change="changeTypeSubscription" class="tw-input-sm" @if($subscriber_user_id!=null) disabled @endif>
                                                        @if(count($subscriptions) > 0)
                                                            <option value="-1">{{ __('messages.subscriptions.view.form.placeholders.select') }}</option>
                                                            @foreach($subscriptions as $item)
                                                                <option value="{{$item->tag}}">{{$item->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('subscriptionTag')
                                                    <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>

                                                @if($subscriptionTag=="demo personal" || $subscriptionTag=="demo professional")
                                                <div class="grid grid-cols-12 gap-4 w-100">
                                                    <div class="col-span-12 md:col-span-6">
                                                        <div class="space-y-1  w-100">
                                                            <input id="freeDateStart" type="date" wire:model.live="freeDateStart" class="tw-input-sm" placeholder="" maxlength="100">
                                                            @error('freeDateStart')
                                                            <span class="error" maxlength="100"  style="font-size: 9pt; color:blue;">{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-6">
                                                        <div class="space-y-1  w-100">
                                                            <input id="freeDateEnd" type="date" wire:model.live="freeDateEnd" class="tw-input-sm" placeholder="" maxlength="100">
                                                            @error('freeDateEnd')
                                                            <span class="error " maxlength="100"  style="font-size: 9pt; color:blue;">{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-8">
                                                <label class="p-0 m-0" for="email">{{ __('messages.subscriptions.view.form.labels.email') }}:*</label>
                                                <div class="space-y-1">
                                                    @if($subscriber_user_id==null)
                                                    <input id="email" type="email" wire:model="email" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                    @else
                                                    <input id="email" type="email" wire:model="email" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100" disabled>
                                                    @endif
                                                    @error('email')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if($subscriptionTag!="demo personal" && $subscriptionTag!="demo professional")
                                            <div class="col-span-12 md:col-span-4">
                                                <label class="p-0 m-0" for="password">{{ __('messages.subscriptions.view.form.labels.duration') }}:*</label>
                                                <div class="relative">
                                                    <div class="space-y-1">
                                                    <select wire:model.live="durationitemAdd" class="tw-input-sm">
                                                            @for($i=0; $i < count($durationListAdd); $i++)
                                                                @if($durationListAdd[$i]==1)
                                                                    <option value="{{$durationListAdd[$i]}}">{{$durationListAdd[$i]}} {{ __('messages.subscriptions.view.form.text.year') }}</option>
                                                                @else
                                                                    <option value="{{$durationListAdd[$i]}}">{{$durationListAdd[$i]}} {{ __('messages.subscriptions.view.form.text.years') }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            @endif
                                        </div>
                                        @if($subscriber_user_id==null)
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12">
                                                <label class="p-0 m-0" for="password">{{ __('messages.subscriptions.view.form.labels.password') }}:*</label>
                                                <div class="relative">
                                                    <div class="space-y-1">
                                                        <input id="password" type="password" wire:model="password" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.password') }}"  maxlength="255" >
                                                        <span id="toggle-password" class="password-toggle" onclick="togglePasswordVisibility()">&#128065;</span>
                                                        @error('password')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif


                                        @if($subscriptionTag=='personal' || $subscriptionTag=='professional' || $subscriptionTag=='demo professional' || $subscriptionTag=='demo personal')
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <h6 class=""><span class="font-semibold">{{ __('messages.subscriptions.view.form.text.cost') }}</span> <span class="rounded bg-emerald-600 px-2 py-0.5 text-white">{{$totalA}} USD</span></h6>
                                                </div>

                                            </div>
                                        @endif

                                        @if($subscriptionTag=='personal' || $subscriptionTag=='professional' || $subscriptionTag=='corporate')
                                        <div class="my-3 rounded bg-gray-600 py-1 text-center font-semibold text-white dark:bg-gray-700">{{ __('messages.subscriptions.view.form.headings.apply_discounts') }}</div>
                                        @if($subscriptionTag=='corporate')
                                            <div class="flex items-center gap-2">
                                                <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="checkbox" value="" wire:model.live="discountByStation" id="discountByStation" >
                                                <label class="text-sm text-gray-700 dark:text-gray-300" for="discountByStation">
                                                    {{ __('messages.subscriptions.view.form.labels.discount_station') }}
                                                </label>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2">
                                            <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="checkbox" value="" wire:model.live="discountByDuration" id="discountByDuration">
                                            <label class="text-sm text-gray-700 dark:text-gray-300" for="discountByDuration">
                                                {{ __('messages.subscriptions.view.form.labels.discount_duration') }}
                                            </label>
                                        </div>

                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div wire:ignore.self class="col-span-12 md:col-span-7">
                            @if(in_array(trim(strtolower($subscriptionTag)), ['demo personal', 'professional', 'demo professional', 'personal']))
                                <input id="latitud" wire:model="latitud"  name="latitud"  type="text" value="{{$latitud}}">
                                <input id="longitud" wire:model="longitud"  name="longitud" type="text" value="{{$longitud}}">
                                <div class="rounded bg-gray-600 py-1 text-center font-semibold text-white dark:bg-gray-700">{{ __('messages.subscriptions.view.form.headings.geo_reference') }}</div>
                                <div wire:ignore id="map" style="height: 100vh"></div>
                            @elseif($subscriptionTag=='-1')
                            <div id="map" style="height: 100vh"></div>
                            @else
                                @if($subscriptionTag=='corporate')

                                    <div class="grid grid-cols-12 gap-4">
                                        <p class="w-full rounded-md bg-gray-200 p-2 text-center text-base font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-100" style="width:100%; background-color:#c1c1c1;">{{ __('messages.subscriptions.view.form.headings.user_list') }}</p>
                                    </div>
                                    <div style="height: 90vh;overflow-y: auto; border: 1px solid #ccc; ">
                                        @php $cont=1; @endphp
                                        @foreach($elements as $element)
                                            <div class="grid grid-cols-12 gap-4 px-1 pb-4" >
                                                <div class="col-span-12 md:col-span-2">
                                                    <p>{{ __('messages.subscriptions.view.form.text.user_counter', ['number' => $cont]) }}</p>
                                                </div>
                                                <div class="col-span-12 md:col-span-10">
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-6">
                                                            <input type="text" wire:model="elements.{{ $loop->index }}.name" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="50">
                                                        </div>

                                                        <div class="col-span-12 md:col-span-6">
                                                            <input type="text" wire:model="elements.{{ $loop->index }}.mail" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-6">
                                                            <input type="text" wire:model="elements.{{ $loop->index }}.lastname" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="50">
                                                        </div>
                                                        <div class="col-span-12 md:col-span-6">
                                                            <input type="text" wire:model="elements.{{ $loop->index }}.phone" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="50">
                                                        </div>

                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-6">
                                                            <input type="text" wire:model="elements.{{ $loop->index }}.password" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.password') }}" maxlength="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @php $cont=$cont+1; @endphp
                                        @endforeach
                                    </div>


                                @endif
                            @endif
                        </div>
                    </div>
                    @if($subscriptionTag=='corporate')
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-5">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="md:col-span-7">
                                    <div class="space-y-1">
                                        <label>{{ __('messages.subscriptions.view.form.labels.station_search') }}:</label>
                                        <input type="text" placeholder="{{ __('messages.subscriptions.view.form.placeholders.station_name') }}" wire:model.live="searchStationForAdd" class="tw-input-sm">
                                    </div>
                                </div>
                                <div class="md:col-span-5">


                                    @if($selectStation==false)
                                    <div class="flex items-center gap-2">
                                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="radio" name="flexRadioDefault" id="flexRadioDefault1"  wire:click="setStationSelect" checked>
                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="flexRadioDefault1">
                                            {{ __('messages.subscriptions.view.form.options.without_station') }}
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="radio" name="flexRadioDefault" id="flexRadioDefault2"  onclick="messageWithStation()" >
                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="flexRadioDefault2">
                                            {{ __('messages.subscriptions.view.form.options.with_station') }}
                                        </label>
                                    </div>
                                    @else
                                    <div class="flex items-center gap-2">
                                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="radio" name="flexRadioDefault" id="flexRadioDefault1"  wire:click="setStationSelect" >
                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="flexRadioDefault1">
                                            {{ __('messages.subscriptions.view.form.options.without_station') }}
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="flexRadioDefault2">
                                            {{ __('messages.subscriptions.view.form.options.with_station') }}
                                        </label>
                                    </div>
                                    @endif


                                </div>
                            </div>
                            <table class="tw-table" style="height: 370px;" border="1">
                                <thead class="themed-thead">
                                    <th style="cursor: pointer" >{{ __('messages.subscriptions.view.form.labels.station_prompt') }}:</th>
                                </thead>
                                <tbody>
                                    @if (count($dataStationAdd) > 0)
                                        @foreach ($dataStationAdd as $item3)
                                            <tr class="w-100">
                                                <td class="w-100">
                                                    <button class="w-100" type="button" wire:click="setStationLanLonMarker('{{json_encode(['id'=>$item3->id, 'latitude' => $item3->latitude,'longitude' => $item3->longitude, 'type'=> 'add'])}}')">
                                                        <div class="text-gray-800 dark:text-gray-100" >{{ $item3->name }}</div>
                                                        <small class="text-blue-600 dark:text-blue-400">{{ $item3->location }}</small>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><strong>{{ __('messages.subscriptions.view.table.messages.no_records') }}</strong></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div wire:ignore class="col-span-12 md:col-span-7">

                            <input id="latitud" wire:model="latitud"  name="latitud"  type="text" value="{{$latitud}}">
                            <input id="longitud" wire:model="longitud"  name="longitud" type="text" value="{{$longitud}}">
                            <div class="rounded bg-gray-600 py-1 text-center font-semibold text-white dark:bg-gray-700">{{ __('messages.subscriptions.view.form.headings.geo_reference') }}</div>
                            <div id="map" style="height: 60vh"></div>
                             <!--div id="map" style="height: 60vh"></div!-->
                        </div>

                    </div>
                    @endif
                    @if($subscriptionTag!="-1")
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-8">
                            <button class="tw-btn tw-btn-emerald w-full justify-center" type="submit" >{{ __('messages.subscriptions.view.form.buttons.register_subscription') }}</button>
                        </div>
                        <div class="col-span-12 md:col-span-4">
                            <button class="tw-btn tw-btn-red w-full justify-center" wire:click='closeFormButton' >{{ __('messages.subscriptions.view.form.buttons.close') }}</button>
                        </div>
                    </div>
                    @endif
                </form>
            @endif

            @if($formUpdate)

                @if($subscriptionTagE=='corporate' || $subscriptionTagOwnerE=='corporate')
                    @if($user_typeOwnerE==1)
                        <form wire:submit.prevent="updateUsersSubscriptionOwner">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12">
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-3">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="business_nameOwnerE">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                    <input id="business_nameOwnerE" type="text" wire:model="business_nameOwnerE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.client_name') }}" maxlength="40">
                                                    @error('business_nameOwnerE')
                                                    <span class="error " maxlength="50" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ&\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha_numeric') }}" style="font-size: 9pt; color:blue;" >
                                                        {{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-3">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="nitOwnerE">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                    <input id="nitE" type="text" wire:model="nitOwnerE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.select') }}" maxlength="40">
                                                    @error('nitOwnerE')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-3">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0"  for="nameOwnerE">{{ __('messages.subscriptions.view.form.labels.name') }}:*</label>
                                                    <input id="nameOwnerE" type="text" wire:model="nameOwnerE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40" >
                                                    @error('nameOwnerE')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-3">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="lastnameOwnerE">{{ __('messages.subscriptions.view.form.labels.lastname') }}:*</label>
                                                    <input id="lastnameOwnerE" type="text" wire:model="lastnameOwnerE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40" >
                                                    @error('lastnameOwnerE')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-2">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="ciOwnerE">{{ __('messages.subscriptions.view.form.labels.ci') }}:*</label>
                                                    <input id="ciOwnerE" type="text" wire:model="ciOwnerE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.ci') }}" maxlength="40">
                                                    @error('ciOwnerE')
                                                    <span class="error " maxlength="15" pattern="[a-zA-Z0-9]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-3">
                                                <label class="p-0 m-0" for="emailOwnerE">{{ __('messages.subscriptions.view.form.labels.email') }}:*</label>
                                                <div class="space-y-1">
                                                    <input id="emailOwnerE" type="email" wire:model="emailOwnerE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                    @error('emailOwnerE')
                                                    <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-2">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="number_phoneOwnerE">{{ __('messages.subscriptions.view.form.labels.phone') }}:*</label>
                                                    <input id="number_phoneOwnerE" type="number" wire:model="number_phoneOwnerE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                    @error('number_phoneOwnerE')
                                                    <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-2">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:*</label>
                                                    <select wire:model="subscriptionTagOwnerE" id="subscriptionTagOwnerE" name="subscriptionTagOwnerE" class="tw-input-sm" disabled>
                                                        @if(count($subscriptionsE) > 0)
                                                            <option value="-1">{{ __('messages.subscriptions.view.form.placeholders.select') }}</option>
                                                            @foreach($subscriptionsE as $item)
                                                                @if($subscriptionTagE==$item->tag)
                                                                    <option value="{{$item->tag}}" selected>{{$item->name}}</option>
                                                                @else
                                                                    <option value="{{$item->tag}}">{{$item->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-span-12 md:col-span-3">
                                                <div class="space-y-1">
                                                    <label class="p-0 m-0" for="passwordOwnerE">{{ __('messages.subscriptions.view.form.labels.new_password') }}:</label>
                                                    <input id="passwordOwnerE" type="password" wire:model="passwordOwnerE" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.new_password') }}" maxlength="100">
                                                    @error('passwordOwnerE')
                                                    <span class="error " maxlength="100" style="font-size: 9pt; color:blue;">{{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-6 md:col-span-2 mt-0">
                                                <span class="tw-btn tw-btn-gray mt-4 py-2 w-full justify-center" onclick="resetDevice({{$user_idOwnerE}})" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.reset_device') }}</span>
                                            </div>

                                            <div class="col-span-6 md:col-span-2 mt-0">
                                                @if($stateOwnerE==1)
                                                    <span class="tw-btn tw-btn-red mt-4 py-2 w-full justify-center" onclick="changeStatusUser({{$stateOwnerE}},{{$user_idOwnerE}},'-1','owner')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.deactivate') }}</span>
                                                @else
                                                    <span class="tw-btn tw-btn-emerald mt-4 py-2 w-full justify-center" onclick="changeStatusUser({{$stateOwnerE}},{{$user_idOwnerE}},'-1','owner')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.activate') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-10">
                                            </div>
                                            <div class="col-span-12 md:col-span-2">
                                                <button class="tw-btn tw-btn-blue w-full justify-center" type="submit" >{{ __('messages.subscriptions.view.form.buttons.save_modifications') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="subscriber-admin-workspace mt-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="subscriber-admin-grid grid grid-cols-12 gap-4">
                                <section class="subscriber-admin-card subscriber-admin-card-wide col-span-12 xl:col-span-8 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                                    <div class="subscriber-card-heading mb-4 flex min-h-10 items-center gap-3 text-base font-semibold text-emerald-900 dark:text-emerald-100">
                                        
                                        <span>{{ __('messages.subscriptions.view.form.headings.admin_section') }}</span>
                                    </div>

                                    @if(count($chooseStationListRegisteredOwner) > 0)
                                        <div class="subscriber-table-shell max-h-96 overflow-auto rounded-md border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                            <table class="table table-striped table-sm table-bordered subscriber-admin-table">
                                                <thead class="themed-thead">
                                                    <tr>
                                                        <th style="width:5%;" scope="col">{{ __('messages.subscriptions.view.table.headers.number') }}</th>
                                                        <th style="width:30%;" scope="col">{{ __('messages.subscriptions.view.form.labels.administrator') }}</th>
                                                        <th style="width:15%;" scope="col">{{ __('messages.subscriptions.view.form.labels.expiration') }}</th>
                                                        <th style="width:50%;" scope="col">{{ __('messages.subscriptions.view.form.labels.station') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $cont=1; @endphp
                                                    @foreach($chooseStationListRegisteredOwner as $item)
                                                        <tr class="p-0 m-0">
                                                            <td>{{ $cont++ }}</td>
                                                            <td>
                                                                <div class="subscriber-admin-cell">
                                                                    <span>{{ strlen($item['name'].$item['lastname'])>0?($item['name'].' '.$item['lastname']):__('messages.subscriptions.view.form.text.no_data') }}</span>
                                                                    <button type="button" wire:click='visibleUpdateManager({{$item['id']}}, {{$item['userId'] }})' class="tw-btn tw-btn-gray">{{ __('messages.subscriptions.view.form.buttons.view_data') }}</button>
                                                                </div>
                                                            </td>
                                                            <td>{{ $item['date_expiry'] }}</td>
                                                            <td>
                                                                <div class="subscriber-station-cell">
                                                                    <span>{{ ($item['station']) }}</span>
                                                                    <div class="subscriber-station-actions" x-data="{ chooseStationListUpdateOwnerName: @entangle('chooseStationListUpdateOwnerName') }">
                                                                        <button @click="addStationManager(@js($item))" class="tw-btn tw-btn-emerald" type="button">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                        <button @click="changeStationManager(@js($item))" class="tw-btn tw-btn-amber" type="button">
                                                                            <i class="fa fa-pencil-alt"></i>
                                                                        </button>
                                                                        <button @click="deleteStationManager(@js($item))" class="tw-btn tw-btn-red" type="button">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif (auth()->user()->hasAnyRole(['super_manager','manager','meteorology','meteorologist']))
                                        <div class="create-admin-alert flex flex-col gap-4 rounded-lg border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900/60 dark:bg-amber-950/30 md:flex-row md:items-start">
                                            
                                            <div class="alert-content min-w-0 flex-1">
                                                <h5 class="mb-3 flex items-center text-lg font-semibold text-amber-950 dark:text-amber-100">
                                                    <i class="fas fa-info-circle mr-2 text-amber-600 dark:text-amber-400"></i>
                                                    {{ __('messages.subscriptions.view.form.text.admin_alert_title') }}
                                                </h5>
                                                <p class="mb-4 max-w-4xl text-sm leading-6 text-amber-900 dark:text-amber-100">
                                                    {{ __('messages.subscriptions.view.form.text.admin_alert_message') }}
                                                </p>
                                                <button type="button" class="tw-btn tw-btn-emerald create-admin-btn justify-center" wire:click='createUserManager'>
                                                    <i class="fas fa-user-plus mr-2"></i>
                                                    {{ __('messages.subscriptions.view.form.buttons.create_admins') }}
                                                </button>
                                                <div class="alert-note mt-4 flex gap-2 rounded-md border-l-4 border-sky-500 bg-white/80 p-3 text-sm text-sky-950 dark:bg-gray-900/60 dark:text-sky-100">
                                                    <i class="fas fa-lightbulb mr-2 text-sky-600 dark:text-sky-400"></i>
                                                    <small><strong>{{ __('messages.subscriptions.view.form.text.admin_alert_note_title') }}</strong> {{ __('messages.subscriptions.view.form.text.admin_alert_note_message') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="subscriber-empty-state rounded-md border border-dashed border-gray-300 bg-white p-6 text-center text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                            {{ __('messages.subscriptions.view.table.messages.no_records') }}
                                        </div>
                                    @endif
                                </section>

                                <aside class="subscriber-admin-card col-span-12 xl:col-span-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                                    <div class="subscriber-card-heading mb-4 flex min-h-10 items-center gap-3 text-base font-semibold text-emerald-900 dark:text-emerald-100">
                                        <i class="fas fa-map-marker-alt flex h-9 w-9 items-center justify-center rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200"></i>
                                        <span>{{ __('messages.subscriptions.view.form.headings.change_station') }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="p-0 m-0" for="chooseStationListUpdateOwnerName">{{ __('messages.subscriptions.view.form.labels.choose_station') }}:</label>
                                        <input id="chooseStationListUpdateOwnerName" type="text" list="chooseStationListUpdateOwnerL" wire:model="chooseStationListUpdateOwnerName" class="tw-input-sm w-full">
                                    </div>
                                    <datalist id="chooseStationListUpdateOwnerL" wire:ignore>
                                        @foreach($chooseStationListUpdateOwner as $item)
                                            <option value="{{ $item['name'] }}">
                                        @endforeach
                                    </datalist>
                                </aside>
                            </div>
                        </div>
                    @endif

                    @if($user_typeE==2)
                        <form wire:submit.prevent="updateUsersSubscription">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-5">
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                        <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12">
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="userOwnerNameAndLastNameE">{{ __('messages.subscriptions.view.form.labels.owner') }}:</label>
                                                            <input id="userOwnerNameAndLastNameE" type="text" wire:model="userOwnerNameAndLastNameE" class="tw-input-sm" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-8">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0" for="business_namEe">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                                <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="40" disabled>
                                                                @error('business_nameE')
                                                                <span class="error " maxlength="50" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ&\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha_numeric') }}" style="font-size: 9pt; color:blue;" >
                                                                    {{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 md:col-span-4">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0" for="nitE">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                                <input id="nitE" type="text" wire:model="nitE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="40" disabled>
                                                                @error('nitE')
                                                                <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-5">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0"  for="nameE">{{ __('messages.subscriptions.view.form.labels.name') }}:*</label>
                                                                <input id="nameE" type="text" wire:model="nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40" >
                                                                @error('nameE')
                                                                <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 md:col-span-5">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0"  for="lastnameE">{{ __('messages.subscriptions.view.form.labels.lastname') }}:*</label>
                                                                <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40" >
                                                                @error('lastnameE')
                                                                <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 md:col-span-2">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0" for="ciE">{{ __('messages.subscriptions.view.form.labels.ci') }}:*</label>
                                                                <input id="ciE" type="text" wire:model="ciE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.ci') }}" maxlength="40" >
                                                                @error('ciE')
                                                                <span class="error " maxlength="15" pattern="[a-zA-Z0-9]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-4">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0" for="number_phoneE">{{ __('messages.subscriptions.view.form.labels.phone') }}:*</label>
                                                                <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                                @error('number_phoneE')
                                                                <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 md:col-span-8">
                                                            <div class="space-y-1">
                                                                <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:*</label>
                                                                <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input-sm" disabled>
                                                                    @if(count($subscriptionsE) > 0)
                                                                        <option value="-1">{{ __('messages.subscriptions.view.form.placeholders.select') }}</option>
                                                                        @foreach($subscriptionsE as $item)
                                                                            @if($subscriptionTagE==$item->tag)
                                                                                <option value="{{$item->tag}}" selected>{{$item->name}}</option>
                                                                            @else
                                                                                <option value="{{$item->tag}}">{{$item->name}}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </select>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-4">
                                                            <label class="p-0 m-0" for="dateExpiryE">{{ __('messages.subscriptions.view.form.labels.expiration') }}:</label>
                                                            <div class="relative">
                                                                <div class="space-y-1">
                                                                    <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input-sm"  placeholder="" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 md:col-span-4">
                                                            @if($extensionE!==null)
                                                            <label class="p-0 m-0" for="extensionE">{{ __('messages.subscriptions.view.form.labels.extension') }}:</label>
                                                            <div class="relative">
                                                                <div class="space-y-1">
                                                                    <input id="extensionE" type="text" wire:model="extensionE" class="tw-input-sm"  placeholder="" disabled>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12">
                                                <label class="p-0 m-0" for="emailE">{{ __('messages.subscriptions.view.form.labels.email') }}:*</label>
                                                            <div class="space-y-1">
                                                    <input id="emailE" type="email" wire:model="emailE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                                @error('emailE')
                                                                <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12">
                                                <label class="p-0 m-0" for="passwordE">{{ __('messages.subscriptions.view.form.labels.new_password') }}:</label>
                                                            <div class="relative password-field">
                                                                <div class="space-y-1">
                                                        <input id="passwordE" type="password" wire:model="passwordE" class="tw-input-sm w-full pr-10"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.new_password') }}"  maxlength="100" >
                                                                    <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                                                    @error('passwordE')
                                                                    <span class="error " maxlength="100" style="font-size: 9pt; color:blue;">{{ $message }}
                                                                    </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">

                                                        <div class="col-span-12 md:col-span-8">
                                                            <div class="grid grid-cols-12 gap-4">
                                                                <div class="col-span-12 sm:col-span-6 md:col-span-6">
                                                                    <span class="tw-btn-xs tw-btn-gray w-full justify-center" onclick="resetDevice({{$user_idE}})" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.reset_device') }}</span>
                                                                </div>

                                                                <div class="col-span-12 sm:col-span-6 md:col-span-6">
                                                                    @if($stateE==1)
                                                                        <span class="tw-btn-xs tw-btn-red w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.deactivate') }}</span>
                                                                    @else
                                                                        <span class="tw-btn-xs tw-btn-emerald w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')"  style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.activate') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-7">
                                    <div class="grid grid-cols-12 gap-4">
                                        <p class="col-span-12 w-full rounded-md bg-gray-200 p-2 text-center text-base font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-100" style="width:100%;background-color:#c1c1c1;">{{ __('messages.subscriptions.view.form.headings.user_list') }}</p>
                                    </div>
                                    <div class="dependent-users-list" style="height: 60vh;overflow-y: auto; border: 1px solid #ccc;">
                                        @php $cont=1; @endphp
                                        @foreach($elementsE as $element)
                                            <div class="dependent-user-row px-1">
                                                <div class="dependent-user-actions">
                                                    <div class="dependent-user-counter">
                                                        {{ __('messages.subscriptions.view.form.text.user_counter', ['number' => $cont]) }}
                                                    </div>
                                                    <div class="mb-1">
                                                        <span class="tw-btn-xs tw-btn-gray w-full justify-center" onclick="resetDevice({{$element['idE']}})" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.reset_device') }}<br></span>
                                                    </div>
                                                    <div>
                                                        @if($element['stateE']==1)
                                                            <span class="tw-btn-xs tw-btn-red w-full justify-center" onclick="changeStatusUser({{$element['stateE']}},{{$element['idE']}},{{$cont-1}},'delegate')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.deactivate') }}</span>
                                                        @else
                                                            <span class="tw-btn-xs tw-btn-emerald w-full justify-center" onclick="changeStatusUser({{$element['stateE']}},{{$element['idE']}},{{$cont-1}},'delegate')"  style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.activate') }}</span>
                                                        @endif
                                                    </div>

                                                </div>
                                                <div class="dependent-user-fields">
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-5">
                                                            <input type="text" wire:model="elementsE.{{ $loop->index }}.nameE" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="50">
                                                        </div>
                                                        <div class="col-span-12 md:col-span-7">
                                                            <input type="text" wire:model="elementsE.{{ $loop->index }}.mailE" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-5">
                                                            <input type="text" wire:model="elementsE.{{ $loop->index }}.lastnameE" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="50">
                                                        </div>

                                                        <div class="col-span-12 md:col-span-7">
                                                            <input type="text" wire:model="elementsE.{{ $loop->index }}.phoneE" class="tw-input-sm w-full" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-5">
                                                            @if($element['is_password'] === __('messages.subscriptions.view.form.text.has_password'))
                                                                <span class="tw-badge tw-badge-blue w-full justify-center">{{ __('messages.subscriptions.view.form.text.has_password') }}</span>
                                                            @else
                                                                <span class="tw-badge tw-badge-amber w-full justify-center">{{ __('messages.subscriptions.view.form.text.no_password') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-span-12 md:col-span-7">
                                                            <input type="text" wire:model="elementsE.{{ $loop->index }}.passwordE" class="tw-input-sm w-full" @if($element['is_password'] === __('messages.subscriptions.view.form.text.has_password')) placeholder="{{ __('messages.subscriptions.view.form.placeholders.new_password') }}"  @else placeholder="{{ __('messages.subscriptions.view.form.placeholders.add_password') }}"  @endif maxlength="100">
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                            @php $cont=$cont+1; @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-8">

                                </div>
                                <div class="col-span-12 md:col-span-4">
                                    <button class="tw-btn tw-btn-blue w-full justify-center" type="submit" >{{ __('messages.subscriptions.view.form.buttons.save_changes') }}</button>
                                </div>
                            </div>
                        </form>
                    @elseif($user_typeE==3)
                        <form wire:submit.prevent="updateUsersSubscription">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-6">
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-12">
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12 md:col-span-6">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="userOwnerNameAndLastNameE">{{ __('messages.subscriptions.view.form.labels.owner') }}:</label>
                                                            <input id="userOwnerNameAndLastNameE" type="text" wire:model="userOwnerNameAndLastNameE" class="tw-input-sm" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-6">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="userManagerNameAndLastNameE">{{ __('messages.subscriptions.view.form.labels.manager') }}:</label>
                                                            <input id="userManagerNameAndLastNameE" type="text" wire:model="userManagerNameAndLastNameE" class="tw-input-sm" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12 md:col-span-8">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="business_namEe">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                            <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="40" disabled>
                                                            @error('business_nameE')
                                                            <span class="error " maxlength="50" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ&\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha_numeric') }}" style="font-size: 9pt; color:blue;" >
                                                                {{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="nitE">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                            <input id="nitE" type="text" wire:model="nitE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="40" disabled>
                                                            @error('nitE')
                                                            <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12 md:col-span-5">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0"  for="nameE">{{ __('messages.subscriptions.view.form.labels.name') }}:*</label>
                                                            <input id="nameE" type="text" wire:model="nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40" >
                                                            @error('nameE')
                                                            <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-5">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0"  for="lastnameE">{{ __('messages.subscriptions.view.form.labels.lastname') }}:*</label>
                                                            <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40" >
                                                            @error('lastnameE')
                                                            <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-2">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="ciE">{{ __('messages.subscriptions.view.form.labels.ci') }}:*</label>
                                                            <input id="ciE" type="text" wire:model="ciE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.ci') }}" maxlength="40" >
                                                            @error('ciE')
                                                            <span class="error " maxlength="15" pattern="[a-zA-Z0-9]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12 md:col-span-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="number_phoneE">{{ __('messages.subscriptions.view.form.labels.phone') }}:*</label>
                                                            <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                            @error('number_phoneE')
                                                            <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-8">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:*</label>
                                                            <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input-sm" disabled>
                                                                @if(count($subscriptionsE) > 0)
                                                                    <option value="-1">{{ __('messages.subscriptions.view.form.placeholders.select') }}</option>
                                                                    @foreach($subscriptionsE as $item)
                                                                        @if($subscriptionTagE==$item->tag)
                                                                            <option value="{{$item->tag}}" selected>{{$item->name}}</option>
                                                                        @else
                                                                            <option value="{{$item->tag}}">{{$item->name}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12 md:col-span-4">
                                                        <label class="p-0 m-0" for="dateExpiryE">{{ __('messages.subscriptions.view.form.labels.expiration') }}:</label>
                                                        <div class="relative">
                                                            <div class="space-y-1">
                                                                <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input-sm"  placeholder="" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-4">
                                                        @if($extensionE!==null)
                                                        <label class="p-0 m-0" for="extensionE">{{ __('messages.subscriptions.view.form.labels.extension') }}:</label>
                                                        <div class="relative">
                                                            <div class="space-y-1">
                                                                <input id="extensionE" type="text" wire:model="extensionE" class="tw-input-sm"  placeholder="" disabled>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12">
                                                        <label class="p-0 m-0" for="emailE">{{ __('messages.subscriptions.view.form.labels.email') }}:*</label>
                                                        <div class="space-y-1">
                                                                <input id="emailE" type="email" wire:model="emailE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                            @error('emailE')
                                                            <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12">
                                                    <label class="p-0 m-0" for="passwordE">{{ __('messages.subscriptions.view.form.labels.new_password') }}:</label>
                                                        <div class="relative password-field">
                                                            <div class="space-y-1">
                                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input-sm w-full pr-10"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.new_password') }}"  maxlength="100" >
                                                                <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                                                @error('passwordE')
                                                                <span class="error " maxlength="100" style="font-size: 9pt; color:blue;">{{ $message }}
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-12 gap-4">
                                                    <div class="col-span-12 md:col-span-8">
                                                        <div class="grid grid-cols-12 gap-4">
                                                            <div class="col-span-12 sm:col-span-6 md:col-span-6">
                                                                <span class="tw-btn-xs tw-btn-gray w-full justify-center" onclick="resetDevice({{$user_idE}})" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.reset_device') }}</span>
                                                            </div>

                                                            <div class="col-span-12 sm:col-span-6 md:col-span-6">
                                                                @if($stateE==1)
                                                                    <span class="tw-btn-xs tw-btn-red w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.deactivate') }}</span>
                                                                @else
                                                                    <span class="tw-btn-xs tw-btn-emerald w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')"  style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.activate') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-4 mt-3">
                                        <div class="col-span-12 md:col-span-8">
                                        </div>
                                <div class="col-span-12 md:col-span-4">
                                    <button class="tw-btn tw-btn-blue w-full justify-center" type="submit" >{{ __('messages.subscriptions.view.form.buttons.save_changes') }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div wire:ignore class="col-span-12 md:col-span-6">
                                </div>
                            </div>

                        </form>
                    @endif
                @elseif(in_array($subscriptionTagE,['demo professional', 'professional']))

                <form wire:submit.prevent="updateUsersSubscription">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="business_namEe">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                        <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="40" @if ($subscriptionTagE=='demo professional') disabled @endif>
                                                        @error('business_nameE')
                                                        <span class="error " maxlength="50" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ&\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha_numeric') }}" style="font-size: 9pt; color:blue;" >
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="nitE">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                        <input id="nitE" type="text" wire:model="nitE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="40" @if ($subscriptionTagE=='demo professional') disabled @endif>
                                                        @error('nitE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-5">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="nameE">{{ __('messages.subscriptions.view.form.labels.name') }}:*</label>
                                                        <input id="nameE" type="text" wire:model="nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40" >
                                                        @error('nameE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-5">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="lastnameE">{{ __('messages.subscriptions.view.form.labels.lastname') }}:*</label>
                                                        <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40" >
                                                        @error('lastnameE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-2">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="ciE">{{ __('messages.subscriptions.view.form.labels.ci') }}:*</label>
                                                        <input id="ciE" type="text" wire:model="ciE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.ci') }}" maxlength="40" >
                                                        @error('ciE')
                                                        <span class="error " maxlength="15" pattern="[a-zA-Z0-9]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="number_phoneE">{{ __('messages.subscriptions.view.form.labels.phone') }}:*</label>
                                                        <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                        @error('number_phoneE')
                                                        <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:*</label>
                                                        <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input-sm" disabled>
                                                            @if(count($subscriptionsE) > 0)
                                                                <option value="-1">{{ __('messages.subscriptions.view.form.placeholders.select') }}</option>
                                                                @foreach($subscriptionsE as $item)
                                                                    @if($subscriptionTagE==$item->tag)
                                                                        <option value="{{$item->tag}}" selected>{{$item->name}}</option>
                                                                    @else
                                                                        <option value="{{$item->tag}}">{{$item->name}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <label class="p-0 m-0" for="dateExpiryE">{{ __('messages.subscriptions.view.form.labels.expiration') }}:</label>
                                                    <div class="relative">
                                                        <div class="space-y-1">
                                                            <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input-sm"  placeholder="" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    @if($extensionE!==null)
                                                    <label class="p-0 m-0" for="extensionE">{{ __('messages.subscriptions.view.form.labels.extension') }}:</label>
                                                    <div class="relative">
                                                        <div class="space-y-1">
                                                            <input id="extensionE" type="text" wire:model="extensionE" class="tw-input-sm"  placeholder="" disabled>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <label class="p-0 m-0" for="emailE">{{ __('messages.subscriptions.view.form.labels.email') }}:*</label>
                                                    <div class="space-y-1">
                                                            <input id="emailE" type="email" wire:model="emailE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                        @error('emailE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-span-12 md:col-span-6">
                                                    <label class="p-0 m-0" for="passwordE">{{ __('messages.subscriptions.view.form.labels.new_password') }}:</label>
                                                    <div class="relative password-field">
                                                        <div class="space-y-1">
                                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input-sm w-full pr-10"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.new_password') }}"  maxlength="100" >
                                                            <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                                            @error('passwordE')
                                                            <span class="error " maxlength="100" style="font-size: 9pt; color:blue;">{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="grid grid-cols-12 gap-4">
                                                                <div class="col-span-12 md:col-span-6">
                                                                    <span class="tw-btn-xs tw-btn-gray w-full justify-center" onclick="resetDevice({{$user_idE}})" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.reset_device') }}</span>
                                                        </div>

                                                        <div class="col-span-12 md:col-span-6">
                                                            @if (auth()->user()->hasAnyRole(['super_manager','manager','meteorology','meteorologist']))
                                                                @if($stateE==1)
                                                                        <span class="tw-btn-xs tw-btn-red w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.deactivate') }}</span>
                                                                @else
                                                                        <span class="tw-btn-xs tw-btn-emerald w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')"  style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.activate') }}</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="grid grid-cols-12 gap-4 m-2">
                                                <div class="col-span-12 md:col-span-8">

                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <button class="tw-btn tw-btn-blue w-full justify-center" type="submit" >{{ __('messages.subscriptions.view.form.buttons.save_changes') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>

                        <div wire:ignore class="col-span-12 md:col-span-6">
                            <input id="latitudE" wire:model="latitudE"  name="latitudE"  type="hidden">
                            <input id="longitudE" wire:model="longitudE"  name="longitudE" type="hidden" >
                            <div class="rounded bg-gray-600 py-1 text-center font-semibold text-white dark:bg-gray-700">{{ __('messages.subscriptions.view.form.headings.geo_reference') }}</div>
                            <div id="map"></div>
                        </div>

                    </div>
                </form>
                <hr class="my-2">
                <div class="grid grid-cols-12 gap-4 mt-2 p-3" >
                    <div class="col-span-12 md:col-span-6" style="background-color:#dfdfdf">
                        <p class="mt-1 py-2 text-center text-base font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.form.headings.manage_station') }}</p>
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 md:col-span-8">
                                <div class="space-y-1">
                                    <label class="p-0 m-0" for="stationNameE">{{ __('messages.subscriptions.view.form.labels.station_linked') }}:</label>
                                    <input id="stationNameE" type="text" wire:model="stationNameE" class="tw-input-sm"  placeholder="" disabled>
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-4 mt-2">
                                @if($stationIdE!==null)
                                <button class="tw-btn tw-btn-red w-full justify-center mt-2" @click="unLinkStationE">{{ __('messages.subscriptions.view.form.buttons.unlink_station') }}</button>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4" x-data="{ stationNameNewE: @entangle('stationNameNewE') }">
                            <div class="col-span-12 md:col-span-8">
                                <div class="space-y-1">
                                    <label class="p-0 m-0" for="stationNameNewE">{{ __('messages.subscriptions.view.form.labels.choose_station') }}:</label>
                                    <input id="stationNameNewE" type="text" wire:model="stationNameNewE" list="listStationE" class="tw-input-sm"  placeholder="">
                                    <datalist id="listStationE">
                                        @foreach($dataStationListE as $item)
                                            <option value="{{ $item->name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-4 mt-2">
                                <button class="tw-btn tw-btn-amber mt-3 w-full justify-center" @click="changeStationE">{{ __('messages.subscriptions.view.form.buttons.change_station') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif(in_array($subscriptionTagE,['demo personal', 'personal']))
                <form wire:submit.prevent="updateUsersSubscription">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="business_namEe">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                        <input id="business_nameE" type="text" wire:model="business_nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="40" @if ($subscriptionTagE=='demo personal') disabled @endif>
                                                        @error('business_nameE')
                                                        <span class="error " maxlength="50" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ&\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha_numeric') }}" style="font-size: 9pt; color:blue;" >
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="nitE">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                        <input id="nitE" type="text" wire:model="nitE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="40" @if ($subscriptionTagE=='demo personal') disabled @endif>
                                                        @error('nitE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-5">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="nameE">{{ __('messages.subscriptions.view.form.labels.name') }}:*</label>
                                                        <input id="nameE" type="text" wire:model="nameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.name') }}" maxlength="40" >
                                                        @error('nameE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-5">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="lastnameE">{{ __('messages.subscriptions.view.form.labels.lastname') }}:*</label>
                                                        <input id="lastnameE" type="text" wire:model="lastnameE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.lastname') }}" maxlength="40" >
                                                        @error('lastnameE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-2">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="ciE">{{ __('messages.subscriptions.view.form.labels.ci') }}:*</label>
                                                        <input id="ciE" type="text" wire:model="ciE" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.ci') }}" maxlength="40" >
                                                        @error('ciE')
                                                        <span class="error " maxlength="15" pattern="[a-zA-Z0-9]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="number_phoneE">{{ __('messages.subscriptions.view.form.labels.phone') }}:*</label>
                                                        <input id="number_phoneE" type="number" wire:model="number_phoneE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="100">
                                                        @error('number_phoneE')
                                                        <span class="error " maxlength="15" pattern="[0-9]+" title="{{ __('messages.subscriptions.view.form.validation.numeric') }}" style="font-size: 9pt; color:blue;" >{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:*</label>
                                                        <select wire:model="subscriptionTagE" id="subscriptionTagE" name="subscriptionTagE" class="tw-input-sm" disabled>
                                                            @if(count($subscriptionsE) > 0)
                                                                <option value="-1">{{ __('messages.subscriptions.view.form.placeholders.select') }}</option>
                                                                @foreach($subscriptionsE as $item)
                                                                    @if($subscriptionTagE==$item->tag)
                                                                        <option value="{{$item->tag}}" selected>{{$item->name}}</option>
                                                                    @else
                                                                        <option value="{{$item->tag}}">{{$item->name}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                            <label class="p-0 m-0" for="dateExpiryE">{{ __('messages.subscriptions.view.form.labels.expiration') }}:</label>
                                                    <div class="relative">
                                                        <div class="space-y-1">
                                                            <input id="dateExpiryE" type="text" wire:model="dateExpiryE" class="tw-input-sm"  placeholder="" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    @if($extensionE!==null)
                                                    <label class="p-0 m-0" for="extensionE">{{ __('messages.subscriptions.view.form.labels.extension') }}:</label>
                                                    <div class="relative">
                                                        <div class="space-y-1">
                                                            <input id="extensionE" type="text" wire:model="extensionE" class="tw-input-sm"  placeholder="" disabled>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <label class="p-0 m-0" for="emailE">{{ __('messages.subscriptions.view.form.labels.email') }}:*</label>
                                                    <div class="space-y-1">
                                                            <input id="emailE" type="email" wire:model="emailE" class="tw-input-sm" placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="100">
                                                        @error('emailE')
                                                        <span class="error " maxlength="100" pattern="[a-zA-Z0-9áéíóúñÑÚÜÉÁÍÓ@.\s]+" title="{{ __('messages.subscriptions.view.form.validation.alpha') }} y caracteres: @." style="font-size: 9pt; color:blue;">{{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-span-12 md:col-span-6">
                                                    <label class="p-0 m-0" for="passwordE">{{ __('messages.subscriptions.view.form.labels.new_password') }}:</label>
                                                    <div class="relative password-field">
                                                        <div class="space-y-1">
                                                            <input id="passwordE" type="password" wire:model="passwordE" class="tw-input-sm w-full pr-10"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.new_password') }}"  maxlength="100" >
                                                            <span id="toggle-passwordE" class="password-toggleE" onclick="togglePasswordVisibilityE()">&#128065;</span>
                                                            @error('passwordE')
                                                            <span class="error " maxlength="100" style="font-size: 9pt; color:blue;">{{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <div class="col-span-12 md:col-span-6">
                                                            <span class="tw-btn-xs tw-btn-gray w-full justify-center" onclick="resetDevice({{$user_idE}})" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.reset_device') }}</span>
                                                        </div>

                                                        <div class="col-span-12 md:col-span-6">
                                                            @if (auth()->user()->hasAnyRole(['super_manager','manager','meteorology','meteorologist']))
                                                                @if($stateE==1)
                                                                    <span class="tw-btn-xs tw-btn-red w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')" style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.deactivate') }}</span>
                                                                @else
                                                                    <span class="tw-btn-xs tw-btn-emerald w-full justify-center" onclick="changeStatusUser({{$stateE}},{{$user_idE}},'-1','manager')"  style="width:100%;">{{ __('messages.subscriptions.view.form.buttons.activate') }}</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="grid grid-cols-12 gap-4 m-2">
                                                <div class="col-span-12 md:col-span-8">

                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <button class="tw-btn tw-btn-blue w-full justify-center" type="submit" >{{ __('messages.subscriptions.view.form.buttons.save_changes') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>

                        <div wire:ignore class="col-span-12 md:col-span-6">
                            <input id="latitudE" wire:model="latitudE"  name="latitudE"  type="hidden">
                            <input id="longitudE" wire:model="longitudE"  name="longitudE" type="hidden" >
                            <div class="rounded bg-gray-600 py-1 text-center font-semibold text-white dark:bg-gray-700">{{ __('messages.subscriptions.view.form.headings.geo_reference') }}</div>
                            <div wire:ignore id="map" style="height: 70vh"></div>
                        </div>

                    </div>
                </form>
                @endif

            @endif

            @if($formRenewal)
                <div>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="business_name">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                        <input id="business_name" type="text" value="{{$dataRenewal->business_name}}" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="nit">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                        <input id="nit" type="text" value="{{$dataRenewal->nit}}"   class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="name">{{ __('messages.subscriptions.view.form.labels.customer') }}:</label>
                                                        <input id="name" type="text" value="{{$dataRenewal->user->name}}" class="tw-input-sm"  placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="ci">{{ __('messages.subscriptions.view.form.labels.ci') }}:</label>
                                                        <input id="ci" type="text" value="{{$dataRenewal->user->ci}}"  class="tw-input-sm"  placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="number_phone">{{ __('messages.subscriptions.view.form.labels.phone') }}:</label>
                                                        <input id="number_phone" type="number" value="{{$dataRenewal->user->number_phone}}" class="tw-input-sm" placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:</label>
                                                        <input type="text" id="subscriptionName" name="subscriptionName" value="{{$dataRenewal->subscription->name}}" class="tw-input-sm" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="reg_date">{{ __('messages.subscriptions.view.form.text.expiration_start') }}</label>
                                                        <input id="reg_date" type="date" value="{{$dataRenewal->reg_date}}" class="tw-input-sm"  disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="date_expiry">{{ __('messages.subscriptions.view.form.text.expiration_current') }}</label>
                                                        <input id="date_expiry" type="date" value="{{$dataRenewal->date_expiry}}"   class="tw-input-sm" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-2">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="date_expiry">{{ __('messages.subscriptions.view.form.labels.total_paid') }}:</label>
                                                        <p class="tw-input-sm font-semibold">{{$totalRRegistered}} USD</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="grid grid-cols-12 gap-4 text-center mb-2">
                                                <div class="col-span-12">
                                                    <div class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.form.headings.extension_block') }}</div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">

                                                <div class="col-span-12 md:col-span-4">
                                                    @if($temporaryExtensionExt)
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <strong>{{ __('messages.subscriptions.view.form.text.has_extension_question') }}:</strong>
                                                        <span class="tw-badge tw-badge-green">{{ __('messages.common.options.yes') }}</span>
                                                    </div>
                                                    <div class="grid grid-cols-12 gap-4 mt-2">
                                                         <strong>{{ __('messages.subscriptions.view.form.text.total_to_pay') }} </strong> <div class=" text-center h6 ml-2">{{ $totalRWithExpiration}} USD</div>
                                                    </div>
                                                    @else
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <strong>{{ __('messages.subscriptions.view.form.text.has_extension_question') }}:</strong>
                                                        <span class="tw-badge tw-badge-red">{{ __('messages.common.options.no') }}</span>
                                                    </div>

                                                    @endif
                                                </div>

                                                @if($temporaryExtension)
                                                    <div class="col-span-12 md:col-span-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="temporary_extension_start">{{ __('messages.subscriptions.view.form.text.start_label') }}</label>
                                                            <input id="temporary_extension_start" type="date" value="{{$dataRenewal->temporary_extension_start}}" class="tw-input-sm"  disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="temporary_extension_end">{{ __('messages.subscriptions.view.form.text.end_label') }}</label>
                                                            <input id="temporary_extension_end" type="date" value="{{$dataRenewal->temporary_extension_end}}" class="tw-input-sm"  disabled>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4 text-center  mb-2">
                                    <div class="col-span-12">
                                        <div class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.form.headings.renewal_details') }}</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">

                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="numberOfYearsR">{{ __('messages.subscriptions.view.form.labels.duration_time') }}:</label>
                                                        <select id="numberOfYearsR" wire:model.live="numberOfYearsR" class="tw-input-sm">
                                                            @for($i=0; $i < count($listYearsR); $i++)
                                                            <option value="{{$listYearsR[$i]}}">{{$listYearsR[$i]}}</option>
                                                            @endfor
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newDateBeginR">{{ __('messages.subscriptions.view.form.labels.new_start_date') }}:</label>
                                                        <input id="newDateBeginR" type="date" wire:model.live="newDateBeginR"   class="tw-input-sm">
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newDateEndR">{{ __('messages.subscriptions.view.form.labels.new_end_date') }}:</label>
                                                        <input id="newDateEndR" type="date" wire:model="newDateEndR"   class="tw-input-sm bg-amber-100 dark:bg-amber-900/30" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="grid grid-cols-12 gap-4 my-1 w-full">
                                                <div class="flex items-center gap-2">
                                                    <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="checkbox" name="discountIncidenceCheckR" id="discountIncidenceCheckR" wire:model.live="discountIncidenceCheckR" >
                                                    <label class="text-sm text-gray-700 dark:text-gray-300" for="discountIncidenceCheckR">
                                                        {{ __('messages.subscriptions.view.form.labels.incidences') }}
                                                    </label>
                                                </div>
                                            </div>
                                            @if($discountIncidenceCheckR)
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="daysDiscountByIncidence">{{ __('messages.subscriptions.view.form.labels.incidence_days') }}:</label>
                                                        <input id="daysDiscountByIncidence" type="number" wire:model.live="daysDiscountByIncidence"   class="tw-input-sm" min="1" max="365">
                                                    </div>
                                                </div>

                                            </div>
                                            @endif

                                            <div class="grid grid-cols-12 gap-4 my-1 w-full">
                                                <div class="flex items-center gap-2">
                                                    <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="checkbox" name="discountNumberStationsCheckR" id="discountNumberStationsCheckR" wire:model.live="discountNumberStationsCheckR"  @if(!$is_discountNumberStationsCheckR) disabled @endif>
                                                    @if($is_discountNumberStationsCheckR)
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="discountNumberStationsCheckR">
                                                            {{ __('messages.subscriptions.view.form.labels.stations_quantity') }}: {{$discountValueStationsR}} %
                                                        </label>
                                                    @else
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="discountNumberStationsCheckR">
                                                            {{ __('messages.subscriptions.view.form.labels.stations_quantity') }}: {{ __('messages.subscriptions.view.form.text.discount_not_applicable') }}
                                                        </label>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-12 gap-4 my-1 w-full">
                                                <div class="flex items-center gap-2">
                                                    <input class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" type="checkbox" name="discountDurationCheckR" id="discountDurationCheckR" wire:model.live="discountDurationCheckR"  @if(!$is_discountDurationCheckR) disabled @endif>
                                                    @if($is_discountDurationCheckR)
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="discountDurationCheckR">
                                                            {{ __('messages.subscriptions.view.form.labels.duration_time') }}: {{$durationValueDiscountR}} %
                                                        </label>
                                                    @else
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="discountDurationCheckR">
                                                            {{ __('messages.subscriptions.view.form.labels.duration_time') }}: {{ __('messages.subscriptions.view.form.text.discount_not_applicable') }}
                                                        </label>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="grid grid-cols-12 gap-4 mt-4">
                                                <div class="col-span-12 md:col-span-3">
                                                    <div class="space-y-1 mt-4">
                                                        <div class="p-0 m-0 mt-3">{{ __('messages.subscriptions.view.form.labels.total') }}:</div>
                                                        <div class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">{{$totalR}} USD</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-8">
                            <button class="tw-btn tw-btn-blue w-full justify-center" wire:click='saveRenewal' >{{ __('messages.subscriptions.view.form.buttons.renew_subscription') }}</button>
                        </div>
                        <div class="col-span-12 md:col-span-4">
                            <button class="tw-btn tw-btn-red w-full justify-center" wire:click='closeFormButton' >{{ __('messages.subscriptions.view.form.buttons.close') }}</button>
                        </div>
                    </div>
                </div>
            @endif

            @if($formExtension)
                @if($dataExtension==null && $extensionAlertWarningExt==true)
                <div>
                    <div class="grid grid-cols-12 gap-4 justify-center">
                        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="rounded-t-lg bg-gray-600 px-4 py-3 text-white dark:bg-gray-700">
                              <h5>{{ __('messages.subscriptions.view.form.text.extension_warning_title') }}</h5>
                            </div>
                            <div class="px-4 py-3">
                              <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.subscriptions.view.form.text.extension_warning_message') }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div>
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 md:col-span-6">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="business_name">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                        <input id="business_name" type="text" value="{{$dataExtension->subscription->business_name}}" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="nit">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                        <input id="nit" type="text" value="{{$dataExtension->subscription->nit}}"   class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="name">{{ __('messages.subscriptions.view.form.labels.customer') }}:</label>
                                                        <input id="name" type="text" value="{{$dataExtension->user->name}}  {{$dataExtension->user->lastname}}" class="tw-input-sm"  placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="ci">{{ __('messages.subscriptions.view.form.labels.ci') }}:</label>
                                                        <input id="ci" type="text" value="{{$dataExtension->user->ci}}"  class="tw-input-sm"  placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="number_phone">{{ __('messages.subscriptions.view.form.labels.phone') }}:</label>
                                                        <input id="number_phone" type="number" value="{{$dataExtension->user->number_phone}}" class="tw-input-sm" placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:</label>
                                                        <input type="text" id="subscriptionName" name="subscriptionName" value="{{$dataExtension->subscription->subscription->name}}" class="tw-input-sm" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="reg_date">{{ __('messages.subscriptions.view.form.text.start_label') }}</label>
                                                        <input id="reg_date" type="date" value="{{$dataExtension->subscription->reg_date}}" class="tw-input-sm"  disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-6">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="date_expiry">{{ __('messages.subscriptions.view.form.text.expiration_current') }}</label>
                                                        <input id="date_expiry" type="date" value="{{$dataExtension->subscription->date_expiry}}"   class="tw-input-sm" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    @if($temporaryExtensionExt)
                                                    <strong>{{ __('messages.subscriptions.view.form.text.has_extension_question') }}</strong> <span class="tw-badge tw-badge-green">{{ __('messages.subscriptions.view.form.text.has_extension_yes') }}</span>
                                                    @else
                                                    <strong>{{ __('messages.subscriptions.view.form.text.has_extension_question') }}</strong> <span class="tw-badge tw-badge-red">{{ __('messages.subscriptions.view.form.text.has_extension_no') }}</span>
                                                    @endif
                                                </div>
                                                @if($temporaryExtensionExt)
                                                    <div class="col-span-12 md:col-span-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="temporary_extension_start">{{ __('messages.subscriptions.view.form.text.start_label') }}</label>
                                                            <input id="temporary_extension_start" type="date" value="{{$dataExtension->subscription->temporary_extension_start}}" class="tw-input-sm"  disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-span-12 md:col-span-4">
                                                        <div class="space-y-1">
                                                            <label class="p-0 m-0" for="temporary_extension_end">{{ __('messages.subscriptions.view.form.text.end_label') }}</label>
                                                            <input id="temporary_extension_end" type="date" value="{{$dataExtension->subscription->temporary_extension_end}}" class="tw-input-sm"  disabled>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($temporaryExtensionExt)
                                            <div class="grid grid-cols-12 gap-4 justify-end">
                                                <div class="col-span-12 md:col-span-4">
                                                    <button class="tw-btn tw-btn-red w-full justify-center" wire:click='removeExtension' wire:confirm.prompt="{{ __('messages.subscriptions.view.form.confirm.remove_extension') }}">{{ __('messages.subscriptions.view.form.buttons.remove_extension') }}</button>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-12 md:col-span-6">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newDateBeginExt">{{ __('messages.subscriptions.view.form.labels.new_start_date') }}:</label>
                                                        <input id="newDateBeginExt" type="date" wire:model.live="newDateBeginExt"   class="tw-input-sm">
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newDateEndExt">{{ __('messages.subscriptions.view.form.labels.new_end_date') }}:</label>
                                                        <input id="newDateEndExt" type="date" wire:model.live="newDateEndExt"   class="tw-input-sm " >
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12 xl:col-span-4">
                                            <button class="tw-btn tw-btn-blue w-full justify-center" wire:click='saveExtension' wire:confirm.prompt="{{ __('messages.subscriptions.view.form.confirm.save_extension') }}">{{ __('messages.subscriptions.view.form.buttons.save_extension') }}</button>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                @endif
            @endif

            @if($formAddNewUser)
                <form wire:submit.prevent="saveNewUser">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="business_name">{{ __('messages.subscriptions.view.form.labels.business_name') }}:</label>
                                                        <input id="business_name" type="text" value="{{$dataNewUser->business_name}}" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.business_name') }}" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="nit">{{ __('messages.subscriptions.view.form.labels.nit') }}:</label>
                                                        <input id="nit" type="text" value="{{$dataNewUser->nit}}"   class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.labels.nit') }}" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0"  for="name">{{ __('messages.subscriptions.view.form.labels.customer') }}:</label>
                                                        <input id="name" type="text" value="{{$dataNewUser->user->name}}" class="tw-input-sm"  placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="ci">{{ __('messages.subscriptions.view.form.labels.ci') }}:</label>
                                                        <input id="ci" type="text" value="{{$dataNewUser->user->ci}}"  class="tw-input-sm"  placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="number_phone">{{ __('messages.subscriptions.view.form.labels.phone') }}:</label>
                                                        <input id="number_phone" type="number" value="{{$dataNewUser->user->number_phone}}" class="tw-input-sm" placeholder="" maxlength="150" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-8">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.subscription_type') }}:</label>
                                                        <input type="text" id="subscriptionName" name="subscriptionName" value="{{$dataNewUser->subscription->name}}" class="tw-input-sm" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.duration') }}:</label>
                                                        @if($dataNewUser->amount > 1)
                                                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{$dataNewUser->amount}} {{ __('messages.subscriptions.view.form.text.years') }}</div>
                                                        @else
                                                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{$dataNewUser->amount}} {{ __('messages.subscriptions.view.form.text.year') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.text.start_date') }}</label>
                                                        <div class=" h5">{{$dataNewUser->accumulation_date}}</div>
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.text.expiration_date') }}</label>
                                                        <div class=" h5">{{$dataNewUser->date_expiry}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-12">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newUserName">{{ __('messages.subscriptions.view.form.labels.full_name') }}:</label>
                                                        <input id="newUserName" type="text" wire:model="newUserName" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.user_full_name') }}" maxlength="50">
                                                        @error('newUserName')
                                                        <span class="error" maxlength="50" style="font-size: 9pt; color:blue;" >
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-6">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newUserEmail">{{ __('messages.subscriptions.view.form.labels.email') }}:</label>
                                                        <input id="newUserEmail" type="email" wire:model="newUserEmail" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.email') }}" maxlength="80">
                                                        @error('newUserEmail')
                                                        <span class="error" maxlength="50" style="font-size: 9pt; color:blue;">
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newUserPhone">{{ __('messages.subscriptions.view.form.labels.phone') }}:</label>
                                                        <input id="newUserPhone" type="number" wire:model="newUserPhone" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.phone') }}" maxlength="15" >
                                                        @error('newUserPhone')
                                                        <span class="error" maxlength="15" style="font-size: 9pt; color:blue;">
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-6">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="newUserPassword">{{ __('messages.subscriptions.view.form.labels.password') }}:</label>
                                                        <input id="newUserPassword" type="text" wire:model="newUserPassword" class="tw-input-sm"  placeholder="{{ __('messages.subscriptions.view.form.placeholders.password') }}" maxlength="30" >
                                                        @error('newUserPassword')
                                                        <span class="error" maxlength="50" style="font-size: 9pt; color:blue;">
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0" for="number_phone">{{ __('messages.subscriptions.view.form.labels.discount') }}:</label>
                                                        @if($is_discountNU)
                                                        <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{$dataNewUser->subscription->discount}}%</div>
                                                        @else
                                                        <div class="font-semibold h5">{{ __('messages.subscriptions.view.form.text.discount_not_applicable') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-span-12 md:col-span-4">
                                                    <div class="space-y-1">
                                                        <label class="p-0 m-0">{{ __('messages.subscriptions.view.form.labels.total') }}:</label>
                                                        <div class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">{{$totalNU}} USD</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-8">
                            <button class="tw-btn tw-btn-blue w-full justify-center" type="submit">{{ __('messages.subscriptions.view.form.buttons.add_user') }}</button>
                        </div>
                        <div class="col-span-12 md:col-span-4">
                            <button class="tw-btn tw-btn-red w-full justify-center" wire:click='closeFormButton' >{{ __('messages.subscriptions.view.form.buttons.close') }}</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @if(count($dataHistorical) > 0)
    <div x-data="{ open: false }"
         x-on:open-history-modal.window="open = true"
         x-on:keydown.escape.window="open = false"
         x-on:click.self="open = false"
         x-show="open" x-cloak
         class="fixed inset-0 z-[60] flex items-start justify-center overflow-y-auto bg-black/50 p-4 sm:p-6"
         role="dialog" aria-modal="true">
        <div class="relative z-10 w-full max-w-lg">

            <div class="rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.subscriptions.view.form.text.history_modal_title') }}</h5>
                <button type="button" class="text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" x-on:click="open = false" aria-label="{{ __('messages.actions.close') }}"><i class="fas fa-times"></i></button>
                </div>
                <div class="space-y-2 px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                @foreach($dataHistorical as $item)
                    <p>{{ __('messages.subscriptions.view.form.text.start_label') }} {{$item->start_date}} - {{ __('messages.subscriptions.view.form.text.expiration_date') }} {{$item->start_date}}  - {{ __('messages.subscriptions.view.form.labels.duration') }}: {{$item->amount}} {{ $item->amount > 1 ? __('messages.subscriptions.view.form.text.years') : __('messages.subscriptions.view.form.text.year') }}</p>
                @endforeach
                </div>
                <div class="flex justify-end border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    <button type="button" class="tw-btn tw-btn-gray" x-on:click="open = false">{{ __('messages.actions.close') }}</button>
                </div>
            </div>

        </div>
    @endif
</div>

@section('css')
<style>
    /* Table layout */
    .content-wrapper .subscription-row {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e9ecef !important;
    }

    .content-wrapper .subscription-row:hover {
        background-color: #f8f9fa !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .content-wrapper .subscription-row td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle;
        border-right: 1px solid #f1f5f9;
    }

    .content-wrapper .subscription-row td:last-child {
        border-right: none;
    }

    .content-wrapper .client-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .content-wrapper .client-name {
        font-size: 0.95rem;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .content-wrapper .client-name i {
        font-size: 0.875rem;
    }

    .content-wrapper .contact-info small {
        line-height: 1.8;
        font-size: 0.8rem;
    }

    .content-wrapper .contact-info i {
        width: 14px;
        text-align: center;
    }

    .content-wrapper .amount-display {
        font-size: 1.1rem;
        padding: 0.5rem;
    }

    .content-wrapper .debts-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .content-wrapper .debt-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.25rem 0;
    }

    .content-wrapper .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .content-wrapper .action-buttons .group {
        width: 100%;
    }

    .content-wrapper .action-buttons button {
        transition: all 0.2s ease;
        font-weight: 500;
        border-width: 1px;
    }

    .content-wrapper .action-buttons .tw-btn-outline-blue:hover,
    .content-wrapper .action-buttons .tw-btn-outline:hover,
    .content-wrapper .action-buttons .tw-btn-outline-red:hover {
        transform: translateY(-1px);
    }

    .content-wrapper td small {
        display: inline-flex;
        align-items: center;
    }

    .content-wrapper td small i {
        width: 16px;
    }

    .content-wrapper .blurred {
        filter: blur(4px);
        transition: filter 0.3s ease;
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        background: #f7fafc;
        display: inline-block;
    }

    .content-wrapper .blurred:hover {
        filter: blur(0);
        background: #e6fffa;
    }

    /* Form styles */
    .content-wrapper .space-y-1 label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .content-wrapper .tw-input,
    .content-wrapper .tw-input-sm {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .content-wrapper .tw-input:focus,
    .content-wrapper .tw-input-sm:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        background-color: #f0fdf4;
    }

    .content-wrapper .tw-input:disabled,
    .content-wrapper .tw-input-sm:disabled {
        background-color: #f7fafc;
        color: #718096;
        cursor: not-allowed;
    }

    .content-wrapper select.tw-input,
    .content-wrapper select.tw-input-sm {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.625rem 2.5rem 0.625rem 0.875rem;
        background-color: white;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23047857' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .content-wrapper select.tw-input:disabled,
    .content-wrapper select.tw-input-sm:disabled {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23cbd5e0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    }

    .content-wrapper .tw-btn,
    .content-wrapper .tw-btn-xs {
        transition: all 0.3s ease;
    }

    .content-wrapper .tw-btn:hover,
    .content-wrapper .tw-btn-xs:hover {
        transform: translateY(-2px);
    }

    .content-wrapper .flex.items-center.gap-2 {
        padding: 0.75rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s ease;
    }

    .content-wrapper .flex.items-center.gap-2:hover {
        background-color: #f0fdf4;
    }

    .content-wrapper input[type="checkbox"]:checked,
    .content-wrapper input[type="radio"]:checked {
        background-color: #047857;
        border-color: #047857;
    }

    .content-wrapper hr[style*="background-color: #A4B0B8FF"] {
        height: 2px !important;
        background: linear-gradient(90deg, transparent 0%, #cbd5e0 50%, transparent 100%) !important;
        border: none !important;
        margin: 1.5rem 0 !important;
    }

    .content-wrapper .text-center.font-semibold {
        font-size: 1.1rem;
        color: #2d3748;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 0.75rem;
        background: linear-gradient(135deg, #f0fdf4 0%, #e6fffa 100%);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Corporate user sections */
    .content-wrapper div[style*="height: 60vh"][style*="overflow-y: auto"],
    .content-wrapper div[style*="height: 90vh"][style*="overflow-y: auto"] {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1rem;
        background: white;
    }

    .content-wrapper div[style*="overflow-y: auto"] .tw-input-sm,
    .content-wrapper div[style*="overflow-y: auto"] .tw-input {
        width: 100%;
    }

    .content-wrapper .password-field .tw-input-sm {
        width: 100%;
        min-width: 0;
        padding-right: 2.5rem;
    }

    .content-wrapper .password-field .password-toggleE {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #64748b;
        line-height: 1;
    }

    .content-wrapper .dependent-users-list {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
        border-color: #d1d5db !important;
        border-radius: 0.75rem;
        padding: 0.875rem;
        background: #ffffff;
    }

    .content-wrapper .dependent-user-row {
        display: grid;
        grid-template-columns: minmax(120px, 150px) minmax(0, 1fr);
        gap: 0.875rem;
        align-items: stretch;
        padding: 0.875rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background: #f8fafc;
    }

    .content-wrapper .dependent-user-actions,
    .content-wrapper .dependent-user-fields {
        min-width: 0;
    }

    .content-wrapper .dependent-user-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .content-wrapper .dependent-user-counter {
        border-radius: 0.375rem;
        background: #e2e8f0;
        color: #0f172a;
        font-size: 0.875rem;
        font-weight: 700;
        line-height: 1.25;
        padding: 0.5rem;
        text-align: center;
    }

    .content-wrapper .dependent-user-actions .tw-btn-xs {
        width: 100%;
        min-height: 2rem;
        white-space: normal;
        word-break: normal;
        line-height: 1.2;
        text-align: center;
    }

    .content-wrapper .dependent-user-fields .grid + .grid {
        margin-top: 0.375rem;
    }

    @media (max-width: 767px) {
        .content-wrapper .dependent-user-row {
            grid-template-columns: 1fr;
        }
    }

    /* Subscriber administration workspace */
    .content-wrapper .subscriber-admin-workspace {
        margin: 1.5rem 0;
    }

    .content-wrapper .subscriber-admin-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 360px);
        gap: 1rem;
        align-items: start;
    }

    .content-wrapper .subscriber-admin-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #ffffff;
        padding: 1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .content-wrapper .dark .subscriber-admin-card,
    .dark .content-wrapper .subscriber-admin-card {
        border-color: #374151;
        background: #111827;
    }

    .content-wrapper .subscriber-admin-card-wide {
        min-width: 0;
    }

    .content-wrapper .subscriber-card-heading {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        min-height: 2.5rem;
        margin-bottom: 0.875rem;
        color: #064e3b;
        font-size: 1rem;
        font-weight: 700;
    }

    .content-wrapper .subscriber-card-heading i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: #ecfdf5;
        color: #047857;
    }

    .content-wrapper .subscriber-table-shell {
        max-height: 360px;
        overflow: auto;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }

    .content-wrapper .subscriber-admin-table {
        margin-bottom: 0;
        min-width: 760px;
    }

    .content-wrapper .subscriber-admin-table th,
    .content-wrapper .subscriber-admin-table td {
        vertical-align: middle;
    }

    .content-wrapper .subscriber-admin-cell,
    .content-wrapper .subscriber-station-cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .content-wrapper .subscriber-station-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
    }

    .content-wrapper .subscriber-empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 2rem;
        color: #64748b;
        text-align: center;
        background: #f8fafc;
    }

    /* Create admin alert */
    .content-wrapper .create-admin-alert {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        padding: 1.5rem;
        background: #fffbeb;
        border-radius: 0.75rem;
        border: 1px solid #fbbf24;
        box-shadow: 0 8px 18px rgba(146, 64, 14, 0.08);
        animation: slideIn 0.5s ease-in-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .content-wrapper .alert-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        flex: 0 0 4rem;
        border-radius: 0.75rem;
        background: #fef3c7;
    }

    .content-wrapper .alert-icon i {
        font-size: 2rem;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }

    .content-wrapper .alert-content {
        flex: 1;
        min-width: 0;
    }

    .content-wrapper .alert-content h5 {
        display: flex;
        align-items: center;
        margin: 0 0 0.625rem;
        color: #78350f;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .content-wrapper .alert-content p {
        max-width: 62rem;
        margin: 0 0 1rem;
        color: #92400e;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .content-wrapper .create-admin-btn {
        background: linear-gradient(135deg, #047857 0%, #10b981 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.95rem !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px rgba(4, 120, 87, 0.3) !important;
        transition: all 0.3s ease !important;
    }

    .content-wrapper .create-admin-btn:hover {
        background: linear-gradient(135deg, #065f46 0%, #059669 100%) !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 6px 12px rgba(4, 120, 87, 0.4) !important;
    }

    .content-wrapper .create-admin-btn:active {
        transform: translateY(-1px) !important;
    }

    .content-wrapper .alert-note {
        background: rgba(255, 255, 255, 0.7);
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border-left: 3px solid #3b82f6;
        margin-top: 1rem;
    }

    .content-wrapper .alert-note small {
        color: #1e40af;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .content-wrapper .subscriber-admin-grid {
            grid-template-columns: 1fr;
        }

        .content-wrapper .subscriber-admin-cell,
        .content-wrapper .subscriber-station-cell {
            align-items: flex-start;
            flex-direction: column;
        }

        .content-wrapper .create-admin-alert {
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
        }

        .content-wrapper .alert-icon {
            width: 3.25rem;
            height: 3.25rem;
            flex-basis: 3.25rem;
        }
    }

    /* Date pills */
    .content-wrapper .date-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.875rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .content-wrapper .date-badge i {
        font-size: 0.9rem;
        opacity: 0.7;
    }

    .content-wrapper .date-start {
        background: #eff6ff;
        border-left: 3px solid #3b82f6;
        color: #1e40af;
    }

    .content-wrapper .date-active {
        background: #f0fdf4;
        border-left: 3px solid #10b981;
        color: #047857;
    }

    .content-wrapper .date-expired {
        background: #fef2f2;
        border-left: 3px solid #ef4444;
        color: #991b1b;
    }

    .content-wrapper .date-expired strong {
        font-weight: 600;
    }
</style>
@stop

<div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.system_settings.form_title') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.form_subtitle') }}</p>
        </div>

        @can('system_settings.update')
            {{-- Se deshabilita también mientras el archivo viaja al servidor: guardar
                 antes de que termine la subida grabaría los ajustes sin la imagen. --}}
            <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save,logoImage,faviconImage"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70">
                <i class="fas fa-save" wire:loading.remove wire:target="save,logoImage,faviconImage"></i>
                <i class="fas fa-spinner fa-spin" wire:loading wire:target="save,logoImage,faviconImage"></i>
                <span>{{ __('mma.admin.system_settings.actions.save') }}</span>
            </button>
        @endcan
    </div>

    <div class="grid gap-5 p-4 xl:grid-cols-[1fr_360px]">
        <div class="space-y-5">
            <section class="space-y-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.sections.identity') }}</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.product_name') }}</label>
                        <input type="text" wire:model="form.product_name" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.product_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.public_title') }}</label>
                        <input type="text" wire:model="form.public_title" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.public_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.short_description') }}</label>
                    <textarea rows="4" wire:model="form.short_description" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    @error('form.short_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.sections.contact') }}</h3>
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.contact_email') }}</label>
                        <input type="email" wire:model="form.contact_email" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.contact_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.contact_phone') }}</label>
                        <input type="text" wire:model="form.contact_phone" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.contact_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.whatsapp_phone') }}</label>
                        <input type="text" wire:model="form.whatsapp_phone" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.whatsapp_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.sections.social') }}</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach (['facebook', 'instagram', 'youtube', 'tiktok'] as $network)
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.social.'.$network) }}</label>
                            <input type="url" wire:model="form.social_links.{{ $network }}" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('form.social_links.'.$network) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.sections.seo') }}</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.seo_title') }}</label>
                        <input type="text" wire:model="form.seo_title" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.seo_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-3 self-end rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <input type="checkbox" wire:model="form.landing_show_rankings" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span>{{ __('mma.admin.system_settings.form.landing_show_rankings') }}</span>
                    </label>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.system_settings.form.seo_description') }}</label>
                    <textarea rows="3" wire:model="form.seo_description" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    @error('form.seo_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.system_settings.form.logo_path') }}</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.image_help') }}</p>
                <div class="mt-4 flex h-36 items-center justify-center rounded-md border border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900">
                    @if ($logoImage)
                        <img src="{{ $logoImage->temporaryUrl() }}" alt="{{ __('mma.admin.system_settings.form.logo_path') }}" class="max-h-32 max-w-full object-contain">
                    @elseif ($currentLogoPath)
                        <img src="{{ asset($currentLogoPath) }}" alt="{{ __('mma.admin.system_settings.form.logo_path') }}" class="max-h-32 max-w-full object-contain">
                    @endif
                </div>
                <input type="file" wire:model="logoImage" accept="image/png,image/jpeg,image/webp" class="mt-3 w-full text-sm text-gray-700 dark:text-gray-300">
                <p wire:loading wire:target="logoImage" class="mt-2 flex items-center gap-2 text-xs font-medium text-amber-600 dark:text-amber-400">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>{{ __('mma.admin.system_settings.messages.uploading') }}</span>
                </p>
                @error('logoImage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </section>

            <section class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.system_settings.form.favicon_path') }}</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.image_help') }}</p>
                <div class="mt-4 flex h-24 items-center justify-center rounded-md border border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900">
                    @if ($faviconImage)
                        <img src="{{ $faviconImage->temporaryUrl() }}" alt="{{ __('mma.admin.system_settings.form.favicon_path') }}" class="max-h-20 max-w-full object-contain">
                    @elseif ($currentFaviconPath)
                        <img src="{{ asset($currentFaviconPath) }}" alt="{{ __('mma.admin.system_settings.form.favicon_path') }}" class="max-h-20 max-w-full object-contain">
                    @endif
                </div>
                <input type="file" wire:model="faviconImage" accept="image/png,image/jpeg,image/webp" class="mt-3 w-full text-sm text-gray-700 dark:text-gray-300">
                <p wire:loading wire:target="faviconImage" class="mt-2 flex items-center gap-2 text-xs font-medium text-amber-600 dark:text-amber-400">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>{{ __('mma.admin.system_settings.messages.uploading') }}</span>
                </p>
                @error('faviconImage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </section>
        </aside>
    </div>
</div>

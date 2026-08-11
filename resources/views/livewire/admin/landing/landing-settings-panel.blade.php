<div class="space-y-5">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.landing.form_title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.landing.form_subtitle') }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('landing.home') }}" target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    <i class="fas fa-external-link-alt"></i>
                    <span>{{ __('mma.admin.landing.actions.open_public') }}</span>
                </a>

                @can('landing.update')
                    <button type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70">
                        <i class="fas fa-save"></i>
                        <span>{{ __('mma.admin.landing.actions.save') }}</span>
                    </button>
                @endcan
            </div>
        </div>

        <div class="grid gap-5 p-4 xl:grid-cols-[1fr_340px]">
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.landing.form.public_title') }}</label>
                    <input type="text" wire:model="form.public_title" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @error('form.public_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.landing.form.short_description') }}</label>
                    <textarea rows="4" wire:model="form.short_description" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    @error('form.short_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.landing.form.seo_title') }}</label>
                        <input type="text" wire:model="form.seo_title" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('form.seo_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-3 self-end rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <input type="checkbox" wire:model="form.landing_show_rankings" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span>{{ __('mma.admin.landing.form.landing_show_rankings') }}</span>
                    </label>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('mma.admin.landing.form.seo_description') }}</label>
                    <textarea rows="3" wire:model="form.seo_description" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    @error('form.seo_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <aside class="space-y-4">
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('mma.admin.landing.stats.published') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['published'] }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('mma.admin.landing.stats.featured') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['featured'] }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('mma.admin.landing.stats.drafts') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['drafts'] }}</p>
                    </div>
                </div>

                <section class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.landing.latest_events') }}</h3>
                        @can('events.view')
                            <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                {{ __('mma.admin.landing.actions.manage_events') }}
                            </a>
                        @endcan
                    </div>

                    <div class="mt-3 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($latestEvents as $event)
                            <div class="py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $event->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $event->venue?->name ?? __('mma.admin.common.not_available') }}
                                            @if ($event->starts_at)
                                                &middot; {{ $event->starts_at->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    @if ($event->is_featured)
                                        <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                                            {{ __('mma.landing.featured') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.landing.empty_events') }}</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>

@props([
    'close',
    'confirm',
    'title',
    'message',
    'targetName' => null,
    'confirmText' => null,
    'cancelText' => null,
    'variant' => 'danger',
    'icon' => null,
    'maxWidth' => 'md',
    'loadingTarget' => null,
])

@php
    $variantConfig = [
        'danger' => [
            'header' => 'bg-red-600 text-white',
            'button' => 'tw-btn-red',
            'icon' => 'fas fa-trash',
            'confirm' => __('messages.actions.delete'),
        ],
        'warning' => [
            'header' => 'bg-amber-500 text-white',
            'button' => 'tw-btn-amber',
            'icon' => 'fas fa-exclamation-triangle',
            'confirm' => __('messages.notices.js.confirm_button'),
        ],
        'success' => [
            'header' => 'bg-emerald-600 text-white',
            'button' => 'tw-btn-emerald',
            'icon' => 'fas fa-check',
            'confirm' => __('messages.notices.js.confirm_button'),
        ],
        'info' => [
            'header' => 'bg-blue-600 text-white',
            'button' => 'tw-btn-blue',
            'icon' => 'fas fa-info-circle',
            'confirm' => __('messages.notices.js.confirm_button'),
        ],
    ];

    $config = $variantConfig[$variant] ?? $variantConfig['danger'];
    $resolvedIcon = $icon ?: $config['icon'];
    $resolvedConfirmText = $confirmText ?: $config['confirm'];
    $resolvedCancelText = $cancelText ?: __('messages.actions.cancel');
    $resolvedLoadingTarget = $loadingTarget ?: $confirm;
@endphp

<x-tw-modal
    :close="$close"
    :max-width="$maxWidth"
    :title="$title"
    :icon="$resolvedIcon"
    :header-class="$config['header']"
>
    <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
        <p>{{ $message }}</p>

        @if(filled($targetName))
            <p class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 font-semibold text-gray-900 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-100">
                {{ $targetName }}
            </p>
        @endif

        {{ $slot }}
    </div>

    <x-slot:footer>
        <button type="button" class="tw-btn tw-btn-gray" wire:click="{{ $close }}">
            {{ $resolvedCancelText }}
        </button>
        <button type="button"
                class="tw-btn {{ $config['button'] }}"
                wire:click="{{ $confirm }}"
                wire:loading.attr="disabled"
                wire:target="{{ $resolvedLoadingTarget }}">
            @if($resolvedIcon)
                <i class="{{ $resolvedIcon }} text-xs"></i>
            @endif
            {{ $resolvedConfirmText }}
        </button>
    </x-slot:footer>
</x-tw-modal>

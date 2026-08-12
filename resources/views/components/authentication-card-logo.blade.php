@php
    $settings = app(\App\Services\SystemSettingsService::class);
@endphp

<a href="/" class="inline-flex items-center justify-center">
    <img
        src="{{ $settings->logoUrl() }}"
        alt="{{ $settings->productName() }}"
        class="h-16 w-auto max-w-[220px] object-contain"
    >
</a>

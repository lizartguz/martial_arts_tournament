@props([
    'config' => [],
])

@php
    // Compatibilidad: este componente antes renderizaba un selectpicker de bootstrap-select.
    // Ahora delega en <x-tw-select> (combobox Tailwind/Alpine) mapeando sus atributos.
    $placeholder = $attributes->get('data-placeholder') ?? $attributes->get('title');
    $search      = filter_var($attributes->get('data-live-search', 'true'), FILTER_VALIDATE_BOOLEAN);
    $actionsBox  = filter_var($attributes->get('data-actions-box', 'true'), FILTER_VALIDATE_BOOLEAN);
    $maxOptions  = $config['maximumSelectionLength'] ?? $attributes->get('data-max-options');
@endphp

<x-tw-select
    :id="$attributes->get('id')"
    :name="$attributes->get('name')"
    :multiple="$attributes->has('multiple')"
    :placeholder="$placeholder"
    :search="$search"
    :actions-box="$actionsBox"
    :max-options="$maxOptions"
>
    {{ $slot }}
</x-tw-select>

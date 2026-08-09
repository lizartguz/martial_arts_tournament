@props([
    'weatherType',
    'breakpoint' => 'md',
])

@php
    $legends = [
        'temperature' => [
            ['label' => '-15', 'color' => '#2030B0', 'textColor' => '#FFFFFF'],
            ['label' => '-10', 'color' => '#205FB0', 'textColor' => '#FFFFFF'],
            ['label' => '-5', 'color' => '#258DB9', 'textColor' => '#FFFFFF'],
            ['label' => '0', 'color' => '#28B238', 'textColor' => '#FFFFFF'],
            ['label' => '5', 'color' => '#EBDC1B', 'textColor' => '#FFFFFF'],
            ['label' => '15', 'color' => '#EAB241', 'textColor' => '#FFFFFF'],
            ['label' => '25', 'color' => '#E88C13', 'textColor' => '#FFFFFF'],
            ['label' => '35', 'color' => '#B61B34', 'textColor' => '#FFFFFF'],
            ['label' => '40+', 'color' => '#3D1396', 'textColor' => '#FFFFFF'],
        ],
        'wind' => [
            ['label' => '< 15', 'color' => '#EAEAD1EF', 'textColor' => '#111111'],
            ['label' => '35', 'color' => '#9C9080', 'textColor' => '#FFFFFF'],
            ['label' => '60', 'color' => '#857A53', 'textColor' => '#FFFFFF'],
            ['label' => '> 90', 'color' => '#B28428B2', 'textColor' => '#FFFFFF'],
        ],
        'rain' => [
            ['label' => '< 18', 'color' => '#50245e', 'textColor' => '#FFFFFF'],
            ['label' => '16', 'color' => '#a61414', 'textColor' => '#FFFFFF'],
            ['label' => '16', 'color' => '#dc5c0c', 'textColor' => '#FFFFFF'],
            ['label' => '14', 'color' => '#ea9155', 'textColor' => '#FFFFFF'],
            ['label' => '11', 'color' => '#e2c726', 'textColor' => '#FFFFFF'],
            ['label' => '9', 'color' => '#199d60', 'textColor' => '#FFFFFF'],
            ['label' => '7', 'color' => '#1D5C63', 'textColor' => '#FFFFFF'],
            ['label' => '5', 'color' => '#55B6DCFF', 'textColor' => '#FFFFFF'],
            ['label' => '0.1', 'color' => '#2056CAFF', 'textColor' => '#FFFFFF'],
            ['label' => '0', 'color' => '#FFFFFF', 'textColor' => '#111111'],
        ],
    ];

    $breakpointVisibility = [
        'md' => [
            'mobile' => 'block md:hidden',
            'desktop' => 'hidden md:flex',
        ],
    ];

    $items = $legends[$weatherType] ?? [];
    $visibility = $breakpointVisibility[$breakpoint] ?? $breakpointVisibility['md'];
    $segmentWidth = count($items) > 0 ? (100 / count($items)) : 100;
    $verticalItems = array_reverse($items);
@endphp

@if (count($items) > 0)
    <div
        class="{{ $visibility['mobile'] }}"
        style="position: absolute; top: 0; left: 0; z-index: 1000; width: 100%; border-radius: 0;"
        role="progressbar"
        aria-label="{{ __('messages.stations.map.controls.' . $weatherType) }}"
    >
        <div style="display: flex; height: 20px; border-radius: 0; overflow: hidden;">
            @foreach ($items as $item)
                <div
                    style="width: {{ $segmentWidth }}%; background-color: {{ $item['color'] }}; color: {{ $item['textColor'] }}; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;"
                >
                    {{ $item['label'] }}
                </div>
            @endforeach
        </div>
    </div>

    <div
        class="{{ $visibility['desktop'] }}"
        style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%); z-index: 1000; width: 47px; flex-direction: column; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18); background-color: rgba(255, 255, 255, 0.92);"
        aria-label="{{ __('messages.stations.map.controls.' . $weatherType) }}"
    >
        @foreach ($verticalItems as $item)
            <div
                style="background-color: {{ $item['color'] }}; color: {{ $item['textColor'] }}; min-height: 36px; display: flex; align-items: center; justify-content: center; padding: 6px 4px; font-size: 12px; font-weight: 700; line-height: 1; border-bottom: 1px solid rgba(255, 255, 255, 0.28);"
            >
                {{ $item['label'] }}
            </div>
        @endforeach
    </div>
@endif

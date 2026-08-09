@props([
    'title' => __('messages.stations.map.page.header'),
    'defaultStatus' => 'Lejano',
    'defaultZoom' => '6.3',
])

<style>
    .map-zoom-indicator {
        position: absolute;
        right: 12px;
        bottom: 12px;
        z-index: 1000;
        min-width: 170px;
        padding: 12px 14px;
        border-radius: 14px;
        background: rgba(15, 23, 42, 0.86);
        color: #f8fafc;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.28);
        backdrop-filter: blur(8px);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        --zoom-accent: #3968a6;
    }

    .map-zoom-indicator__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .map-zoom-indicator__title {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        opacity: 0.92;
    }

    .map-zoom-indicator__status {
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: var(--zoom-accent);
        font-size: 11px;
        line-height: 1;
    }

    .map-zoom-indicator__value {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 10px;
    }

    .map-zoom-indicator__value-number {
        color: var(--zoom-accent);
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }

    .map-zoom-indicator__value-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.75;
    }

    .map-zoom-indicator__track {
        width: 100%;
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        overflow: hidden;
    }

    .map-zoom-indicator__meter {
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--zoom-accent), rgba(255, 255, 255, 0.92));
        transition: width 0.2s ease, background 0.2s ease;
    }

    @media (max-width: 767.98px) {
        .map-zoom-indicator {
            right: 10px;
            bottom: 10px;
            min-width: 150px;
            padding: 10px 12px;
        }

        .map-zoom-indicator__value-number {
            font-size: 24px;
        }
    }
</style>

<div class="map-zoom-indicator">
    <div class="map-zoom-indicator__header">
        <span class="map-zoom-indicator__title">
            <i class="fas fa-search-plus"></i>
            {{ $title }}
        </span>
        <span id="zoom-status" class="map-zoom-indicator__status">{{ $defaultStatus }}</span>
    </div>
    <div class="map-zoom-indicator__value">
        <span id="zoom-level" class="map-zoom-indicator__value-number">{{ $defaultZoom }}</span>
        <span class="map-zoom-indicator__value-label">zoom</span>
    </div>
    <div class="map-zoom-indicator__track">
        <div id="zoom-meter" class="map-zoom-indicator__meter"></div>
    </div>
</div>

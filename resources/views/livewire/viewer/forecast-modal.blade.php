<div>
    @if($show)
        <x-tw-modal close="closeForecastModal" max-width="6xl"
                    header-class="bg-blue-600 text-white"
                    icon="fas fa-cloud-sun"
                    :title="__('messages.viewer.forecast.modal_title') . ($stationName ? ' - '.$stationName : '')">

            @if($stationId)
                <livewire:viewer.forecast-viewer
                    :stationId="$stationId"
                    :key="'modal-forecast-'.$modalKey.'-'.$stationId" />
            @else
                <div class="tw-alert tw-alert-blue">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ __('messages.viewer.forecast.select_station') }}
                </div>
            @endif

            <x-slot:footer>
                <button type="button" wire:click="closeForecastModal" class="tw-btn tw-btn-gray">
                    <i class="fas fa-times mr-1"></i> {{ __('messages.actions.close') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>

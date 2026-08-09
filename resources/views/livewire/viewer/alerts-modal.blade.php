<div>
    @if($show)
        <x-tw-modal close="closeAlertModal" max-width="3xl"
                    header-class="bg-amber-500 text-white"
                    icon="fas fa-exclamation-triangle"
                    :title="__('messages.viewer.alerts.modal_title') . ($stationName ? ' - '.$stationName : '')">

            @if($stationId)
                <livewire:viewer.alerts-viewer
                    :stationId="$stationId"
                    :key="'modal-alerts-'.$modalKey.'-'.$stationId" />
            @else
                <div class="tw-alert tw-alert-blue">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ __('messages.viewer.alerts.select_station') }}
                </div>
            @endif

            <x-slot:footer>
                <button type="button" wire:click="closeAlertModal" class="tw-btn tw-btn-gray">
                    <i class="fas fa-times mr-1"></i> {{ __('messages.actions.close') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>

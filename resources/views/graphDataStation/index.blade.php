<x-app-layout>
    <x-slot:title>{{ __('messages.graphs.page') }}</x-slot:title>
    @livewire('graphDataStation.graphDataStation',['typeS' => $typeS])
</x-app-layout>

<x-app-layout>
    <x-slot:title>{{ __('messages.downloads.page') }}</x-slot:title>
    @livewire('downloadDataStation.downloadDataStation',['typeS' => $typeS])
</x-app-layout>

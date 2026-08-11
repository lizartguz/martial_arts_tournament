@extends('layouts.admin')

@section('title', __('mma.admin.fight_results.page_title'))

@section('content_header')
    <div>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.fight_results.page_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.fight_results.subtitle') }}</p>
    </div>
@stop

@section('content')
    <livewire:admin.fight-results.fight-result-table />
@stop

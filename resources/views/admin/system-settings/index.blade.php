@extends('layouts.admin')

@section('title', __('mma.admin.system_settings.page_title'))

@section('content_header')
    <div>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.system_settings.page_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.system_settings.subtitle') }}</p>
    </div>
@stop

@section('content')
    <livewire:admin.system-settings.system-setting-form />
@stop

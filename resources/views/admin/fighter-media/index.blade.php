@extends('layouts.admin')

@section('title', __('mma.admin.fighter_media.page_title'))

@section('content_header')
    <div>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.fighter_media.page_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.fighter_media.subtitle') }}</p>
    </div>
@stop

@section('content')
    <livewire:admin.fighter-media.fighter-media-table />
@stop

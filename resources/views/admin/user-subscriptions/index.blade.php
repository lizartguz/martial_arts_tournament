@extends('layouts.admin')

@section('title', __('mma.admin.user_subscriptions.page_title'))

@section('content_header')
    <div>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.user_subscriptions.page_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.user_subscriptions.subtitle') }}</p>
    </div>
@stop

@section('content')
    <livewire:admin.user-subscriptions.user-subscription-table />
@stop

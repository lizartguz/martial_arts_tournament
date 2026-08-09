@extends('layouts.admin')

@section('title', __('messages.authorization.page_title'))

@section('content_header')
    <h5>{{ __('messages.authorization.page_title') }}</h5>
@stop

@section('content')
    <div class="grid gap-6 xl:grid-cols-2">
        <livewire:authorization.roles-table />
        <livewire:authorization.permissions-table />
    </div>
@stop

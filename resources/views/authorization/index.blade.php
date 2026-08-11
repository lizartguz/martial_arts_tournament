@extends('layouts.admin')

@section('title', __('messages.authorization.page_title'))

@section('content_header')
    <h5>{{ __('messages.authorization.page_title') }}</h5>
@stop

@section('content')
    <div class="grid gap-6">
        <livewire:authorization.roles-table />
    </div>
@stop

@extends('layouts.admin')

@section('title', __('messages.dashboard.title'))

@section('content_header')
    <h1>{{ __('messages.dashboard.header') }}</h1>
@stop

@section('content')
    <div>
        @livewire('admin-dashboard.admin-dashboard')
    </div>
@stop

@section('css')
@stop

@section('js')
@stop

@push('js')
@endpush


@extends('layouts.admin')

@section('title', __('mma.admin.fighter_teams.page_title'))

@section('content_header')
    <div>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.fighter_teams.page_title') }}</h1>
    </div>
@stop

@section('content')
    <livewire:admin.fighter-teams.fighter-team-table />
@stop

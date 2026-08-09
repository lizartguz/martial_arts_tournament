@extends('layouts.admin')

@section('title', trim($title ?? '') !== '' ? trim($title) : null)

@section('content_header')
    @if(isset($header) && trim((string) $header) !== '')
        {{ $header }}
    @elseif(trim($title ?? '') !== '')
        <h1 class="m-0">{{ $title }}</h1>
    @endif
@stop

@section('content')
    {{ $slot }}
@stop

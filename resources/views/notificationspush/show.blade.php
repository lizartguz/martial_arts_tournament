@extends('layouts.admin')

@section('title', $notification->title)

@section('content_header')
    <div class="flex flex-wrap items-center justify-between">
        <div>
            <div class="mb-1 flex flex-wrap items-center">
                <span class="text-xs font-bold uppercase text-gray-500">{{ __('messages.notices.detail.kicker') }}</span>
                @if($isPreview ?? false)
                    <span class="tw-badge tw-badge-amber ml-2 px-2 py-1">{{ __('messages.notices.detail.preview_badge') }}</span>
                @endif
            </div>
            <h1 class="mb-0 text-2xl font-semibold">{{ $notification->title }}</h1>
        </div>
        <a href="{{ $backUrl ?? url()->previous() }}" class="tw-btn tw-btn-outline mt-2 md:mt-0">
            <i class="fa fa-arrow-left mr-1"></i> {{ __('messages.notices.buttons.back') }}
        </a>
    </div>
@stop

@section('content')
    @if($isPreview ?? false)
        <div class="notification-preview-alert mb-4 rounded-lg p-4" role="alert">
            <i class="fa fa-eye mr-2"></i>
            {{ __('messages.notices.detail.preview_alert') }}
        </div>
    @endif

    <article class="notification-detail rounded-lg bg-white shadow-sm dark:bg-gray-800">
        <div class="notification-detail-body">
            <div class="notification-detail-meta">
                <span><i class="fa fa-calendar-alt mr-1"></i> {{ optional($notification->reg_date)->format('d/m/Y H:i') }}</span>
                @if($notification->deadline)
                    <span><i class="fa fa-clock mr-1"></i> {{ __('messages.notices.detail.available_until') }} {{ $notification->deadline->format('d/m/Y') }}</span>
                @endif
            </div>

            <div class="notification-detail-description">
                {!! nl2br(e($notification->description)) !!}
            </div>

            @if($images->isNotEmpty())
                <div class="notification-detail-gallery">
                    @foreach($images as $image)
                        <a href="{{ $image }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ $image }}" alt="{{ __('messages.notices.gallery.attachment_alt', ['number' => $loop->iteration]) }}">
                        </a>
                    @endforeach
                </div>
            @endif

            @if($notification->link)
                <div class="mt-4">
                    <a href="{{ $notification->link }}" target="_blank" rel="noopener noreferrer" class="tw-btn tw-btn-blue">
                        <i class="fa fa-external-link-alt mr-1"></i> {{ __('messages.notices.detail.open_link') }}
                    </a>
                </div>
            @endif
        </div>
    </article>
@stop

@section('css')
    <style>
        .notification-detail {
            border-radius: 1rem;
            overflow: hidden;
        }

        .notification-preview-alert {
            border: 1px solid #fde68a;
            border-radius: 0.85rem;
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            color: #92400e;
            box-shadow: 0 8px 24px rgba(146, 64, 14, 0.08);
        }

        .notification-detail .notification-detail-body {
            padding: clamp(1.25rem, 3vw, 2.5rem);
        }

        .notification-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.5rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .notification-detail-description {
            margin-top: 1.5rem;
            color: #1e293b;
            font-size: 1.05rem;
            line-height: 1.75;
        }

        .notification-detail-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .notification-detail-gallery a {
            display: block;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.8rem;
            background: #f8fafc;
        }

        .notification-detail-gallery img {
            display: block;
            width: 100%;
            height: 260px;
            object-fit: contain;
        }
    </style>
@stop

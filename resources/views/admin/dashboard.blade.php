@extends('layouts.admin')

@section('title', __('mma.admin.dashboard.page_title'))

@section('content_header')
    <div>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('mma.admin.dashboard.page_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mma.admin.dashboard.subtitle') }}</p>
    </div>
@stop

@section('content')
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['icon' => 'fas fa-calendar-check', 'label' => __('mma.admin.dashboard.cards.published_events'), 'value' => $summary['published_events']],
            ['icon' => 'fas fa-users', 'label' => __('mma.admin.dashboard.cards.active_fighters'), 'value' => $summary['active_fighters']],
            ['icon' => 'fas fa-inbox', 'label' => __('mma.admin.dashboard.cards.pending_purchase_requests'), 'value' => $summary['pending_purchase_requests']],
            ['icon' => 'fas fa-receipt', 'label' => __('mma.admin.dashboard.cards.pending_payments'), 'value' => $summary['pending_payments']],
        ] as $card)
            <div class="rounded border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900/30">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $card['value'] }}</div>
                    </div>
                    <i class="{{ $card['icon'] }} text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="tw-panel-header">
            <div>
                <strong class="text-gray-800 dark:text-gray-200">{{ __('mma.admin.dashboard.events_title') }}</strong>
                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('mma.admin.dashboard.events_subtitle') }}</div>
            </div>
        </div>

        <div class="tw-table-wrap">
            <table class="tw-table">
                <thead class="themed-thead">
                    <tr>
                        <th>{{ __('mma.admin.dashboard.columns.event') }}</th>
                        <th>{{ __('mma.admin.dashboard.columns.venue') }}</th>
                        <th>{{ __('mma.admin.dashboard.columns.date') }}</th>
                        <th>{{ __('mma.admin.dashboard.columns.featured') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($upcomingEvents as $event)
                        <tr>
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ $event->name }}</td>
                            <td>{{ $event->venue?->name ?? __('messages.common.options.not_applicable') }}</td>
                            <td>{{ $event->starts_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="tw-badge {{ $event->is_featured ? 'tw-badge-green' : 'tw-badge-gray' }}">
                                    {{ $event->is_featured ? __('messages.common.options.yes') : __('messages.common.options.no') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">{{ __('mma.admin.common.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

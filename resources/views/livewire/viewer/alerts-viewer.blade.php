<div>
    @if($stationId)
        <div class="alerts-container">
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="bg-amber-400 px-4 py-2 text-gray-900">
                    <h5 class="m-0 text-lg font-semibold">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('messages.viewer.alerts.title') }}
                    </h5>
                </div>
                <div class="p-4">
                    @if(count($alerts) > 0)
                        <div class="tw-table-wrap">
                            <table class="tw-table">
                                <thead class="themed-thead">
                                    <tr>
                                        <th style="width: 25%;">{{ __('messages.viewer.alerts.table.date') }}</th>
                                        <th style="width: 75%;">{{ __('messages.viewer.alerts.table.message') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alerts as $alert)
                                        <tr>
                                            <td>
                                                <small class="text-xs text-gray-500">
                                                    <i class="far fa-calendar"></i>
                                                    {{ \Carbon\Carbon::parse($alert['reg_date'] ?? $alert->reg_date)->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="tw-alert tw-alert-amber mb-0 py-2">
                                                    {{ $alert['description'] ?? $alert->message ?? __('messages.viewer.alerts.no_description') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="tw-alert tw-alert-blue mb-0">
                            <i class="fas fa-info-circle"></i>
                            {{ __('messages.viewer.alerts.empty') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="tw-alert tw-alert-amber">
            <i class="fas fa-exclamation-triangle"></i>
            {{ __('messages.viewer.alerts.select_station') }}
        </div>
    @endif
</div>

<div>
    {{-- Sección 1: Tarjetas métricas principales (small-box) --}}
    <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        {{-- Estaciones --}}
        <div class="relative overflow-hidden rounded-lg bg-sky-500 text-white shadow">
            <div class="p-4">
                <div class="text-3xl font-bold">{{ $data['stations']['total'] }}</div>
                <div class="text-sm">{{ __('messages.dashboard.cards.active_stations') }}</div>
                <div class="text-xs opacity-80">+{{ $data['stations']['newThisMonth'] }} {{ __('messages.dashboard.cards.this_month') }}</div>
            </div>
            <div class="absolute right-3 top-3 text-6xl opacity-20"><i class="fa fa-satellite"></i></div>
            <a href="{{ route('station') }}" class="block bg-black/10 px-4 py-2 text-xs hover:bg-black/20">
                {{ __('messages.dashboard.cards.more_info') }} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>

        {{-- Usuarios --}}
        <div class="relative overflow-hidden rounded-lg bg-emerald-500 text-white shadow">
            <div class="p-4">
                <div class="text-3xl font-bold">{{ $data['users']['total'] }}</div>
                <div class="text-sm">{{ __('messages.dashboard.cards.active_users') }}</div>
                <div class="text-xs opacity-80">+{{ $data['users']['newThisMonth'] }} {{ __('messages.dashboard.cards.this_month') }}</div>
            </div>
            <div class="absolute right-3 top-3 text-6xl opacity-20"><i class="fa fa-users"></i></div>
            <a href="{{ route('userManagement') }}" class="block bg-black/10 px-4 py-2 text-xs hover:bg-black/20">
                {{ __('messages.dashboard.cards.more_info') }} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>

        {{-- Suscripciones --}}
        <div class="relative overflow-hidden rounded-lg bg-amber-400 text-white shadow">
            <div class="p-4">
                <div class="text-3xl font-bold">{{ $data['subscriptions']['active'] }}</div>
                <div class="text-sm">{{ __('messages.dashboard.cards.active_subscriptions') }}</div>
                <div class="text-xs opacity-80">{{ $data['subscriptions']['expiringSoon7'] }} {{ __('messages.dashboard.cards.expiring_7days') }}</div>
            </div>
            <div class="absolute right-3 top-3 text-6xl opacity-20"><i class="fa fa-id-card"></i></div>
            <a href="{{ route('userSubscription') }}" class="block bg-black/10 px-4 py-2 text-xs hover:bg-black/20">
                {{ __('messages.dashboard.cards.more_info') }} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>

        {{-- Alertas --}}
        <div class="relative overflow-hidden rounded-lg bg-red-500 text-white shadow">
            <div class="p-4">
                <div class="text-3xl font-bold">{{ $data['alerts']['last7Days'] }}</div>
                <div class="text-sm">{{ __('messages.dashboard.cards.alerts_7days') }}</div>
                <div class="text-xs opacity-80">{{ $data['alerts']['critical'] }} {{ __('messages.dashboard.cards.critical') }}</div>
            </div>
            <div class="absolute right-3 top-3 text-6xl opacity-20"><i class="fa fa-exclamation-triangle"></i></div>
            <a href="#" class="block bg-black/10 px-4 py-2 text-xs hover:bg-black/20">
                {{ __('messages.dashboard.cards.more_info') }} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    {{-- Fila 2: info-box --}}
    <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="flex overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex w-16 flex-shrink-0 items-center justify-center bg-blue-600 text-2xl text-white">
                <i class="fa fa-thermometer-half"></i>
            </div>
            <div class="flex flex-col justify-center px-4 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.infobox.avg_temp') }}</span>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $data['weather']['avgTemperature'] }}°C</span>
            </div>
        </div>

        <div class="flex overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex w-16 flex-shrink-0 items-center justify-center bg-blue-600 text-2xl text-white">
                <i class="fa fa-cloud-rain"></i>
            </div>
            <div class="flex flex-col justify-center px-4 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.infobox.rain_7days') }}</span>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $data['weather']['totalRainfall'] }} mm</span>
            </div>
        </div>

        <div class="flex overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex w-16 flex-shrink-0 items-center justify-center bg-emerald-600 text-2xl text-white">
                <i class="fa fa-dollar-sign"></i>
            </div>
            <div class="flex flex-col justify-center px-4 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.infobox.income_month') }}</span>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">${{ number_format($data['financial']['incomeThisMonth'], 2) }}</span>
            </div>
        </div>

        <div class="flex overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex w-16 flex-shrink-0 items-center justify-center bg-amber-400 text-2xl text-white">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex flex-col justify-center px-4 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.infobox.uptime_stations') }}</span>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $data['dataQuality']['uptime'] }}%</span>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="mb-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.stations_by_department') }}</h3>
            </div>
            <div class="p-4"><canvas id="chartDepartments" height="250"></canvas></div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.subscription_status') }}</h3>
            </div>
            <div class="p-4"><canvas id="chartSubscriptions" height="250"></canvas></div>
        </div>
    </div>

    <div class="mb-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.users_by_type') }}</h3>
            </div>
            <div class="p-4"><canvas id="chartUserTypes" height="250"></canvas></div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.payment_methods') }}</h3>
            </div>
            <div class="p-4"><canvas id="chartPaymentMethods" height="250"></canvas></div>
        </div>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.user_trend') }}</h3>
        </div>
        <div class="p-4"><canvas id="chartUserTrends" height="80"></canvas></div>
    </div>

    <div class="mb-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.temperature_30days') }}</h3>
            </div>
            <div class="p-4"><canvas id="chartTemperature" height="150"></canvas></div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.precipitation_30days') }}</h3>
            </div>
            <div class="p-4"><canvas id="chartPrecipitation" height="150"></canvas></div>
        </div>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.charts.income_by_month') }}</h3>
        </div>
        <div class="p-4"><canvas id="chartIncome" height="80"></canvas></div>
    </div>

    {{-- TOP 10 Estaciones + Alertas Recientes --}}
    <div class="mb-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.dashboard.top_stations.heading') }}</h3>
            </div>
            <div class="max-h-96 overflow-auto">
                <table class="tw-table whitespace-nowrap">
                    <thead class="themed-thead sticky top-0">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.dashboard.top_stations.station') }}</th>
                            <th>{{ __('messages.dashboard.top_stations.location') }}</th>
                            <th>{{ __('messages.dashboard.top_stations.records') }}</th>
                            <th>{{ __('messages.dashboard.top_stations.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['topStations'] as $index => $station)
                        <tr>
                            <td>
                                @if($index < 3)
                                    <span class="tw-badge tw-badge-amber">#{{ $index + 1 }}</span>
                                @else
                                    <span class="tw-badge tw-badge-gray">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>{{ $station->name }}</td>
                            <td>{{ $station->location }}</td>
                            <td>{{ number_format($station->total_records) }}</td>
                            <td>
                                @if($station->state == 1)
                                    <span class="tw-badge tw-badge-green">{{ __('messages.dashboard.top_stations.active') }}</span>
                                @else
                                    <span class="tw-badge tw-badge-red">{{ __('messages.dashboard.top_stations.inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-amber-200 bg-white shadow-sm dark:border-amber-800 dark:bg-gray-800">
            <div class="border-b border-amber-200 px-4 py-3 dark:border-amber-800">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fa fa-exclamation-circle text-amber-500"></i> {{ __('messages.dashboard.recent_alerts.heading') }}
                </h3>
            </div>
            <ul class="max-h-96 divide-y divide-gray-100 overflow-y-auto dark:divide-gray-700">
                @foreach($data['recentAlerts'] as $alert)
                <li class="px-4 py-3 text-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <strong class="text-gray-800 dark:text-gray-200">{{ $alert->station_name }}</strong>
                            <p class="mb-1 text-gray-600 dark:text-gray-400">{{ $alert->description }}</p>
                            <small class="text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($alert->reg_date)->format('d/m/Y H:i') }}</small>
                        </div>
                        @if($alert->f >= 7)
                            <span class="tw-badge tw-badge-red flex-shrink-0">{{ __('messages.dashboard.recent_alerts.critical') }}</span>
                        @elseif($alert->f >= 5)
                            <span class="tw-badge tw-badge-amber flex-shrink-0">{{ __('messages.dashboard.recent_alerts.high') }}</span>
                        @else
                            <span class="tw-badge tw-badge-blue flex-shrink-0">{{ __('messages.dashboard.recent_alerts.medium') }}</span>
                        @endif
                    </div>
                </li>
                @endforeach
                @if(count($data['recentAlerts']) == 0)
                <li class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.recent_alerts.empty') }}</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Resumen Operacional --}}
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 bg-sky-500 px-4 py-3 text-white dark:border-gray-700">
            <h3 class="text-sm font-semibold"><i class="fa fa-info-circle"></i> {{ __('messages.dashboard.operational.heading') }}</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-3 gap-4 sm:grid-cols-6">
                <div class="flex flex-col items-center border-r border-gray-200 py-2 text-center dark:border-gray-700">
                    <i class="fa fa-bell mb-1 text-xl text-emerald-500"></i>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $data['operational']['alertsToday'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.operational.alerts_today') }}</div>
                </div>
                <div class="flex flex-col items-center border-r border-gray-200 py-2 text-center dark:border-gray-700">
                    <i class="fa fa-user-plus mb-1 text-xl text-sky-500"></i>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $data['operational']['usersToday'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.operational.new_users') }}</div>
                </div>
                <div class="flex flex-col items-center border-r border-gray-200 py-2 text-center dark:border-gray-700">
                    <i class="fa fa-database mb-1 text-xl text-blue-500"></i>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($data['operational']['recordsToday']) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.operational.records_today') }}</div>
                </div>
                <div class="flex flex-col items-center border-r border-gray-200 py-2 text-center dark:border-gray-700">
                    <i class="fa fa-check mb-1 text-xl text-emerald-500"></i>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $data['operational']['stationsOnline'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.operational.stations_online') }}</div>
                </div>
                <div class="flex flex-col items-center border-r border-gray-200 py-2 text-center dark:border-gray-700">
                    <i class="fa fa-exclamation mb-1 text-xl text-amber-500"></i>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $data['operational']['problemStations'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.operational.problem_stations') }}</div>
                </div>
                <div class="flex flex-col items-center py-2 text-center">
                    <i class="fa fa-percent mb-1 text-xl text-red-500"></i>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $data['dataQuality']['uptime'] }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.operational.data_coverage') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $dashboardI18n = [
        'stations' => __('messages.dashboard.chart_labels.stations'),
        'paid' => __('messages.dashboard.chart_labels.paid'),
        'pending' => __('messages.dashboard.chart_labels.pending'),
        'expired' => __('messages.dashboard.chart_labels.expired'),
        'courtesy' => __('messages.dashboard.chart_labels.courtesy'),
        'type_prefix' => __('messages.dashboard.chart_labels.type_prefix'),
        'cash' => __('messages.dashboard.chart_labels.cash'),
        'card' => __('messages.dashboard.chart_labels.card'),
        'qr' => __('messages.dashboard.chart_labels.qr'),
        'new_users' => __('messages.dashboard.chart_labels.new_users'),
        'paid_subscriptions' => __('messages.dashboard.chart_labels.paid_subscriptions'),
        'temperature' => __('messages.dashboard.chart_labels.temperature'),
        'rain' => __('messages.dashboard.chart_labels.rain'),
        'income' => __('messages.dashboard.chart_labels.income'),
    ];
@endphp

@push('js')
<script>
const dashboardI18n = @json($dashboardI18n);

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    const data = @json($data);

    const ctxDepartments = document.getElementById('chartDepartments').getContext('2d');
    new Chart(ctxDepartments, {
        type: 'bar',
        data: {
            labels: data.byDepartment.map(d => d.department),
            datasets: [{ label: dashboardI18n.stations, data: data.byDepartment.map(d => d.total), backgroundColor: 'rgba(54, 162, 235, 0.8)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    const ctxSubscriptions = document.getElementById('chartSubscriptions').getContext('2d');
    new Chart(ctxSubscriptions, {
        type: 'doughnut',
        data: {
            labels: [dashboardI18n.paid, dashboardI18n.pending, dashboardI18n.expired, dashboardI18n.courtesy],
            datasets: [{ data: [data.subscriptionStatus.paid, data.subscriptionStatus.pending, data.subscriptionStatus.expired, data.subscriptionStatus.courtesy], backgroundColor: ['rgba(40,167,69,0.8)', 'rgba(255,193,7,0.8)', 'rgba(220,53,69,0.8)', 'rgba(108,117,125,0.8)'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    const ctxUserTypes = document.getElementById('chartUserTypes').getContext('2d');
    new Chart(ctxUserTypes, {
        type: 'pie',
        data: {
            labels: data.userTypes.map(u => `${dashboardI18n.type_prefix} ${u.user_type || 'N/A'}`),
            datasets: [{ data: data.userTypes.map(u => u.total), backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    const ctxPaymentMethods = document.getElementById('chartPaymentMethods').getContext('2d');
    new Chart(ctxPaymentMethods, {
        type: 'doughnut',
        data: {
            labels: [dashboardI18n.cash, dashboardI18n.card, dashboardI18n.qr, dashboardI18n.courtesy],
            datasets: [{ data: [data.paymentMethods.efectivo, data.paymentMethods.tarjeta, data.paymentMethods.qr, data.paymentMethods.cortesia], backgroundColor: ['rgba(40,167,69,0.8)','rgba(54,162,235,0.8)','rgba(255,206,86,0.8)','rgba(153,102,255,0.8)'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    const ctxUserTrends = document.getElementById('chartUserTrends').getContext('2d');
    new Chart(ctxUserTrends, {
        type: 'line',
        data: {
            labels: data.userTrends.map(t => t.month),
            datasets: [
                { label: dashboardI18n.new_users, data: data.userTrends.map(t => t.new_users), borderColor: 'rgb(75,192,192)', tension: 0.1, fill: true, backgroundColor: 'rgba(75,192,192,0.2)' },
                { label: dashboardI18n.paid_subscriptions, data: data.userTrends.map(t => t.paid_subs), borderColor: 'rgb(255,99,132)', tension: 0.1, fill: true, backgroundColor: 'rgba(255,99,132,0.2)' }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    const ctxTemperature = document.getElementById('chartTemperature').getContext('2d');
    new Chart(ctxTemperature, {
        type: 'line',
        data: {
            labels: data.weatherTime.temperature.map(t => t.date),
            datasets: [{ label: dashboardI18n.temperature, data: data.weatherTime.temperature.map(t => t.avg_temp), borderColor: 'rgb(255,99,132)', tension: 0.1, fill: true, backgroundColor: 'rgba(255,99,132,0.2)' }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: false } } }
    });

    const ctxPrecipitation = document.getElementById('chartPrecipitation').getContext('2d');
    new Chart(ctxPrecipitation, {
        type: 'bar',
        data: {
            labels: data.weatherTime.precipitation.map(p => p.date),
            datasets: [{ label: dashboardI18n.rain, data: data.weatherTime.precipitation.map(p => p.total_rain), backgroundColor: 'rgba(54,162,235,0.8)', borderColor: 'rgba(54,162,235,1)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    const ctxIncome = document.getElementById('chartIncome').getContext('2d');
    new Chart(ctxIncome, {
        type: 'bar',
        data: {
            labels: data.incomeTrends.map(i => i.month),
            datasets: [{ label: dashboardI18n.income, data: data.incomeTrends.map(i => i.income), backgroundColor: 'rgba(40,167,69,0.8)', borderColor: 'rgba(40,167,69,1)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
}
</script>
@endpush

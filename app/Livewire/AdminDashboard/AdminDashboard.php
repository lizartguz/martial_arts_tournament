<?php

namespace App\Livewire\AdminDashboard;

use Livewire\Component;
use App\Models\StationM;
use App\Models\User;
use App\Models\UsersSubscriptionM;
use App\Models\SubscriptionM;
use App\Models\CurrentDataM;
use App\Models\DailyDataM;
use App\Models\AlertM;
use App\Models\ForecastM;
use App\Models\DepartmentM;
use App\Models\ProvinceM;
use App\Models\MunicipalityM;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public $data = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // 1. Métricas de Estaciones
        $this->data['stations'] = $this->getStationMetrics();
        
        // 2. Métricas de Usuarios
        $this->data['users'] = $this->getUserMetrics();
        
        // 3. Métricas de Suscripciones
        $this->data['subscriptions'] = $this->getSubscriptionMetrics();
        
        // 4. Métricas de Alertas
        $this->data['alerts'] = $this->getAlertMetrics();
        
        // 5. Métricas Climáticas
        $this->data['weather'] = $this->getWeatherMetrics();
        
        // 6. Métricas Financieras
        $this->data['financial'] = $this->getFinancialMetrics();
        
        // 7. Distribución por Departamento
        $this->data['byDepartment'] = $this->getDepartmentsData();
        
        // 8. TOP 10 Estaciones con más datos
        $this->data['topStations'] = $this->getTopStations();
        
        // 9. Alertas Recientes
        $this->data['recentAlerts'] = $this->getRecentAlerts();
        
        // 10. Tipos de Usuarios
        $this->data['userTypes'] = $this->getUserTypes();
        
        // 11. Métodos de Pago
        $this->data['paymentMethods'] = $this->getPaymentMethods();
        
        // 12. Estado de Estaciones
        $this->data['stationStatus'] = $this->getStationStatus();
        
        // 13. Estado de Suscripciones
        $this->data['subscriptionStatus'] = $this->getSubscriptionStatus();
        
        // 14. Tendencias de Usuarios
        $this->data['userTrends'] = $this->getUserTrends();
        
        // 15. Datos Climáticos Temporales
        $this->data['weatherTime'] = $this->getWeatherTimeData();
        
        // 16. Registros Diarios
        $this->data['dailyRecords'] = $this->getDailyRecords();
        
        // 17. Ingresos Temporales
        $this->data['incomeTrends'] = $this->getIncomeTrends();
        
        // 18. Calidad de Datos
        $this->data['dataQuality'] = $this->getDataQuality();
        
        // 19. Estaciones para Mapa
        $this->data['stationsMap'] = $this->getStationsForMap();
        
        // 20. Resumen Operacional
        $this->data['operational'] = $this->getOperationalSummary();
    }

    // Métricas de Estaciones
    private function getStationMetrics()
    {
        return [
            'total' => StationM::where('state', 1)->count(),
            'inactive' => StationM::where('state', 0)->count(),
            'newThisMonth' => StationM::where('state', 1)
                ->whereMonth('reg_date', now()->month)
                ->whereYear('reg_date', now()->year)
                ->count(),
            'totalCount' => StationM::count(),
        ];
    }

    // Métricas de Usuarios
    private function getUserMetrics()
    {
        return [
            'total' => User::where('state', 1)->count(),
            'newThisMonth' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'newThisWeek' => User::where('created_at', '>=', now()->subWeek())->count(),
            'totalCount' => User::count(),
        ];
    }

    // Métricas de Suscripciones
    private function getSubscriptionMetrics()
    {
        return [
            'active' => UsersSubscriptionM::where('state', 1)
                ->where('date_expiry', '>=', now())
                ->count(),
            'pending' => UsersSubscriptionM::where('is_it_paid_user', 0)
                ->where('payment_type_user', '!=', 4)
                ->where('state', 1)
                ->count(),
            'expired' => UsersSubscriptionM::where('date_expiry', '<', now())->count(),
            'courtesy' => UsersSubscriptionM::where('payment_type_user', 4)->count(),
            'renewalsThisMonth' => UsersSubscriptionM::whereMonth('reg_date', now()->month)
                ->whereYear('reg_date', now()->year)
                ->count(),
            'expiringSoon7' => UsersSubscriptionM::where('date_expiry', '>=', now())
                ->where('date_expiry', '<=', now()->addDays(7))
                ->where('state', 1)
                ->count(),
            'expiringSoon30' => UsersSubscriptionM::where('date_expiry', '>=', now())
                ->where('date_expiry', '<=', now()->addDays(30))
                ->where('state', 1)
                ->count(),
        ];
    }

    // Métricas de Alertas
    private function getAlertMetrics()
    {
        $sevenDaysAgo = now()->subDays(7);
        
        return [
            'total' => AlertM::where('state', 1)->count(),
            'last7Days' => AlertM::where('reg_date', '>=', $sevenDaysAgo)
                ->where('state', 1)
                ->count(),
            'critical' => AlertM::where('reg_date', '>=', $sevenDaysAgo)
                ->where('state', 1)
                ->where('f', '>=', 7)
                ->count(),
            'avgResolution' => $this->calculateAvgResolution(),
        ];
    }

    private function calculateAvgResolution()
    {
        // Implementar lógica de resolución promedio si aplica
        return '24h'; // Placeholder
    }

    // Métricas Climáticas
    private function getWeatherMetrics()
    {
        $data = CurrentDataM::select(
                DB::raw('AVG(tempout) as avg_temp'),
                DB::raw('AVG(humout) as avg_humidity'),
                DB::raw('AVG(raintotal) as avg_rain'),
                DB::raw('AVG(windspeed) as avg_wind'),
                DB::raw('AVG(uvindex) as avg_uv'),
                DB::raw('AVG(solrad) as avg_solar_rad')
            )
            ->where('receipt_date', '>=', now()->subDays(7))
            ->first();

        return [
            'avgTemperature' => $data->avg_temp ? round($data->avg_temp, 2) : 0,
            'avgHumidity' => $data->avg_humidity ? round($data->avg_humidity, 2) : 0,
            'totalRainfall' => $data->avg_rain ? round($data->avg_rain, 2) : 0,
            'avgWindSpeed' => $data->avg_wind ? round($data->avg_wind, 2) : 0,
            'avgUVIndex' => $data->avg_uv ? round($data->avg_uv, 2) : 0,
            'avgSolarRad' => $data->avg_solar_rad ? round($data->avg_solar_rad, 2) : 0,
        ];
    }

    // Métricas Financieras
    private function getFinancialMetrics()
    {
        $thisMonth = now()->month;
        $thisYear = now()->year;
        
        $income = UsersSubscriptionM::whereMonth('reg_date', $thisMonth)
            ->whereYear('reg_date', $thisYear)
            ->where('is_it_paid_user', 1)
            ->sum('cost');

        $incomeYear = UsersSubscriptionM::whereYear('reg_date', $thisYear)
            ->where('is_it_paid_user', 1)
            ->sum('cost');

        $lastMonth = now()->subMonth();
        $incomeLastMonth = UsersSubscriptionM::whereMonth('reg_date', $lastMonth->month)
            ->whereYear('reg_date', $lastMonth->year)
            ->where('is_it_paid_user', 1)
            ->sum('cost');

        return [
            'incomeThisMonth' => round($income, 2),
            'incomeThisYear' => round($incomeYear, 2),
            'incomeLastMonth' => round($incomeLastMonth, 2),
            'arpu' => $this->calculateARPU(),
            'conversionRate' => $this->calculateConversionRate(),
        ];
    }

    private function calculateARPU()
    {
        $totalUsers = UsersSubscriptionM::where('state', 1)
            ->where('date_expiry', '>=', now())
            ->count();
        
        $income = UsersSubscriptionM::whereYear('reg_date', now()->year)
            ->where('is_it_paid_user', 1)
            ->sum('cost');
        
        return $totalUsers > 0 ? round($income / $totalUsers, 2) : 0;
    }

    private function calculateConversionRate()
    {
        $totalDemo = UsersSubscriptionM::whereHas('subscription', function($q) {
            $q->where('tag', 'like', '%demo%');
        })->count();

        $converted = UsersSubscriptionM::whereHas('subscription', function($q) {
            $q->where('tag', 'not like', '%demo%');
        })->where('is_it_paid_user', 1)->count();

        return $totalDemo > 0 ? round(($converted / $totalDemo) * 100, 2) : 0;
    }

    // Distribución por Departamento
    private function getDepartmentsData()
    {
        try {
            return DB::table('stations as s')
                ->join('municipalities as m', 's.municipality_id', '=', 'm.id')
                ->join('provinces as p', 'm.province_id', '=', 'p.id')
                ->join('departments as d', 'p.department_id', '=', 'd.id')
                ->select('d.name as department', DB::raw('count(*) as total'))
                ->where('s.state', 1)
                ->groupBy('d.name')
                ->get();
        } catch (\Exception $e) {
            // Si falla, devolver datos vacíos
            return collect([]);
        }
    }

    // TOP 10 Estaciones
    private function getTopStations()
    {
        return CurrentDataM::select(
                'stations.id',
                'stations.name',
                'stations.location',
                'stations.state',
                DB::raw('count(*) as total_records')
            )
            ->join('stations', 'current_data.station_id', '=', 'stations.id')
            ->groupBy('station_id', 'stations.id', 'stations.name', 'stations.location', 'stations.state')
            ->orderBy('total_records', 'desc')
            ->limit(10)
            ->get();
    }

    // Alertas Recientes
    private function getRecentAlerts()
    {
        return AlertM::select('alerts.*', 'stations.name as station_name')
            ->join('stations', 'alerts.station_id', '=', 'stations.id')
            ->where('alerts.state', 1)
            ->orderBy('alerts.reg_date', 'desc')
            ->limit(10)
            ->get();
    }

    // Tipos de Usuarios
    private function getUserTypes()
    {
        return User::select('user_type', DB::raw('count(*) as total'))
            ->where('state', 1)
            ->groupBy('user_type')
            ->get();
    }

    // Métodos de Pago
    private function getPaymentMethods()
    {
        $methods = UsersSubscriptionM::select(
            DB::raw('SUM(CASE WHEN payment_type_user = 1 OR payment_type_renewal = 1 THEN 1 ELSE 0 END) as efectivo'),
            DB::raw('SUM(CASE WHEN payment_type_user = 2 OR payment_type_renewal = 2 THEN 1 ELSE 0 END) as tarjeta'),
            DB::raw('SUM(CASE WHEN payment_type_user = 3 OR payment_type_renewal = 3 THEN 1 ELSE 0 END) as qr'),
            DB::raw('SUM(CASE WHEN payment_type_user = 4 OR payment_type_renewal = 4 THEN 1 ELSE 0 END) as cortesia')
        )->first();

        return [
            'efectivo' => $methods->efectivo ?? 0,
            'tarjeta' => $methods->tarjeta ?? 0,
            'qr' => $methods->qr ?? 0,
            'cortesia' => $methods->cortesia ?? 0,
        ];
    }

    // Estado de Estaciones
    private function getStationStatus()
    {
        return [
            'active' => StationM::where('state', 1)->count(),
            'inactive' => StationM::where('state', 0)->count(),
            'withData' => CurrentDataM::distinct('station_id')->count(),
            'withoutData' => StationM::whereNotIn('id', 
                CurrentDataM::distinct('station_id')->pluck('station_id')
            )->count(),
        ];
    }

    // Estado de Suscripciones
    private function getSubscriptionStatus()
    {
        return [
            'paid' => UsersSubscriptionM::where('is_it_paid_user', 1)->count(),
            'pending' => UsersSubscriptionM::where('is_it_paid_user', 0)
                ->where('payment_type_user', '!=', 4)
                ->count(),
            'expired' => UsersSubscriptionM::where('date_expiry', '<', now())->count(),
            'courtesy' => UsersSubscriptionM::where('payment_type_user', 4)->count(),
        ];
    }

    // Tendencias de Usuarios
    private function getUserTrends()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'month' => $date->locale(app()->getLocale())->translatedFormat('M Y'),
                'new_users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'paid_subs' => UsersSubscriptionM::whereMonth('reg_date', $date->month)
                    ->whereYear('reg_date', $date->year)
                    ->where('is_it_paid_user', 1)
                    ->count(),
            ];
        }
        return $months;
    }

    // Datos Climáticos Temporales
    private function getWeatherTimeData()
    {
        $days = 30;
        return [
            'temperature' => CurrentDataM::select(
                    DB::raw('DATE(receipt_date) as date'),
                    DB::raw('AVG(tempout) as avg_temp')
                )
                ->where('receipt_date', '>=', now()->subDays($days))
                ->groupBy(DB::raw('DATE(receipt_date)'))
                ->orderBy('date')
                ->get(),
            'precipitation' => CurrentDataM::select(
                    DB::raw('DATE(receipt_date) as date'),
                    DB::raw('SUM(raintotal) as total_rain')
                )
                ->where('receipt_date', '>=', now()->subDays($days))
                ->groupBy(DB::raw('DATE(receipt_date)'))
                ->orderBy('date')
                ->get(),
        ];
    }

    // Registros Diarios
    private function getDailyRecords()
    {
        return CurrentDataM::select(
                DB::raw('DATE(receipt_date) as date'),
                DB::raw('count(*) as total_records'),
                DB::raw('count(DISTINCT station_id) as stations')
            )
            ->where('receipt_date', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(receipt_date)'))
            ->orderBy('date')
            ->get();
    }

    // Ingresos Temporales
    private function getIncomeTrends()
    {
        $months = [];
        for ($amount = 5; $amount >= 0; $amount--) {
            $date = now()->subMonths($amount);
            $monthIncome = UsersSubscriptionM::whereMonth('reg_date', $date->month)
                ->whereYear('reg_date', $date->year)
                ->where('is_it_paid_user', 1)
                ->sum('cost');
            
            $months[] = [
                'month' => $date->locale(app()->getLocale())->translatedFormat('M'),
                'income' => round($monthIncome, 2),
            ];
        }
        return $months;
    }

    // Calidad de Datos
    private function getDataQuality()
    {
        $totalStations = StationM::where('state', 1)->count();
        $stationsWithData = CurrentDataM::distinct('station_id')->count();
        
        $expectedRecords = $totalStations * 144; // 144 registros por día (10 min interval)
        
        $actualRecords = CurrentDataM::where('receipt_date', '>=', now()->subDay())
            ->count();
        
        return [
            'uptime' => $totalStations > 0 ? round(($stationsWithData / $totalStations) * 100, 2) : 0,
            'dataCoverage' => $expectedRecords > 0 ? round(($actualRecords / $expectedRecords) * 100, 2) : 0,
            'stationsWithData' => $stationsWithData,
            'missingStations' => $totalStations - $stationsWithData,
        ];
    }

    // Estaciones para Mapa
    private function getStationsForMap()
    {
        return StationM::select(
                'stations.id',
                'stations.name',
                'stations.location',
                'stations.latitude',
                'stations.longitude',
                'stations.state',
                'cd.tempout',
                'cd.humout',
                'cd.receipt_date'
            )
            ->leftJoin(DB::raw('(SELECT station_id, tempout, humout, receipt_date FROM current_data ORDER BY receipt_date DESC) as cd'), 
                'stations.id', '=', 'cd.station_id')
            ->where('stations.state', 1)
            ->get()
            ->unique('id');
    }

    // Resumen Operacional
    private function getOperationalSummary()
    {
        $today = now()->startOfDay();
        
        return [
            'alertsToday' => AlertM::whereDate('reg_date', $today)->count(),
            'usersToday' => User::whereDate('created_at', $today)->count(),
            'recordsToday' => CurrentDataM::whereDate('receipt_date', $today)->count(),
            'stationsOnline' => CurrentDataM::whereDate('receipt_date', $today)
                ->distinct('station_id')
                ->count(),
            'problemStations' => StationM::where('state', 1)
                ->whereNotIn('id', 
                    CurrentDataM::whereDate('receipt_date', $today)->pluck('station_id')
                )->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin-dashboard.admin-dashboard');
    }
}

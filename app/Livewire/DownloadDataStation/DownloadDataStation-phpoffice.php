<?php

namespace App\Livewire\DownloadDataStation;

use App\Exports\DataStationExport;
use App\Models\StationM;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DownloadDataStation extends Component
{
    public $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    public $stationList = [];
    public $selectedStationId = null;
    public $format = 'daily';
    public $data = [];
    public $fromDate;
    public $toDate;
    public $lastDayMonth;
    public $lastYear;
    public $month;
    public $year;
    public $isDownload = false;
    public $noData = false;
    protected $listeners = ['showData', 'downloadData'];

    protected $downloadData = [];

    public function mount() {
        date_default_timezone_set('America/La_Paz');
        $this->isDownload = false;
        $this->noData = false;
        $this->downloadData = [];
        $this->fromDate = Carbon::now()->format('Y-m-d');
        $this->toDate = Carbon::now()->format('Y-m-d');
        $this->month = Carbon::now()->format('m');
        $this->year = Carbon::now()->format('Y');
        $this->lastYear = Carbon::now()->format('Y');
        $this->lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        $this->stationList = StationM::select('stations.id', 'stations.name')
            ->where('stations.state',1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.download-data-station.download-data-station-phpoffice');
    }

    public function clearData() {
        $this->data = [];
        $this->downloadData = [];
        $this->noData = true;
    }

    public function showData($stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null) {
        $this->isDownload = false;
        $this->clearData();
        $this->selectedStationId = $stationId;
        $this->format = $format;
        if ($fromDate != null) {
            $this->fromDate = $fromDate;
        }
        if ($toDate != null) {
            $this->toDate = $toDate;
        }
        if ($year != null) {
            $this->year = $year;
        }
        if ($month != null) {
            $this->month = $month + 1; // el select del mes esta desde 0 a 11, tenemos que sumar 1
            $this->lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        }
        $this->lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        if ($this->format == 'daily') { //todos los registros
            $this->data = $this->getDailyData(200);
        } else if ($this->format == 'monthly') { //resumen diario
            $this->data = $this->getMonthlyData(200);
        } else if ($this->format == 'annual') { //resumen anual
            $this->data = $this->getAnnualData(200);
        } else { //no hace nada
        }
        if (count($this->data)) {
            $this->noData = false;
        }
    }

    public function getDailyData($limit = 0) {
        $fromDate = Carbon::parse($this->fromDate . '00:00:00');
        $toDate = Carbon::parse($this->toDate . '23:59:59'); //colocamos la fecha final correcta
        $year = $fromDate->format('Y');
        
        // Conexión antigua (comentado)
        // $query = DB::connection('db_guest_h'.$year)
        //     ->table('data')
        
        // Nueva conexión
        $query = DB::connection('senvatec_db_'.$year)
            ->table('senva_data')
            ->select('receipt_date', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout',
                'humout', 'winddir', 'winddirstr', 'windspeed', 'windspeedhi', 'raintotal', 'rainrate', 'press', 'heatindexout',
                'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1')
            ->where('station_id', $this->selectedStationId)
            ->where('receipt_date', '>=', $fromDate)
            ->where('receipt_date', '<=', $toDate)
            ->orderBy('receipt_date', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->get()
            ->map(function ($item) {
                return [
                    'receipt_date' => $item->receipt_date,
                    'tempout' => round($item->tempout, 2),
                    'tempoutmax' => round($item->tempoutmax, 2),
                    'tempoutmin' => round($item->tempoutmin, 2),
                    'dewptout' => round($item->dewptout, 2),
                    'humout' => round($item->humout, 2),
                    'winddir' => round($item->winddir, 2),
                    'winddirstr' => Utils::windDirToStr($item->winddir, $item->windspeed),
                    'windspeed' => round($item->windspeed, 2),
                    'windspeedhi' => round($item->windspeedhi, 2),
                    'raintotal' => round($item->raintotal, 2),
                    'rainrate' => round($item->rainrate, 2),
                    'press' => round($item->press, 2),
                    'wetbulb' => 'bulbo',
                    'heatindexout' => round($item->heatindexout, 2),
                    'uvindex' => round($item->uvindex, 2),
                    'solrad' => round($item->solrad, 2),
                    'solevo' => round($item->solevo, 2),
                    'soiltemp1' => round($item->soiltemp1, 2),
                    'soilhum1' => round($item->soilhum1, 2),
                    'leaftemp1' => round($item->leaftemp1, 2),
                    'leafhum1' => round($item->leafhum1, 2),
                ];
            })
            ->toArray();
    }

    public function getMonthlyData($limit = 0) {
        // Conexión antigua (comentado)
        // $query = DB::connection('db_guest_h'.$this->year)
        //     ->table('daily_data')
        
        // Nueva conexión
        $query = DB::connection('senvatec_db_'.$this->year)
            ->table('senva_daily_data')
            ->select('f_day', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout',
                'humout', 'winddirstr', 'windspeed', 'windspeedhi', 'raintotal', 'press', 'heatindexout',
                'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1')
            ->where('station_id', $this->selectedStationId)
            ->where('f_year', $this->year)
            ->where('f_month', $this->month)
            ->orderBy('f_day', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return collect($query->get()
            ->map(function ($item) {
                return [
                    'f_day' => $item->f_day,
                    'tempout' => round($item->tempout, 2),
                    'tempoutmax' => round($item->tempoutmax, 2),
                    'tempoutmin' => round($item->tempoutmin, 2),
                    'dewptout' => round($item->dewptout, 2),
                    'humout' => round($item->humout, 2),
                    'winddirstr' => $item->winddirstr,
                    'windspeed' => round($item->windspeed, 2),
                    'windspeedhi' => round($item->windspeedhi, 2),
                    'raintotal' => round($item->raintotal, 2),
                    'press' => round($item->press, 2),
                    'wetbulb' => 'bulbo',
                    'heatindexout' => round($item->heatindexout, 2),
                    'uvindex' => round($item->uvindex, 2),
                    'solrad' => round($item->solrad, 2),
                    'solevo' => round($item->solevo, 2),
                    'soiltemp1' => round($item->soiltemp1, 2),
                    'soilhum1' => round($item->soilhum1, 2),
                    'leaftemp1' => round($item->leaftemp1, 2),
                    'leafhum1' => round($item->leafhum1, 2),
                ];
            })
            ->toArray())->keyBy('f_day');
    }

    public function getAnnualData($limit = 0) {
        // Conexión antigua (comentado)
        // $query = DB::connection('db_guest_h'.$this->year)
        //     ->table('daily_data')
        
        // Nueva conexión
        $query = DB::connection('senvatec_db_'.$this->year)
            ->table('senva_daily_data')
            ->selectRaw('
                f_month,
                avg(tempout) as tempout,
                max(tempoutmax) as tempoutmax,
                min(tempoutmin) as tempoutmin,
                avg(dewptout) as dewptout,
                avg(humout) as humout,
                max(winddirstr) as winddirstr,
                avg(windspeed) as windspeed,
                max(windspeedhi) as windspeedhi,
                max(raintotal) as raintotal,
                avg(press) as press,
                avg(heatindexout) as heatindexout,
                avg(uvindex) as uvindex,
                avg(solrad) as solrad,
                avg(solevo) as solevo,
                avg(soiltemp1) as soiltemp1,
                avg(soilhum1) as soilhum1,
                avg(leaftemp1) as leaftemp1,
                avg(leafhum1) as leafhum1
            ')
            ->where('station_id', $this->selectedStationId)
            ->where('f_year', $this->year)
            ->groupBy('f_month')
            ->orderBy('f_month', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return collect($query->get()
            ->map(function ($item) {
                return [
                    'f_month' => $item->f_month,
                    'tempout' => round($item->tempout, 2),
                    'tempoutmax' => round($item->tempoutmax, 2),
                    'tempoutmin' => round($item->tempoutmin, 2),
                    'dewptout' => round($item->dewptout, 2),
                    'humout' => round($item->humout, 2),
                    'winddirstr' => $item->winddirstr,
                    'windspeed' => round($item->windspeed, 2),
                    'windspeedhi' => round($item->windspeedhi, 2),
                    'raintotal' => round($item->raintotal, 2),
                    'press' => round($item->press, 2),
                    'wetbulb' => 'bulbo',
                    'heatindexout' => round($item->heatindexout, 2),
                    'uvindex' => round($item->uvindex, 2),
                    'solrad' => round($item->solrad, 2),
                    'solevo' => round($item->solevo, 2),
                    'soiltemp1' => round($item->soiltemp1, 2),
                    'soilhum1' => round($item->soilhum1, 2),
                    'leaftemp1' => round($item->leaftemp1, 2),
                    'leafhum1' => round($item->leafhum1, 2),
                ];
            })
            ->toArray())->keyBy('f_month');
    }

    public function downloadData($stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null) {
        $this->clearData();
        $this->isDownload = true;
        $this->selectedStationId = $stationId;
        $this->format = $format;
        if ($fromDate != null) {
            $this->fromDate = $fromDate;
        }
        if ($toDate != null) {
            $this->toDate = $toDate;
        }
        if ($year != null) {
            $this->year = $year;
        }
        if ($month != null) {
            $this->month = $month + 1; // el select del mes esta desde 0 a 11, tenemos que sumar 1
        }
        $this->lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        if ($this->format == 'daily') { //todos los registros
            $this->downloadData = $this->getDailyData(0);
        } else if ($this->format == 'monthly') { //resumen diario
            $this->downloadData = $this->getMonthlyData(0);
        } else if ($this->format == 'annual') { //resumen anual
            $this->downloadData = $this->getAnnualData(0);
        } else { //no hace nada
        }
        if (count($this->downloadData)) {
            $this->noData = false;
            $dataExported = new DataStationExport($this->selectedStationId, $this->format,  $this->fromDate, $this->toDate, $this->year, $this->month, $this->downloadData);
            $this->downloadData = [];
            return Excel::download($dataExported, 'download.xlsx');
        }
    }
}

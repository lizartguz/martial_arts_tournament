<?php

namespace App\Exports;

use App\Models\StationM;
use App\Utils\Utils;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;

class DataStationExport
{
    protected $monthList = [];
    protected $station;
    protected $format;
    protected $fromDate;
    protected $toDate;
    protected $year;
    protected $month;
    protected $heads;
    protected $filename;
    protected $writer;
    protected $options;
    protected $measureSystem = 'metric';
    protected $typeS;
    protected array $visibleColumns = [];

    /**
     * Prepara el escritor y genera el archivo segun el formato solicitado.
     */
    public function __construct($filename, $stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null, $heads = [], $measureSystem = 'metric', $typeS = null, array $visibleColumns = [])
    {
        $this->monthList = [
            1 => __('messages.months.january'),
            2 => __('messages.months.february'),
            3 => __('messages.months.march'),
            4 => __('messages.months.april'),
            5 => __('messages.months.may'),
            6 => __('messages.months.june'),
            7 => __('messages.months.july'),
            8 => __('messages.months.august'),
            9 => __('messages.months.september'),
            10 => __('messages.months.october'),
            11 => __('messages.months.november'),
            12 => __('messages.months.december'),
        ];
        $this->station = StationM::find($stationId);
        $this->format = $format;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->year = $year;
        $this->month = $month;
        $this->heads = $heads;
        $this->filename = $filename;
        $this->measureSystem = $measureSystem;
        $this->typeS = $typeS;
        $this->visibleColumns = $this->normalizeVisibleColumns($visibleColumns);
        $this->options = new Options();
        $this->writer = new Writer($this->options);
        $this->writer->openToBrowser($this->filename);
        if ($this->format == 'daily') {
            $this->fillDailyData();
        } else if ($this->format == 'monthly') {
            $this->fillMonthlyData();
        } else if ($this->format == 'annual') {
            $this->fillAnnualData();
        }
    }

    /**
     * Llena el reporte diario respetando las columnas visibles.
     */
    public function fillDailyData() {
        set_time_limit(1800);  //ampliamos el limite de espera
        date_default_timezone_set('America/La_Paz');

        $fromDateFormatted = Carbon::parse($this->fromDate)->format('d/m/Y');
        $toDateFormatted = Carbon::parse($this->toDate)->format('d/m/Y');
        $currentDate = date('d/m/Y G:i:s');

        $lastColumn = count($this->heads) - 1;
        $border = new Border(
            new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
        );

        $style = (new Style())->setFontBold()->setFontSize(25)->setCellAlignment(CellAlignment::CENTER)->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        $this->writer->addRow(Row::fromValues([__('messages.downloads.file_report_title_daily')], $style));
        $this->options->mergeCells(0, 1, $lastColumn, 1, $this->writer->getCurrentSheet()->getIndex());
        $this->options->setColumnWidth(20, 1);

        $this->writer->addRow(Row::fromValues([__('messages.downloads.file_report_station'), '', '', '', __('messages.downloads.file_report_date_emit') . $currentDate, '', '', '', '', '', '', __('messages.downloads.file_report_latitude') . $this->station->latitude]));
        $this->writer->addRow(Row::fromValues([$this->station->name, '', '', '', __('messages.downloads.file_report_date_from') . $fromDateFormatted . ' - ' . __('messages.downloads.file_report_date_to') . $toDateFormatted, '', '', '', '', '', '', __('messages.downloads.file_report_longitude') . $this->station->longitude]));

        $styleHeads = (new Style())->setFontBold()->setCellAlignment(CellAlignment::CENTER)->setCellVerticalAlignment(CellVerticalAlignment::CENTER)->setTextRotation(90);
        $styleHeads->setBorder($border);
        $this->writer->addRow(Row::fromValues($this->heads, $styleHeads));

        //realizamos la consulta
        $fromDate = Carbon::parse($this->fromDate . '00:00:00');
        $toDate = Carbon::parse($this->toDate . '23:59:59'); //colocamos la fecha final correcta
        $year = $fromDate->format('Y');
        $arrayData = [];
        
        if($this->typeS == "qair"){
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$year)
            // ->table('data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$year)
            ->table('senva_data')
            ->select('receipt_date', 'tempout','humout','pm1', 'pm2_5', 'pm10')
            ->where('station_id', $this->station->id)
            ->where('receipt_date', '>=', $fromDate)
            ->where('receipt_date', '<=', $toDate)
            ->orderBy('receipt_date', 'asc');
            $i = 0;
            foreach ($query->cursor() as $item) {  //utilizamos cursor para optimizar los registros obtenidos
                
                
                $this->writer->addRow(Row::fromValues([
                    Carbon::parse($item->receipt_date)->format('d/m/Y H:i:s'),
                    $item->tempout != null ? round(Utils::convertTemp($item->tempout, $this->measureSystem), 1) : '',
                    $item->humout != null ? round($item->humout, 0) : '',                   
                    $item->pm1 != null ? round($item->pm1, 1) : '',
                    $item->pm2_5 != null ? round($item->pm2_5, 1) : '',
                    $item->pm10 != null ? round($item->pm10, 1) : ''
                ]));

                if ($i % 1000 === 0) {
                    flush(); // vaciamos el buffer cada 1000 filas
                }
            };
        }else{
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$year)
            // ->table('data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$year)
            ->table('senva_data')
            ->select('receipt_date', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout', 'humout',
                'winddir', 'winddirstr', 'windspeed', 'windspeedhi',
                'winddir2', 'windspeed2', 'windspeedhi2',
                'raintotal', 'rainrate', 'press', 'heatindexout',
                'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1')
            ->where('station_id', $this->station->id)
            ->where('receipt_date', '>=', $fromDate)
            ->where('receipt_date', '<=', $toDate)
            ->orderBy('receipt_date', 'asc');
            $i = 0;
            foreach ($query->cursor() as $item) {  //utilizamos cursor para optimizar los registros obtenidos
                $bulbo = null;
                try{
                    $e= (6.11 * pow(10,(7.5 *round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1)))));
                    $resultado = (((0.00066 * round(Utils::convertPress($item->press, $this->measureSystem), 1)) *  round(Utils::convertTemp($item->tempout, $this->measureSystem), 1)) + ((4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) + 237.7) , 2) * round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $this->measureSystem), 1) ) + (4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) + 237.7) , 2));
                
                $bulbo = round($resultado,2);
                } catch (\Exception $e) {}
                
                $this->writer->addRow(Row::fromValues($this->filterExportValues($this->dailyColumnKeys(), [
                    Carbon::parse($item->receipt_date)->format('d/m/Y H:i:s'),
                    $item->tempout != null ? round(Utils::convertTemp($item->tempout, $this->measureSystem), 1) : '',
                    $item->tempoutmax != null ? round(Utils::convertTemp($item->tempoutmax, $this->measureSystem), 1) : '',
                    $item->tempoutmin != null ? round(Utils::convertTemp($item->tempoutmin, $this->measureSystem), 1): '',
                    $item->dewptout != null ? round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) : '',
                    $item->humout != null ? round($item->humout, 0) : '',
                    $item->winddir != null ? round($item->winddir, 1) : '',
                    Utils::windDirToStr($item->winddir, $item->windspeed),
                    $item->windspeed != null ? round(Utils::convertSpeed($item->windspeed, $this->measureSystem), 0) : '',
                    $item->windspeedhi != null ? round(Utils::convertSpeed($item->windspeedhi, $this->measureSystem), 1) : '',
                    $item->winddir2 != null ? round($item->winddir2, 1) : '',
                    $item->winddir2 != null ? Utils::windDirToStr($item->winddir2, $item->windspeed2) : '',
                    $item->windspeed2 != null ? round(Utils::convertSpeed($item->windspeed2, $this->measureSystem), 0) : '',
                    $item->windspeedhi2 != null ? round(Utils::convertSpeed($item->windspeedhi2, $this->measureSystem), 1) : '',
                    $item->raintotal != null ? round(Utils::convertLength($item->raintotal, $this->measureSystem), 3) : '',
                    $item->rainrate != null ? round(Utils::convertRainfall($item->rainrate, $this->measureSystem), 3) : '',
                    $item->press != null ? round(Utils::convertPress($item->press, $this->measureSystem), 1) : '',
                    $bulbo!=null?(string)$bulbo:'',
                    $item->heatindexout != null ? round(Utils::convertTemp($item->heatindexout, $this->measureSystem), 1) : '',
                    $item->uvindex != null ? round($item->uvindex, 1) : '',
                    $item->solrad != null ? round(Utils::convertPower($item->solrad, $this->measureSystem), 2) : '',
                    $item->solevo != null ? round(Utils::convertLength($item->solevo, $this->measureSystem), 3) : '',
                    $item->soiltemp1 != null ? round(Utils::convertLength($item->soiltemp1, $this->measureSystem), 1) : '',
                    $item->soilhum1 != null ? round($item->soilhum1, 1) : '',
                    $item->leaftemp1 != null ? round(Utils::convertLength($item->leaftemp1, $this->measureSystem), 1) : '',
                    $item->leafhum1 != null ? round($item->leafhum1, 1) : ''
                ])));

                if ($i % 1000 === 0) {
                    flush(); // vaciamos el buffer cada 1000 filas
                }
            };
        }
        


    }

    /**
     * Llena el reporte mensual respetando las columnas visibles.
     */
    public function fillMonthlyData() {
        set_time_limit(1800);  //ampliamos el limite de espera
        date_default_timezone_set('America/La_Paz');
        $fromDateFormatted = Carbon::create($this->year, $this->month, 1)->startOfMonth()->format('d/m/Y');
        $toDateFormatted = Carbon::create($this->year, $this->month, 1)->endOfMonth()->format('d/m/Y');
        $currentDate = date('d/m/Y G:i:s');

        $lastColumn = count($this->heads) - 1;
        $border = new Border(
            new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
        );

        $style = (new Style())->setFontBold()->setFontSize(25)->setCellAlignment(CellAlignment::CENTER)->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
        $this->writer->addRow(Row::fromValues([__('messages.downloads.file_report_title_monthly')], $style));
        $this->options->mergeCells(0, 1, $lastColumn, 1, $this->writer->getCurrentSheet()->getIndex());

        $this->writer->addRow(Row::fromValues([__('messages.downloads.file_report_station'), '', '', '', __('messages.downloads.file_report_date_emit') . $currentDate, '', '', '', '', '', '', __('messages.downloads.file_report_latitude') . $this->station->latitude]));
        $this->writer->addRow(Row::fromValues([$this->station->name, '', '', '', __('messages.downloads.file_report_date_from') . $fromDateFormatted . ' - ' . __('messages.downloads.file_report_date_to') . $toDateFormatted, '', '', '', '', '', '', __('messages.downloads.file_report_longitude') . $this->station->longitude]));

        $styleHeads = (new Style())->setFontBold()->setCellAlignment(CellAlignment::CENTER)->setCellVerticalAlignment(CellVerticalAlignment::CENTER)->setTextRotation(90);
        $styleHeads->setBorder($border);
        $this->writer->addRow(Row::fromValues($this->heads, $styleHeads));

        //realizamos la consulta
        $fromDate = Carbon::parse($this->fromDate . '00:00:00');
        $toDate = Carbon::parse($this->toDate . '23:59:59'); //colocamos la fecha final correcta
        $year = $fromDate->format('Y');
        $arrayData = [];
        
        if($this->typeS == "qair"){
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->year)
            //     ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->year)
                ->table('senva_daily_data')
                ->select('f_day','tempout','humout','pm1', 'pm2_5', 'pm10')
                ->where('station_id', $this->station->id)
                ->where('f_year', $this->year)
                ->where('f_month', $this->month)
                ->orderBy('f_day', 'asc');
            $i = 0;
            foreach ($query->cursor() as $item) {  //utilizamos cursor para optimizar los registros obtenidos

                $arrayData[$item->f_day] = [
                    $item->f_day,
                    $item->tempout != null ? round(Utils::convertTemp($item->tempout, $this->measureSystem), 1) : '',
                    $item->humout != null ? round($item->humout, 1) : '',
                    $item->pm1 != null ? round($item->pm1, 1) : '',
                    $item->pm2_5 != null ? round($item->pm2_5, 1) : '',
                    $item->pm10 != null ? round($item->pm10, 1) : '',
                ];
                if ($i % 1000 === 0) {
                    flush(); // vaciamos el buffer cada 1000 filas
                }
            };
            $lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
            for ($day=1 ; $day<=$lastDayMonth ; $day++ ) {
                if (isset($arrayData[$day])) {
                    $this->writer->addRow(Row::fromValues($arrayData[$day]));
                } else {
                    $this->writer->addRow(Row::fromValues([$day, '', '', '', '', '']));
                }
            }
        }else{
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->year)
            //     ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->year)
                ->table('senva_daily_data')
                ->select('f_day', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout', 'humout',
                    'winddirstr', 'windspeed', 'windspeedhi',
                    'winddirstr2', 'windspeed2', 'windspeedhi2',
                    'raintotal', 'press', 'heatindexout',
                    'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1')
                ->where('station_id', $this->station->id)
                ->where('f_year', $this->year)
                ->where('f_month', $this->month)
                ->orderBy('f_day', 'asc');
            $i = 0;
            foreach ($query->cursor() as $item) {  //utilizamos cursor para optimizar los registros obtenidos

                $bulbo = null;
                try{
                    $e= (6.11 * pow(10,(7.5 *round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1)))));
                    $resultado = (((0.00066 * round(Utils::convertPress($item->press, $this->measureSystem), 1)) *  round(Utils::convertTemp($item->tempout, $this->measureSystem), 1)) + ((4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) + 237.7) , 2) * round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $this->measureSystem), 1) ) + (4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) + 237.7) , 2));
                
                $bulbo = round($resultado,2);
                } catch (\Exception $e) {}

                $arrayData[$item->f_day] = $this->filterExportValues($this->monthlyColumnKeys(), [
                    $item->f_day,
                    $item->tempout != null ? round(Utils::convertTemp($item->tempout, $this->measureSystem), 1) : '',
                    $item->tempoutmax != null ? round(Utils::convertTemp($item->tempoutmax, $this->measureSystem), 1) : '',
                    $item->tempoutmin != null ? round(Utils::convertTemp($item->tempoutmin, $this->measureSystem), 1): '',
                    $item->dewptout != null ? round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) : '',
                    $item->humout != null ? round($item->humout, 1) : '',
                    $item->winddirstr != null ?  $item->winddirstr : '',
                    $item->windspeed != null ? round(Utils::convertSpeed($item->windspeed, $this->measureSystem), 0) : '',
                    $item->windspeedhi != null ? round(Utils::convertSpeed($item->windspeedhi, $this->measureSystem), 1) : '',
                    $item->winddirstr2 != null ? $item->winddirstr2 : '',
                    $item->windspeed2 != null ? round(Utils::convertSpeed($item->windspeed2, $this->measureSystem), 0) : '',
                    $item->windspeedhi2 != null ? round(Utils::convertSpeed($item->windspeedhi2, $this->measureSystem), 1) : '',
                    $item->raintotal != null ? round(Utils::convertLength($item->raintotal, $this->measureSystem), 3) : '',
                    $item->press != null ? round(Utils::convertPress($item->press, $this->measureSystem), 1) : '',
                    $bulbo!=null?(string)$bulbo:'',
                    $item->heatindexout != null ? round(Utils::convertTemp($item->heatindexout, $this->measureSystem), 1) : '',
                    $item->uvindex != null ? round($item->uvindex, 1) : '',
                    $item->solrad != null ? round(Utils::convertPower($item->solrad, $this->measureSystem), 1) : '',
                    $item->solevo != null ? round(Utils::convertLength($item->solevo, $this->measureSystem), 3) : '',
                    $item->soiltemp1 != null ? round(Utils::convertLength($item->soiltemp1, $this->measureSystem), 1) : '',
                    $item->soilhum1 != null ? round($item->soilhum1, 1) : '',
                    $item->leaftemp1 != null ? round(Utils::convertLength($item->leaftemp1, $this->measureSystem), 1) : '',
                    $item->leafhum1 != null ? round($item->leafhum1, 1) : ''
                ]);
                if ($i % 1000 === 0) {
                    flush(); // vaciamos el buffer cada 1000 filas
                }
            };
            $lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
            for ($day=1 ; $day<=$lastDayMonth ; $day++ ) {
                if (isset($arrayData[$day])) {
                    $this->writer->addRow(Row::fromValues($arrayData[$day]));
                } else {
                    $this->writer->addRow(Row::fromValues($this->filterExportValues($this->monthlyColumnKeys(), [$day, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''])));
                }
            }
        }
    }

    /**
     * Llena el reporte anual respetando las columnas visibles.
     */
    public function fillAnnualData() {
        set_time_limit(1800);  //ampliamos el limite de espera
        date_default_timezone_set('America/La_Paz');
        $fromDateFormatted = Carbon::create($this->year, 1, 1)->startOfMonth()->format('d/m/Y');
        $toDateFormatted = Carbon::create($this->year, 12, 1)->endOfMonth()->format('d/m/Y');
        $currentDate = date('d/m/Y G:i:s');

        $lastColumn = count($this->heads) - 1;
        $border = new Border(
            new BorderPart(Border::LEFT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::TOP, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID),
        );

        $style = (new Style())->setFontBold()->setFontSize(25)->setCellAlignment(CellAlignment::CENTER)->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
        $this->writer->addRow(Row::fromValues([__('messages.downloads.file_report_title_annual')], $style));
        $this->options->mergeCells(0, 1, $lastColumn, 1, $this->writer->getCurrentSheet()->getIndex());

        $this->writer->addRow(Row::fromValues([__('messages.downloads.file_report_station'), '', '', '', __('messages.downloads.file_report_date_emit') . $currentDate, '', '', '', '', '', '', __('messages.downloads.file_report_latitude') . $this->station->latitude]));
        $this->writer->addRow(Row::fromValues([$this->station->name, '', '', '', __('messages.downloads.file_report_date_from') . $fromDateFormatted . ' - ' . __('messages.downloads.file_report_date_to') . $toDateFormatted, '', '', '', '', '', '', __('messages.downloads.file_report_longitude') . $this->station->longitude]));

        $styleHeads = (new Style())->setFontBold()->setCellAlignment(CellAlignment::CENTER)->setCellVerticalAlignment(CellVerticalAlignment::CENTER)->setTextRotation(90);
        $styleHeads->setBorder($border);
        $this->writer->addRow(Row::fromValues($this->heads, $styleHeads));

        //realizamos la consulta
        $fromDate = Carbon::parse($this->fromDate . '00:00:00');
        $toDate = Carbon::parse($this->toDate . '23:59:59'); //colocamos la fecha final correcta
        $year = $fromDate->format('Y');
        $arrayData = [];
        
        if($this->typeS == "qair"){
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->year)
            //     ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->year)
                ->table('senva_daily_data')
                ->selectRaw('
                    f_month,
                    avg(tempout) as tempout,
                    avg(humout) as humout,
                    avg(pm1) as pm1,
                    avg(pm2_5) as pm2_5,
                    avg(pm10) as pm10
                ')
                ->where('station_id', $this->station->id)
                ->where('f_year', $this->year)
                ->groupBy('f_month')
                ->orderBy('f_month', 'asc');
            $i = 0;
            foreach ($query->cursor() as $item) {  //utilizamos cursor para optimizar los registros obtenidos                

                $arrayData[$item->f_month] = [
                    $this->monthList[$item->f_month],
                    $item->tempout != null ? round(Utils::convertTemp($item->tempout, $this->measureSystem), 3) : '',
                    $item->humout != null ? round($item->humout, 3) : '',
                    $item->pm1 != null ? round($item->pm1, 1) : '',
                    $item->pm2_5 != null ? round($item->pm2_5, 1) : '',
                    $item->pm10 != null ? round($item->pm10, 3) : ''
                ];

                if ($i % 1000 === 0) {
                    flush(); // vaciamos el buffer cada 1000 filas
                }
            };
            for ($month=1 ; $month<=12 ; $month++ ) {
                if (isset($arrayData[$month])) {
                    $this->writer->addRow(Row::fromValues($arrayData[$month]));
                } else {
                    $this->writer->addRow(Row::fromValues([$this->monthList[$month], '', '', '', '', '']));
                }
            }
            $lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        }else{
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
                    max(winddirstr2) as winddirstr2,
                    avg(windspeed2) as windspeed2,
                    max(windspeedhi2) as windspeedhi2,
                    sum(raintotal) as raintotal,
                    avg(press) as press,
                    avg(heatindexout) as heatindexout,
                    avg(uvindex) as uvindex,
                    avg(solrad) as solrad,
                    sum(solevo) as solevo,
                    avg(soiltemp1) as soiltemp1,
                    avg(soilhum1) as soilhum1,
                    avg(leaftemp1) as leaftemp1,
                    avg(leafhum1) as leafhum1
                ')
                ->where('station_id', $this->station->id)
                ->where('f_year', $this->year)
                ->groupBy('f_month')
                ->orderBy('f_month', 'asc');
            $i = 0;
            foreach ($query->cursor() as $item) {  //utilizamos cursor para optimizar los registros obtenidos

                $bulbo = null;
                try{
                    $e= (6.11 * pow(10,(7.5 *round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1)))));
                    $resultado = (((0.00066 * round(Utils::convertPress($item->press, $this->measureSystem), 1)) *  round(Utils::convertTemp($item->tempout, $this->measureSystem), 1)) + ((4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) + 237.7) , 2) * round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $this->measureSystem), 1) ) + (4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $this->measureSystem), 1) + 237.7) , 2));
                
                $bulbo = round($resultado,2);
                } catch (\Exception $e) {}

                $arrayData[$item->f_month] = $this->filterExportValues($this->monthlyColumnKeys(), [
                    $this->monthList[$item->f_month],
                    $item->tempout != null ? round(Utils::convertTemp($item->tempout, $this->measureSystem), 3) : '',
                    $item->tempoutmax != null ? round(Utils::convertTemp($item->tempoutmax, $this->measureSystem), 3) : '',
                    $item->tempoutmin != null ? round(Utils::convertTemp($item->tempoutmin, $this->measureSystem), 3): '',
                    $item->dewptout != null ? round(Utils::convertTemp($item->dewptout, $this->measureSystem), 3) : '',
                    $item->humout != null ? round($item->humout, 3) : '',
                    $item->winddirstr != null ?  $item->winddirstr : '',
                    $item->windspeed != null ? round(Utils::convertSpeed($item->windspeed, $this->measureSystem), 0) : '',
                    $item->windspeedhi != null ? round(Utils::convertSpeed($item->windspeedhi, $this->measureSystem), 3) : '',
                    $item->winddirstr2 != null ? $item->winddirstr2 : '',
                    $item->windspeed2 != null ? round(Utils::convertSpeed($item->windspeed2, $this->measureSystem), 3) : '',
                    $item->windspeedhi2 != null ? round(Utils::convertSpeed($item->windspeedhi2, $this->measureSystem), 3) : '',
                    $item->raintotal != null ? round(Utils::convertLength($item->raintotal, $this->measureSystem), 3) : '',
                    $item->press != null ? round(Utils::convertPress($item->press, $this->measureSystem), 2) : '',
                    $bulbo!=null?(string)$bulbo:'',
                    $item->heatindexout != null ? round(Utils::convertTemp($item->heatindexout, $this->measureSystem), 3) : '',
                    $item->uvindex != null ? round($item->uvindex, 4) : '',
                    $item->solrad != null ? round(Utils::convertPower($item->solrad, $this->measureSystem), 2) : '',
                    $item->solevo != null ? round(Utils::convertLength($item->solevo, $this->measureSystem), 3) : '',
                    $item->soiltemp1 != null ? round(Utils::convertLength($item->soiltemp1, $this->measureSystem), 3) : '',
                    $item->soilhum1 != null ? round($item->soilhum1, 3) : '',
                    $item->leaftemp1 != null ? round(Utils::convertLength($item->leaftemp1, $this->measureSystem), 3) : '',
                    $item->leafhum1 != null ? round($item->leafhum1, 3) : ''
                ]);

                if ($i % 1000 === 0) {
                    flush(); // vaciamos el buffer cada 1000 filas
                }
            };
            for ($month=1 ; $month<=12 ; $month++ ) {
                if (isset($arrayData[$month])) {
                    $this->writer->addRow(Row::fromValues($arrayData[$month]));
                } else {
                    $this->writer->addRow(Row::fromValues($this->filterExportValues($this->monthlyColumnKeys(), [$this->monthList[$month], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''])));
                }
            }
            $lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        }
    }

    /**
     * Cierra el escritor y entrega el archivo generado al navegador.
     */
    public function download() {
        return response()->streamDownload(function () {
            $this->writer->close();
        },  $this->filename);

    }

    /**
     * Normaliza las columnas visibles manteniendo la fecha al inicio.
     */
    protected function normalizeVisibleColumns(array $columns): array
    {
        if (empty($columns)) {
            return [];
        }

        return array_values(array_unique(array_merge(['receipt_date'], array_diff($columns, ['receipt_date']))));
    }

    /**
     * Filtra valores del reporte segun la configuracion de columnas.
     */
    protected function filterExportValues(array $columns, array $values): array
    {
        if ($this->typeS === 'qair' || empty($this->visibleColumns)) {
            return $values;
        }

        $row = array_combine($columns, $values);

        if ($row === false) {
            return $values;
        }

        return array_map(fn ($column) => $row[$column] ?? '', $this->visibleColumns);
    }

    /**
     * Devuelve el orden de columnas del reporte diario normal.
     */
    protected function dailyColumnKeys(): array
    {
        return [
            'receipt_date',
            'tempout',
            'tempoutmax',
            'tempoutmin',
            'dewptout',
            'humout',
            'winddir',
            'winddirstr',
            'windspeed',
            'windspeedhi',
            'winddir2',
            'winddirstr2',
            'windspeed2',
            'windspeedhi2',
            'raintotal',
            'rainrate',
            'press',
            'wetbulb',
            'heatindexout',
            'uvindex',
            'solrad',
            'solevo',
            'soiltemp1',
            'soilhum1',
            'leaftemp1',
            'leafhum1',
        ];
    }

    /**
     * Devuelve el orden de columnas de reportes mensual y anual normales.
     */
    protected function monthlyColumnKeys(): array
    {
        return [
            'receipt_date',
            'tempout',
            'tempoutmax',
            'tempoutmin',
            'dewptout',
            'humout',
            'winddirstr',
            'windspeed',
            'windspeedhi',
            'winddirstr2',
            'windspeed2',
            'windspeedhi2',
            'raintotal',
            'press',
            'wetbulb',
            'heatindexout',
            'uvindex',
            'solrad',
            'solevo',
            'soiltemp1',
            'soilhum1',
            'leaftemp1',
            'leafhum1',
        ];
    }
}

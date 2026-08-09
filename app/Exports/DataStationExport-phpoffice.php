<?php

namespace App\Exports;

use App\Models\StationM;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataStationExport implements WithStyles, WithEvents
{
    protected $data = [];
    protected $station;
    protected $format;
    protected $fromDate;
    protected $toDate;
    protected $year;
    protected $month;
    
    public function __construct($stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null, $data = [])
    {
        $this->station = StationM::find($stationId);
        $this->format = $format;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->year = $year;
        $this->month = $month;
        $this->data = $data;
    }

    public function setDaily($sheet) {
        date_default_timezone_set('America/La_Paz'); 
        $fromDateFormatted = Carbon::parse($this->fromDate)->format('d/m/Y');
        $toDateFormatted = Carbon::parse($this->toDate)->format('d/m/Y');
        $currentDate = date('d/m/Y G:i:s');

        $sheet->setCellValue('G1', 'Reporte Diario');
        $sheet->setCellValue('A2', 'Nombre de estación:');
        $sheet->setCellValue('A3', $this->station->name);
        $sheet->setCellValue('E2', 'Fecha de emision: ' . $currentDate);
        $sheet->setCellValue('E3', 'Desde: ' . $fromDateFormatted . ' - Hasta: ' . $toDateFormatted);
        $sheet->setCellValue('K2', 'Ubicación:');
        $sheet->setCellValue('M2', 'Longitud: ' . $this->station->latitude);
        $sheet->setCellValue('M3', 'Latitud: ' . $this->station->longitude);
        //titulos
        $lastColumn = "A";
        $sheet->setCellValue("{$lastColumn}5", 'FECHA');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMPERATURA (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP MAX (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP MIN (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PUNTO DE ROCIO (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUMEDAD (%)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'DIR VIENTO (0º N)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PUNT. CARDINALES');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'VEL VIENTO (km/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'RAFAGA (km/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'LLUVIA ACUMULADA DIARIA (mm)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INTENSIDAD (mm/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PRESION BAROMETRICA (hpa)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'BULBO HUMEDO (C°)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INDICE DE CALOR (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INDICE UV');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'RADIACION SOLAR (W/m^2)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'EVAPOTRANSPIRACION (mm)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP. SUELO (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUM. SUELO (cbar)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP. HOJA (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUM. HOJA (wet)');
        $sheet->getStyle("A5:{$lastColumn}5")->getAlignment()->setTextRotation(90);
        $sheet->getStyle("A5:{$lastColumn}5")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A5:{$lastColumn}5")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $fila = 6;
        foreach ($this->data as $row) {
            $lastColumn = "A";
            $sheet->setCellValue("{$lastColumn}{$fila}", Carbon::parse($row['receipt_date'])->format('d/m/Y H:i'));            
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['tempout']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['tempoutmax']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['tempoutmin']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['dewptout']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['humout']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['winddir']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['winddir']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['windspeed']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['windspeedhi']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['raintotal']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['rainrate']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['press']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['wetbulb']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['heatindexout']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['uvindex']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['solrad']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['solevo']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['soiltemp1']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['soilhum1']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['leaftemp1']);
            $lastColumn++;
            $sheet->setCellValue("{$lastColumn}{$fila}", $row['leafhum1']);
            $fila++;
        }
  
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(20);
    }

    public function setMonthly($sheet) {
        date_default_timezone_set('America/La_Paz'); 
        $fromDateFormatted = Carbon::create($this->year, $this->month, 1)->startOfMonth()->format('d/m/Y');
        $toDateFormatted = Carbon::create($this->year, $this->month, 1)->endOfMonth()->format('d/m/Y');
        $lastDayMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth()->day;
        $currentDate = date('d/m/Y G:i:s');

        $sheet->setCellValue('G1', 'Reporte Mensual');
        $sheet->setCellValue('A2', 'Nombre de estación:');
        $sheet->setCellValue('A3', $this->station->name);
        $sheet->setCellValue('E2', 'Fecha de emision: ' . $currentDate);
        $sheet->setCellValue('E3', 'Desde: ' . $fromDateFormatted . ' - Hasta: ' . $toDateFormatted);
        $sheet->setCellValue('K2', 'Ubicación:');
        $sheet->setCellValue('M2', 'Longitud: ' . $this->station->latitude);
        $sheet->setCellValue('M3', 'Latitud: ' . $this->station->longitude);
        //titulos
        $lastColumn = "A";
        $sheet->setCellValue("{$lastColumn}5", 'DIA');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMPERATURA (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP MAX (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP MIN (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PUNTO DE ROCIO (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUMEDAD (%)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PUNT. CARDINALES');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'VEL VIENTO (km/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'RAFAGA (km/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'LLUVIA ACUMULADA DIARIA (mm)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PRESION BAROMETRICA (hpa)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'BULBO HUMEDO (C°)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INDICE DE CALOR (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INDICE UV');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'RADIACION SOLAR (W/m^2)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'EVAPOTRANSPIRACION (mm)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP. SUELO (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUM. SUELO (cbar)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP. HOJA (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUM. HOJA (wet)');
        $sheet->getStyle("A5:{$lastColumn}5")->getAlignment()->setTextRotation(90);
        $sheet->getStyle("A5:{$lastColumn}5")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A5:{$lastColumn}5")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        for ($day=1 ; $day<=$lastDayMonth ; $day++ ) {
            $lastColumn = "A";
            $fila = 5 + $day;
            $sheet->setCellValue("{$lastColumn}{$fila}", $day);            
            $lastColumn++;
            if (isset($this->data[$day])) {
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['tempout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['tempoutmax']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['tempoutmin']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['dewptout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['humout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['winddirstr']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['windspeed']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['windspeedhi']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['raintotal']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['press']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['wetbulb']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['heatindexout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['uvindex']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['solrad']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['solevo']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['soiltemp1']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['soilhum1']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['leaftemp1']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$day]['leafhum1']);
            } else {
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
            }
        }
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
    }

    public function setAnnual($sheet) {
        date_default_timezone_set('America/La_Paz'); 
        $fromDateFormatted = Carbon::create($this->year, $this->month, 1)->startOfMonth()->format('d/m/Y');
        $toDateFormatted = Carbon::create($this->year, $this->month, 1)->endOfMonth()->format('d/m/Y');
        $currentDate = date('d/m/Y G:i:s');

        $sheet->setCellValue('G1', 'Reporte Anual');
        $sheet->setCellValue('A2', 'Nombre de estación:');
        $sheet->setCellValue('A3', $this->station->name);
        $sheet->setCellValue('E2', 'Fecha de emision: ' . $currentDate);
        $sheet->setCellValue('E3', 'Desde: ' . $fromDateFormatted . ' - Hasta: ' . $toDateFormatted);
        $sheet->setCellValue('K2', 'Ubicación:');
        $sheet->setCellValue('M2', 'Longitud: ' . $this->station->latitude);
        $sheet->setCellValue('M3', 'Latitud: ' . $this->station->longitude);
        //titulos
        $lastColumn = "A";
        $sheet->setCellValue("{$lastColumn}5", 'DIA');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMPERATURA (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP MAX (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP MIN (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PUNTO DE ROCIO (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUMEDAD (%)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PUNT. CARDINALES');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'VEL VIENTO (km/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'RAFAGA (km/h)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'LLUVIA ACUMULADA DIARIA (mm)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'PRESION BAROMETRICA (hpa)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'BULBO HUMEDO (C°)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INDICE DE CALOR (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'INDICE UV');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'RADIACION SOLAR (W/m^2)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'EVAPOTRANSPIRACION (mm)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP. SUELO (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUM. SUELO (cbar)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'TEMP. HOJA (ºC)');
        $lastColumn++;
        $sheet->setCellValue("{$lastColumn}5", 'HUM. HOJA (wet)');
        $sheet->getStyle("A5:{$lastColumn}5")->getAlignment()->setTextRotation(90);
        $sheet->getStyle("A5:{$lastColumn}5")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("A5:{$lastColumn}5")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        for ($month=1 ; $month<=12 ; $month++ ) {
            $lastColumn = "A";
            $fila = 5 + $month;
            $sheet->setCellValue("{$lastColumn}{$fila}", $month);            
            $lastColumn++;
            if (isset($this->data[$month])) {
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['tempout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['tempoutmax']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['tempoutmin']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['dewptout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['humout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['winddirstr']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['windspeed']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['windspeedhi']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['raintotal']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['press']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['wetbulb']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['heatindexout']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['uvindex']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['solrad']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['solevo']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['soiltemp1']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['soilhum1']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['leaftemp1']);
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", $this->data[$month]['leafhum1']);
            } else {
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
                $lastColumn++;
                $sheet->setCellValue("{$lastColumn}{$fila}", '-');
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                if ($this->format == 'daily') {
                    $this->setDaily($sheet);
                }
                if ($this->format == 'monthly') {
                    $this->setMonthly($sheet);
                }
                if ($this->format == 'annual') {
                    $this->setAnnual($sheet);
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B2')->getFont()->setBold(true);
    }
}

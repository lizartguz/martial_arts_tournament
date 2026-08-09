<?php

namespace App\Livewire\ForecastsEdit;

use App\Models\AlertM;
use App\Models\ForecastM;
use App\Models\StationM;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Throwable;
use App\Support\ErrorMessage;
use App\Http\Controllers\ForecastC;
use Illuminate\Http\Request;
use Livewire\Attributes\Renderless;

class ForecastsEdit extends Component
{
    public $stationList = [];
    public $columnsName = [];

    public $showResults = false;
    public $hasData = false;

    protected $listeners = ['processParams', 'saveData', 'collectForecasts'];

    public $selectedStation;

    public function mount() {
        $this->columnsName = [
            'ID',
            __('messages.forecasts_edit.data_columns.date'),
            __('messages.forecasts_edit.data_columns.v10m'),
            __('messages.forecasts_edit.data_columns.v10m_dir'),
            __('messages.forecasts_edit.data_columns.prec'),
            __('messages.forecasts_edit.data_columns.cape'),
            __('messages.forecasts_edit.data_columns.li'),
            __('messages.forecasts_edit.data_columns.t2m'),
            __('messages.forecasts_edit.data_columns.hr2m'),
            __('messages.forecasts_edit.data_columns.baro'),
            __('messages.forecasts_edit.data_columns.cloud'),
            __('messages.forecasts_edit.data_columns.snow'),
            __('messages.forecasts_edit.data_columns.dp'),
        ];
        $this->stationList = StationM::select('stations.id', 'stations.name')
            ->where('stations.state', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function processParams($selectedStation) {
        $this->selectedStation = $selectedStation;
        $data = [];
        if ($this->selectedStation > 0) {
            $data = ForecastM::where('station_id', $this->selectedStation)
                ->orderBy('reg_date', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        $item->id,
                        $item->reg_date,
                        $item->v10m,
                        $item->v10m_dir,
                        $item->prec,
                        $item->cape,
                        $item->li,
                        $item->t2m,
                        $item->hr2m,
                        $item->baro,
                        $item->cloud,
                        $item->snow,
                        $item->dp
                    ];
                })
                ->toArray();
            $this->hasData = true; //count($data) > 0;
        }
        $data[] = [];
        $this->showResults = true;
        $this->dispatch('showData', $data);
    }

    public function rowHasEmpty($row) {
        $hasEmpty = false;
        foreach ($row as $index => $item) {
            if ($index === 0) continue;
            if ($item === null || trim($item) === "") {
                $hasEmpty = true;
                break;
            }
        }
        return $hasEmpty;
    }

    public function rowIsEmpty($row) {
        $isEmpty = false;
        foreach ($row as $index => $item) {
            if ($item === null || trim($item) === "") {
                $isEmpty = true;
                break;
            }
        }
        return $isEmpty;
    }

    public function updatePrec($prec) {
        $newPrec = $prec;
        if ($prec == 0.1) {
            $newPrec = 0.6;
           } elseif ($prec == 0.2) {
            $newPrec = 0.8;
           } elseif ($prec == 0.3) {
            $newPrec = 0.9;
           } elseif ($prec == 0.4) {
            $newPrec = 1.2;
           } elseif ($prec == 0.5) {
            $newPrec = 1.5;
           }
        return $newPrec;
    }

	function DZ ($prec) {
		return ((($prec<=0.89)&&($prec>=0.55))?10:0);
	}

	function RA ($prec) {
		return ((($prec<=60)&&($prec>=0.9))?12:0);
	}

	function SN ($temp,$prec) {
		return ((($temp<=1.4)&&($prec>=0.55))?14:0);
	}

	function SH ($prec,$li) {
		return ((($prec>=0.78)&&($li<=-2.5))?16:0);
	}

	function TS ($prec,$li) {
		return ((($prec>=1)&&($li<-3))?18:0);
	}

	function GR20($prec, $li){
        if ($prec >= 0.78 && $li <= -8.5) {
            return 800000000;
        } else {
            if ($prec <= 1) {
                return 0;
            } else {
                if ($li < -12.1) {
                    return 900000000;
                } else {
                    return 0;
                }
            }
        }
    }

	function SKY ($nubes) {
		$cadena = "";
		if (($nubes>0)&&($nubes<=0.15)) { $cadena = $cadena . '0';}
		if (($nubes>0.15)&&($nubes<=45)) { $cadena = $cadena . '2';}
		if (($nubes>45)&&($nubes<=55.5)) { $cadena = $cadena . '4';}
		if (($nubes>55.5)&&($nubes<=98.78)) { $cadena = $cadena . '6';}
		if (($nubes>98.78)&&($nubes<=100)) { $cadena = $cadena . '8';}
		return intval($cadena);
	}

    function ICON_IMAGE($fecha,$temp,$prec,$li,$nubes){
		$indice =  max($this->DZ($prec),$this->RA($prec),$this->SN($temp,$prec),$this->SH($prec,$li),$this->TS($prec,$li),$this->GR20($prec,$li),$this->SKY($nubes));
		$ico = '';
        if (($indice>=0)&&($indice<2)) { $ico = '1';}
		if (($indice>=2)&&($indice<4)) { $ico = '2';}
		if (($indice>=4)&&($indice<6)) { $ico = '3';}
		if (($indice>=6)&&($indice<8)) { $ico = '4';}
		if (($indice>=8)&&($indice<10)) { $ico = '5';}
		if (($indice>=10)&&($indice<12)) { $ico = '7';}
		if (($indice>=12)&&($indice<14)) { $ico = '6';}
		if (($indice>=14)&&($indice<16)) { $ico = '10';}
		if (($indice>=16)&&($indice<18)) { $ico = '8';}
		if (($indice>=18)&&($indice<20)) { $ico = '9';}
		if (($indice>=20)&&($indice<247.5)) { $ico = '11';}

        $dt = Carbon::parse($fecha);
		if( ($dt->format('H') >= 6) && ($dt->format('H') < 18) ){
			$ico = $ico."a";
		} else {
			$ico = $ico."b";
		}
		return $ico;
	}

    function ICON($temp,$prec,$li,$nubes){
		return max($this->DZ($prec),$this->RA($prec),$this->SN($temp,$prec),$this->SH($prec,$li),$this->TS($prec,$li),$this->GR20($prec,$li),$this->SKY($nubes));
    }

    function SH_TS($prec, $li) {
        return max($this->SH($prec,$li),$this->TS($prec,$li),$this->GR20($prec,$li));
    }

    public function reCalculate() {
        $currentDate = Carbon::now();;
        $year = Carbon::now()->format('Y');
        
        // Conexión antigua (comentado)
        // $TEMPmin = DB::connection('db_guest_h' . $year)
        //     ->table('data')
        
        // Nueva conexión
        $TEMPmin = DB::connection('senvatec_db_' . $year)
            ->table('senva_data')
            ->where('station_id', $this->selectedStation)
            ->where('tempoutmin','!=',0)
            ->whereDate('receipt_date', $currentDate)
            ->min('tempoutmin');
        $i = 0;
        $diff = 0.0;
        $flag_first_major = false;
        $t2m_original_aux = null;
        $forecastData = ForecastM::where('station_id', $this->selectedStation)
            ->orderBy('reg_date', 'asc')
            ->get();
        foreach ($forecastData as $record) {
            $t2m_original = $record->t2m;
            if ($this->selectedStation !== 480) {
                if ($i === 0) {
                    $diff = $t2m_original - ($TEMPmin !== null ? $TEMPmin : ($t2m_original + 2.4));
                    $t2m = ($TEMPmin !== null && $TEMPmin <= 0) ? $t2m_original : $TEMPmin;
                    $t2m_original_aux = $t2m_original;
                } elseif ($i === 1) {
                    $t2m = $t2m_original - (-$diff);
                    if ($t2m_original > $t2m_original_aux) {
                        $flag_first_major = true;
                    } else {
                        $flag_first_major = false;
                    }
                    $t2m_original_aux = $t2m_original;
                } else {
                    if ($t2m_original > $t2m_original_aux) {
                        if ($flag_first_major) {
                            $t2m = $t2m_original - (-$diff * 1.5);
                        } else {
                            $t2m = $t2m_original - (-$diff);
                            $flag_first_major = true;
                        }
                        $t2m_original_aux = $t2m_original;
                    } else {
                        if ($flag_first_major) {
                            $t2m = $t2m_original - (-$diff * 1.5);
                            $flag_first_major = false;
                        } else {
                            $t2m = $t2m_original - (-$diff);
                            $flag_first_major = true;
                        }
                        $t2m_original_aux = $t2m_original;
                    }
                }
            } else {
                $t2m = $t2m_original;
            }
            $prec = $this->updatePrec($record->prec);
            ForecastM::where('id', $record->id)
                ->update([
                    't2m' => $t2m,
                    'prec' => $prec,
                    'icon' => $this->ICON($t2m, $prec, $record->li, $record->cloud),
                    'icon_image' => $this->ICON_IMAGE($record->reg_date, $t2m, $prec, $record->li, $record->cloud),
                    'sh_ts' => $this->SH_TS($prec, $record->li),
                ]);
            $i = $i + 1;
        }
    }

    public function updateTotals() {
        $total_days = ForecastM::selectRaw('DATE(reg_date) as day,
                 MAX(t2m) as tmax,
                 MIN(t2m) as tmin,
                 MAX(v10m) as v10mMax,
                 MAX(hr2m) as hr2mMax,
                 MIN(hr2m) as hr2mMin,
                 SUM(prec) as precTotal')
            ->where('station_id', $this->selectedStation)
            ->groupByRaw('DATE(reg_date)')
            ->get();
        foreach ($total_days as $record) {
            $windSpeedMax = "";

            if ($record->v10mMax > 12) {
                $windSpeedMax .= "";
            } elseif ($record->v10mMax > 9) {
                $windSpeedMax .= "25";
            } else {
                $windSpeedMax .= "15";
            }

            if ($record->v10mMax > 18) {
                $windSpeedMax .= "";
            } elseif ($record->v10mMax > 12) {
                $windSpeedMax .= "35";
            }

            if ($record->v10mMax > 23) {
                $windSpeedMax .= "";
            } elseif ($record->v10mMax > 18) {
                $windSpeedMax .= "45";
            }

            if ($record->v10mMax > 33) {
                $windSpeedMax .= "";
            } elseif ($record->v10mMax > 23) {
                $windSpeedMax .= "65";
            }

            if ($record->v10mMax > 33) {
                $windSpeedMax .= "80";
            } elseif ($record->v10mMax > 35) {
                $windSpeedMax .= "90";
            }

            ForecastM::WhereDate('reg_date', $record->day)
                ->where('station_id', $this->selectedStation)
                ->update([
                    'tmax' => $record->tmax + 0.8,
                    'tmin' => $record->tmin,
                    'v10m_max' => $windSpeedMax !== "" ? $windSpeedMax : null,
                    'hr2m_max' => $record->hr2mMax,
                    'hr2m_min' => $record->hr2mMin,
                    'prec_total' => $record->precTotal
                ]);
        }
    }

    public function deleteEmptyRows($data) {
        return array_filter($data, function ($row) {
            return array_filter($row, function ($value) {
                return $value !== null && $value !== "";
            });
        });
    }

    public function isValidDate($fecha, $formato = 'Y-m-d H:i:s') {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha .= ' 00:00:00';
        }
        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }

    public function checkRows($data) {
        $index = 1;
        $hasError = false;
        foreach ($data as $row) {
            if (!$this->isValidDate($row[1])) {
                $hasError = true;
                break;
            };
            if (!is_numeric($row[2])
                || !is_numeric($row[4])
                || !is_numeric($row[5])
                || !is_numeric($row[6])
                || !is_numeric($row[7])
                || !is_numeric($row[8])
                || !is_numeric($row[9])
                || !is_numeric($row[10])
                || !is_numeric($row[11])
                || !is_numeric($row[12])) {
                $hasError = true;
                break;
            }
            if (!in_array($row[3], ['N', 'E', 'S', 'O', 'NE', 'NO', 'SE', 'SO'], true)) {
                $hasError = true;
                break;
            }
            $index = $index + 1;
        }
        return $hasError ? $index : 0;
    }

    public function generateAlerts() {
        $forecastData = ForecastM::selectRaw('DATE(reg_date) as date, MAX(sh_ts) as max_ts, MAX(v10m) as max_v10m, MAX(icon) AS max_icon, MIN(t2m) AS min_t2m, MIN(dp) as min_dp')
            ->where('station_id', $this->selectedStation)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        AlertM::where('station_id', $this->selectedStation)->delete();
        foreach ($forecastData as $row) {
            //alertas TS
            if ($row->max_ts==18) {
				$ts_txt = "Tormentas electricas y lluvia moderada a fuerte";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $ts_txt,
                    'f' => 1,
                    'state' => 1
                ]);
			}
            //alerta viento
            if ($row->max_v10m>23&$row->max_v10m<=33) {
				$v10m_txt = "Viento Maximo 65 Km/h";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $v10m_txt,
                    'f' => 2,
                    'state' => 1
                ]);
			}
    		if ($row->max_v10m>33&$row->max_v10m<=35) {
				$v10m_txt = "Viento Maximo 80 Km/h";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $v10m_txt,
                    'f' => 2,
                    'state' => 1
                ]);
			}
        	if ($row->max_v10m>35) {
				$v10m_txt = "Viento Maximo 90 Km/h";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $v10m_txt,
                    'f' => 2,
                    'state' => 1
                ]);
			}
            //alerta Granizo
    		if ($row->max_icon==20) {
				$t_txt = "80% Tormentas Electricas con Lluvia y Granizo";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $t_txt,
                    'f' => 3,
                    'state' => 1
                ]);
			}
            //falta heladas
        	if ($row->min_t2m<=1.4&&$row->min_dp<=0) {
				$t_txt = "90%Probable Helada";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $t_txt,
                    'f' => 4,
                    'state' => 1
                ]);
			}
            //alerta nevada
        	if ($row->max_icon==14) {
				$t_txt = "80% Nieve";
                AlertM::create([
                    'station_id' => $this->selectedStation,
                    'reg_date' => $row->date,
                    'description' => $t_txt,
                    'f' => 5,
                    'state' => 1
                ]);
			}
        }
    }

    public function saveData($gridData, $updatedIds, $deletedIds) {
        $gridData = $this->deleteEmptyRows($gridData);
        $rowError = $this->checkRows($gridData);
        if ($rowError>0) {
            $this->dispatch('error', ['message' => __('messages.forecasts_edit.errors.data_row_required', ['row' => $rowError])]);
            return;
        }
        $rowCount = 1;
        if ($this->selectedStation > 0) {
            try {
                DB::beginTransaction();
                ForecastM::destroy($deletedIds); //primero eliminamos las filas que se eliminaron en el grid
                foreach ($gridData as $row) { //nuevos registros o modificar
                    $hasEmpty = $this->rowHasEmpty($row);
                    if ($hasEmpty) {
                        throw new Exception("hay datos en blanco");
                    }
                    if ($row[0] === null || $row[0] === '') {  //fila nueva
                        $forecast = ForecastM::create([
                            'station_id' => $this->selectedStation,
                            'reg_date' => $row[1],
                            'v10m' => $row[2],
                            'v10m_dir' => $row[3],
                            'prec' => $row[4],
                            'cape' => $row[5],
                            'li' => $row[6],
                            't2m' => $row[7],
                            'hr2m' => $row[8],
                            'baro' => $row[9],
                            'cloud' => $row[10],
                            'snow' => $row[11],
                            'dp' => $row[12],
                        ]);
                    } else {
                        if (in_array($row[0], $updatedIds)) { //fila a modificar
                            $forecast = ForecastM::where('id', $row[0])
                                ->update([
                                    'reg_date' => $row[1],
                                    'v10m' => $row[2],
                                    'v10m_dir' => $row[3],
                                    'prec' => $row[4],
                                    'cape' => $row[5],
                                    'li' => $row[6],
                                    't2m' => $row[7],
                                    'hr2m' => $row[8],
                                    'baro' => $row[9],
                                    'cloud' => $row[10],
                                    'snow' => $row[11],
                                    'dp' => $row[12],
                                ]);
                        }
                    }
                    $rowCount = $rowCount + 1;
                }
                if (count($gridData) > 0) {
                    $this->reCalculate();
                    $this->updateTotals();
                    $this->generateAlerts();
                }
                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                $this->dispatch('error', ['message' => __('messages.forecasts_edit.errors.error_db_message')]);
            } catch (Throwable $e) {
                DB::rollback();
                $this->dispatch('error', ['message' => __('messages.forecasts_edit.errors.data_row_required', ['row' => $rowCount])]);
            }
            $this->processParams($this->selectedStation);
        }
    }
    /**
     * Recolecta los pronósticos para una estación específica
     * @param int|null $selectedStation - La estación seleccionada, si no se proporciona, se recolectarán todos los pronósticos
     * @return void
     */
    #[Renderless]
    public function collectForecasts($selectedStation = null) {
        try {
            $forecastController = new ForecastC();
            
            // Crear el request con los parámetros correctos
            $params = [];
            if ($selectedStation && $selectedStation > 0) {
                $params['station_id'] = $selectedStation;
            }
            $request = Request::create('/forecast/collect', 'GET', $params);
            
            // Llamar a la función de recolección de pronósticos (con flag fromLivewire=true)
            $result = $forecastController->getForecastCurl($request, true);
            
            // Construir mensaje de éxito
            $message = 'Pronósticos recolectados exitosamente. ';
            $message .= 'Estaciones procesadas: ' . $result['processed_stations'] . '/' . $result['total_stations'];
            
            if (!empty($result['errors'])) {
                $message .= '. Errores: ' . count($result['errors']);
            }
            
            // Agregar nota para recargar manualmente si se recolectó para una estación específica
            if ($selectedStation && $selectedStation > 0) {
                $message .= '. Haz click en "Mostrar" para ver los datos actualizados.';
            }
            
            $this->dispatch('collectSuccess', ['message' => $message]);
        } catch (Exception $e) {
            $this->dispatch('error', ['message' => ErrorMessage::userSeesTechnicalDetail()
                ? 'Error al recolectar pronósticos: ' . $e->getMessage()
                : 'Error al recolectar pronósticos. Inténtelo nuevamente.']);
        }
    }

    public function render()
    {
        return view('livewire.forecasts-edit.forecasts-edit');
    }
}

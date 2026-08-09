<?php

namespace App\Livewire\Forecasts;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ForecastM;
use App\Models\StationM;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;
use Forecast;
use Illuminate\Support\Carbon;

class Forecasts extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $headers;
    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';
    public $perPage = 10;
    public $searchStation = '';

    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    public $formAdd = false;
    public $formUpdate = false;
    public $stations = [];
    public $forecasts = [];
    public $forecastId = [];
    
    public $name = null;
    public $percentage_value = null;
    public $reg_date = null;
    public $state = 1;
    
    public $stationIdAU = null;
    public $idAU = null;
    public $v10mAU = null;
    public $v10mdirAU = null;
    public $precAU = null;
    public $capeAU = null;
    public $liAU = null;
    public $t2mAU = null;
    public $hr2mAU = null;
    public $baroAU = null; 
    public $cloudAU = null;
    public $snowAU = null;  
    public $dpAU = null;
    public $reg_dateAU = null;
    public $new_reg_dateAU = null;
    public $data = [];

    public $station_name_text = null;

    
    
    public $messageFailed = null;
   
    protected $listeners = ['delete','updateForecast'];    
    
    public function mount(){
        $this->stations = StationM::OrderBy('name','desc')->select('id','name')->get();
       // dd($this->stations[0]);
    }

    public function closeFormButton(){
        $this->formAdd = false;
        $this->formUpdate = false;
    }

    public function addForecast(){       
       
        $flag = $this->verifData();
        if($flag){
            $this->dispatch('failedAlert',[ 'failed' => $this->messageFailed ]);            
        }else{
            try {
                date_default_timezone_set('America/La_Paz');
                $datetimeObj = new DateTime($this->reg_dateAU);
                DB::beginTransaction();
                try { 
                    ForecastM::create([
                        'reg_date'=> $datetimeObj->format('Y-m-d H:i:s'),
                        'v10m'=> $this->v10mAU,
                        'v10m_dir'=> $this->v10mdirAU,
                        'v850'=> null,
                        'v850_dir'=> null,
                        'prec'=> $this->precAU,
                        'cape'=> $this->capeAU,
                        'li'=> $this->liAU,
                        'dam'=> null,
                        'a850'=> null,
                        'a500'=> null,
                        't2m'=> $this->t2mAU,
                        'hr2m'=> $this->hr2mAU,
                        't850'=> null,
                        't500'=> null,
                        'baro'=> $this->baroAU,
                        'cloud'=> $this->cloudAU,
                        'snow'=> $this->snowAU,
                        'dp'=> $this->dpAU,
                        'tmax'=> null,
                        'tmin'=> null,
                        'sh_ts'=> null,
                        'icon'=> null,
                        'min_t2m'=> null,
                        'min_dp'=> null,
                        'state'=> 1, 
                        'station_id' => $this->stationIdAU,                         
                        ]); 
                        DB::commit();
                        $this->formAdd = false;
                        $this->formUpdate = false;
                        $this->clearVariable();
                        $this->dispatch('successAlert',[ 'success' => 'Registro exitoso!' ]);
                               
                } catch (\Throwable $th) {
                    DB::rollBack(); 
                    $this->dispatch('failedAlert',[ 'failed' => 'No se pudo realizar el registro del pronóstico. Intente mas tarde.' ]);                
                }                
            } catch (\Throwable $th) {
                //throw $th;
            }
        }          
    }

    private function verifData(){
        $flag = false;
        $fechaHoraRegex = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/';
        if (!empty($this->reg_dateAU) && preg_match($fechaHoraRegex, $this->reg_dateAU)) {
            if (is_numeric($this->v10mAU)) {            
                if (is_numeric($this->precAU)) {                
                    if (is_numeric($this->capeAU)) {                    
                        if (is_numeric($this->liAU)) {                        
                            if (is_numeric($this->t2mAU)) {                            
                                if (is_numeric($this->hr2mAU)) {                                
                                    if (is_numeric($this->baroAU)) {                                    
                                        if (is_numeric($this->cloudAU)) {                                            
                                            if (is_numeric($this->snowAU)) {                                            
                                                if (is_numeric($this->dpAU)) {
                                                }else{
                                                    $flag = true;
                                                    $this->messageFailed = "DP no es un dato válido";
                                                }
                                            }else{
                                                $flag = true;
                                                $this->messageFailed = "NIEVES no es un dato válido";
                                            }
                                        }else{
                                            $flag = true;
                                            $this->messageFailed = "NUBES no es un dato válido";
                                        }
                                    }else{
                                        $flag = true;
                                        $this->messageFailed = "BARO no es un dato válido"; 
                                    }
                                }else{
                                    $flag = true;
                                    $this->messageFailed = "HR2M no es un dato válido";
                                }
                            }else{
                                $flag = true;
                                $this->messageFailed = "T2M no es un dato válido";
                            }
                        }else{
                            $flag = true;
                            $this->messageFailed = "LI no es un dato válido";
                        }
                    }else{
                        $flag = true;
                        $this->messageFailed = "CAPE no es un dato válido";
                    }
                }else{
                    $flag = true;
                    $this->messageFailed = "PREC no es un dato válido";
                }
            }else{
                $flag = true;
                $this->messageFailed = "V10M no es un dato válido";       
            } 
        }else{
            $flag = true;
            $this->messageFailed = "El formato de la fecha no es un dato válido"; 
        }    
        return $flag;
    }

    public function updateForecast(){       
        try {
            date_default_timezone_set('America/La_Paz');
            $datetimeObj = null;
            if($this->new_reg_dateAU!=null){
                $datetimeObj = new DateTime($this->new_reg_dateAU);
            } else{
                $datetimeObj = new DateTime($this->reg_dateAU);
            }
            DB::beginTransaction();
            try {
                ForecastM::where('id','=',$this->idAU)
                ->update([
                    'reg_date'=> $datetimeObj->format('Y-m-d H:i:s'),
                    'v10m'=> $this->v10mAU,
                    'v10m_dir'=> $this->v10mdirAU,
                    'prec'=> $this->precAU,
                    'cape'=> $this->capeAU,
                    'li'=> $this->liAU,
                    't2m'=> $this->t2mAU,
                    'hr2m'=> $this->hr2mAU,
                    'baro'=> $this->baroAU,
                    'cloud'=> $this->cloudAU,
                    'snow'=> $this->snowAU,
                    'dp'=> $this->dpAU,
                    ]);
                    DB::commit();
                    $this->formAdd = false;
                    $this->formUpdate = false;
                    $this->clearVariable();
                    $this->dispatch('successAlert',[ 'success' => 'Actualización exitosa!' ]);
                            
            } catch (\Throwable $th) {
                DB::rollBack(); 
                $this->dispatch('failedAlert',[ 'failed' => 'No se pudo realizar la actualización del pronóstico. Intente mas tarde.' ]);                
            }                
        } catch (\Throwable $th) {
            //throw $th;
        }                
    }

    public function visibleAdd(){
      
        $station = StationM::where('name', $this->searchStation)->first();
        if ($station) {
            $this->clearVariable();
            $this->stationIdAU = $station->id;
            $this->formAdd = !$this->formAdd;
        }else{
            $this->dispatch('failedAlert',[ 'failed' => 'No hay estación seleccionada' ]); 
        }      
    }

    public function noVisibleAdd(){
        $this->clearVariable();
        $this->formAdd = false; 
        $this->formUpdate = false;
                 
    }

    public function formUpdateModal($data){
        $data = json_decode($data);        
        $this->idAU = $data->id;
        $this->v10mAU = $data->v10m;
        $this->v10mdirAU = $data->v10m_dir;
        $this->precAU = $data->prec;
        $this->capeAU = $data->cape;
        $this->liAU = $data->li;
        $this->t2mAU = $data->t2m;
        $this->hr2mAU = $data->hr2m;
        $this->baroAU = $data->baro;
        $this->cloudAU = $data->cloud;
        $this->snowAU = $data->snow;
        $this->dpAU = $data->dp; 
        $this->reg_dateAU = $data->reg_date; 
        $this->new_reg_dateAU = null;      
        $this->formAdd = false;  
        $this->formUpdate = true; 
        $this->dispatch('showModal');
    }

    public function clearVariable(){
        $this->stationIdAU = null; 
        $this->idAU = null;        
        $this->formUpdate = false; 
        $this->formAdd = false;     
        $this->stationIdAU = null;
        $this->v10mAU = null;
        $this->v10mdirAU = null;
        $this->precAU = null;
        $this->capeAU = null;
        $this->liAU = null;
        $this->t2mAU = null;
        $this->hr2mAU = null;
        $this->baroAU = null; 
        $this->cloudAU = null;
        $this->snowAU = null;  
        $this->dpAU = null;
        $this->reg_dateAU = null;
        $this->new_reg_dateAU = null;
    }

    public function delete($id){
        DB::beginTransaction();
        try {
            ForecastM::where('id',$id)->delete();                
            DB::commit();
            $this->dispatch('successAlert',[ 'success' => 'Eliminación exitosa!' ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th);
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo Eliminar. Intente mas tarde' ]);              
        }
    }

    public function changeStatus($id,$estadoActual){
        DB::beginTransaction();
        try {
            ForecastM::where('id','=',$id)->update(['state'=> $estadoActual==1?0:1]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo cambiar el estado. Intente mas tarde.' ]);                    
        }                    
    }

    public function updateCell($rowId, $column, $newValue)
    {
        // Buscar el modelo de la fila correspondiente
        $forecast = ForecastM::find($rowId);        
        
        dd($rowId, $column, $newValue, $forecast);
        if ($forecast) {
            // Actualizamos el valor de la columna según el nombre
            // Usamos `str_replace` para quitar el prefijo 'col_' y obtener el nombre de la columna
            $columnName = str_replace('col_', '', $column);

            // Actualizamos la propiedad correspondiente
            $forecast->$columnName = $newValue;

            // Guardamos el cambio en la base de datos
            $forecast->save();
        }
    }


    public function viewData(){
        
        if(trim($this->searchStation)!=''){
            $this->data = [];
            $this->data = StationM::join('forecasts as f','f.station_id','stations.id')->where('stations.name','Like', '%'.$this->searchStation . '%')->select('f.*')->get();
        }        
    }
    
    public function render(){
        return view('livewire.forecasts.forecasts');
    }
}

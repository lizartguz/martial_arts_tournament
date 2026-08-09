<?php

namespace App\Livewire\Station;

use DateTime;
use DateInterval;
use App\Utils\Utils;
use App\Models\AlertM;
use Livewire\Component;
use App\Models\StationM;
use App\Models\StationCalibrationM;
use App\Models\ForecastM;
use App\Models\ProvinceM;
use App\Models\DepartmentM;
use App\Models\CurrentDataM;
use App\Models\TranslationM;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\MunicipalityM;
use Livewire\WithFileUploads;
use App\Models\BrandM;
use App\Models\StationModelM;
use App\Models\DetailStationM;
use Illuminate\Support\Carbon;
use App\Models\UserDependencyM;
use App\Models\MaintenancePlanM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthValidationTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomizeSensorStationM;
use App\Support\ErrorMessage;
use Illuminate\Support\Facades\Storage;

class Station extends Component{

    use WithFileUploads;
    use WithPagination;
    use AuthValidationTrait;

    public const DEFAULT_IMAGE_PATH = 'imgpf/logo.jpg';

    protected function resetImageDefaults(): void
    {
        $this->image1linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image2linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image3linkDT = self::DEFAULT_IMAGE_PATH;
        $this->imagebeforelink = self::DEFAULT_IMAGE_PATH;
        $this->imageafterlink = self::DEFAULT_IMAGE_PATH;
    }

    public $headers;
    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';
    public $viewType = 'table';
    public $viewlist = true;
    public $perPage = 10;
    public $search = null;
    public $searchLocation = null;

    //Graph variable:
    public $labels = [];
    public $formatType = null;
    public $selectedFromDate = null;
    public $selectedToDate = null;
    public $variable = null;
    public $varStationId = null;
    public $chartData = [];

    //Forecasts variable: (Ahora se manejan en ForecastViewer)
    public $selectedStationIdForForecast = null;


    //ALERTS
    public $dataAlerts = [];
    public $nameStationAlert = null;


    public $sortColumn = 'id';
    public $sortDirection = 'desc';

    public $failedAlert = null;

    public $stations = [];
    public $stationId = null;
    public $stationName = null;
    public $idAU = null;

    public $state=1;
    public $stateAU = 1;
    public $name=null;

    public $formAdd = false;
    public $formUpdate = false;
    public $dataForSteelseries = null;

    //STATION FORM
    public $nameAU = null;
    public $codeAU = null;

    public $placeAU = null;
    public $locationAU = null;
    public $departmentAU = [];
    public $departmentIdAU = -1;
    public $provinceAU = [];
    public $municipalityAU = [];
    public $provinceIdAU = -1;
    public $municipalityIdAU = -1;
    public $municipalityNameAU = -1;
    public $latitudeAU = null;
    public $longitudeAU = null;
    public $typeInputIdAU = null;
    

    //ADD AND UPDATE DetailStationM
    public $equipmentDT = null;
    public $brandDT = null;
    public $power_supplyDT = -1;

    // Catálogo marca → modelo (selects en cascada)
    public $brandsListDT = [];
    public $modelsListDT = [];
    public $brandIdDT = -1;
    public $stationModelIdDT = -1;
    
    //public $reception_typeDT = -1;
    public $packageDT = null;
    public $codeDT = null;
    public $modelDT = -1;
    public $installation_dateDT = null;
    public $to_hour_dayDT = null;
    public $to_cvDT = null;
    public $elevationDT = null;
    public $installer_user_idDT = null;
    public $station_idDT = null;
    public $stateDT = 1;
    public $image1DT = null;
    public $image2DT = null;
    public $image3DT = null;

    public $image1linkDT;
    public $image2linkDT;
    public $image3linkDT;
    protected $listeners = ['delete','deleteStation','addStation','updateStation','registerFormMaintanancePlan', 'resetStationCalibrationModal'];


    //------- maintenance plan --------
    public $viewMaintenancePlan = false;
    public $stationNameMP = null;
    public $locationMP = null;  
    public $reg_dateMP = null;
    public $stationIdMP = null;
    public $viewFormMaintenancePlanAdd = false;
    public $viewFormMaintenancePlanUpdate = false;

    public $maintenanceDateFilter = null;
    public $workOrderFilter = null;
    public $deliveryNoteFilter = null;
    public $maintenanceTypeNoteFilter = -1;
    public $paginationMP = 25;
    public $dataResponse = [];
    public array $txChannelOptions = [];
    public array $optionsYesNo = ['YES', 'NO'];
    public array $optionsYesNoNa = ['YES', 'NO', 'NA'];
    public array $optionsSiNo = ['SI', 'NO'];
    public array $optionsSiNoNa = ['SI', 'NO', 'NA'];
    public $image_before = null;
    public $image_after = null;
    public $imagebeforelink;
    public $imageafterlink;
    public $recommendations = null;
    public $preventiveMaintenanceFlag = false;

    // ------- station calibrations --------
    public $showCalibrationModal = false;
    public $stationCalibrationStationId = null;
    public $stationCalibrationStationName = null;
    public $stationCalibrationLocation = null;
    public $stationCalibrationRegDate = null;
    public $stationCalibrationRows = [];
    public $viewStationCalibrationForm = false;
    public $editStationCalibrationForm = false;
    public $stationCalibrationId = null;
    public $stationCalibrationVariableName = null;
    public $stationCalibrationKey = null;
    public $stationCalibrationParamsJson = null;
    public $stationCalibrationIsActive = 1;
    public array $stationCalibrationVariableOptions = [];
    public array $stationCalibrationAvailableVariables = [];


    // -------- maintenance plan Form Add and Update --------
    public $idMPU = null;
    public $nextMaintenanceDate = null;
    public $maintenanceDate = null;
    public $workOrder = null;
    public $deliveryNote = null;
    public $descriptionMaintenance = null;
    public $preventiveMaintenance = -1;
    public $correctiveMaintenance = -1;
    public $image_signatureU = null;

    public $dataSteelSeries= null;
    public $markerLat = 40.730610;
    public $markerLng = -73.935242;
    public $markerColor = 'red';
    
    public $typeS = null;
    public $isTranslation = -1;
    public $listTranslation = [];
    public $userVisibilityMapInternal = true;

    // optionLabelMap se entrega vía render() (datos estáticos) para no serializarlo
    // como estado público en cada request de Livewire.

    function mount($typeS = null){
        $this->typeS = $typeS;
        $user = Auth::user();
        if ($user->hasRole(['manager', 'subscriber'])) {
           $this->userVisibilityMapInternal = false;
        }
        $this->validarUsuario($user, []);
        abort_unless($user?->can('stations.table.view'), 403);
        $this->selectedFromDate = now()->toDateString(); 
        $this->selectedToDate = now()->toDateString();    
        
        $this->txChannelOptions = range(1, 8);
        $this->stationCalibrationVariableOptions = $this->buildStationCalibrationVariableOptions();
        $this->stationCalibrationAvailableVariables = $this->stationCalibrationVariableOptions;
        $this->resetImageDefaults();
    }

    /**
     * Genera un UUID (Universally Unique Identifier) para la estación
     * Formato: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
     */
    public function generateStationCode()
    {
        $this->codeAU = (string) \Illuminate\Support\Str::uuid();
    }

    public function openModalSteelSeries($stationId){

        //dd($stationId);
        date_default_timezone_set('America/La_Paz');
        $this->selectedFromDate=date('Y-m-d');
        $this->selectedToDate=date('Y-m-d');
        $this->formatType = 'all';
        $this->varStationId = $stationId;
        $this->variable = 'tempout';
        
        $this->viewType = 'steelseries';
        $this->dataForSteelseries = StationM::select(
            'stations.id as stationId', 'stations.name', 'stations.location', 'stations.latitude', 
            'stations.longitude', 'stations.state as stateStation', 'dd.tempout','dd.humout', 
            'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press',
            'dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2',
            'dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2',
            'dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2',
            'dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date'
        )
        ->join('current_data as dd', 'dd.station_id', 'stations.id')
        ->where('stations.id',$this->varStationId)
        ->first();    
       
        $this->dataForSteelseries = collect($this->dataForSteelseries)->map(function ($value) {
            return $value ?? 'NA';
        });

        $this->viewGraph();
    }

    public function handleClick($variable){
        $this->variable = $variable;
        $this->viewGraph();
    }

    public function viewGraph(){ 

        if (empty($this->selectedFromDate) || empty($this->selectedToDate)) {
            $this->dispatch('failedAlert', ['failed' => __('messages.stations.map.more_data.viewgraph_msg_1')]);
            return;
        }    
       
        if ($this->selectedFromDate > $this->selectedToDate) {
            $this->dispatch('failedAlert', ['failed' => __('messages.stations.map.more_data.viewgraph_msg_2')]);
            return;
        }
        
        $fromDate = Carbon::parse($this->selectedFromDate)->startOfDay();
        $toDate = Carbon::parse($this->selectedToDate)->endOfDay();
    
        
        $monthsDifference = $fromDate->diffInMonths($toDate);
        if ($monthsDifference > 3) {
            $this->dispatch('failedAlert', ['failed' => __('messages.stations.map.more_data.viewgraph_msg_3')]);
            return;
        }
       
       if($this->formatType=="all"){        
        $this->getAllData();
       }else if($this->formatType=="daily"){
        $this->getDailyData();
       }else if($this->formatType=="monthly"){
        $this->getAllData();
       }
    }

    public function getAllData() {

        date_default_timezone_set('America/La_Paz');
        $fromDate = Carbon::parse($this->selectedFromDate)->startOfDay();
        $toDate = Carbon::parse($this->selectedToDate)->endOfDay();
        $year = $fromDate->format('Y');
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$year}")) {
        //     return ;
        // }
        // $records = DB::connection('db_guest_h' . $year)
        // ->table('data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$year}")) {
            return ;
        }

        $records = DB::connection('senvatec_db_' . $year)
        ->table('senva_data')
        ->select('station_id', 'receipt_date', $this->variable)
        ->where('station_id', $this->varStationId)
        ->whereBetween('receipt_date', [$fromDate, $toDate])
        ->orderBy('receipt_date', 'asc')
        ->get();        
        
        $titleVariable = '';
        switch($this->variable){ 
            case 'tempout':
                $titleVariable = __('messages.stations.map.more_data.temperature');
            break;
            case 'humout':
                $titleVariable = __('messages.stations.map.more_data.humidity');
            break;
            case 'raintotal':
                $titleVariable = __('messages.stations.map.more_data.precipitation');
            break;
            case 'dewptout':
                $titleVariable = __('messages.stations.map.more_data.dew_point');
            break;
            case 'winddir':
                $titleVariable = __('messages.stations.map.more_data.wind_direction');
            break;
            case 'windspeed':
                $titleVariable = __('messages.stations.map.more_data.wind_direction');
            break;
            case 'uvindex':
                $titleVariable = __('messages.stations.map.more_data.uv_rays');
            break;
            case 'press':
                $titleVariable = __('messages.stations.map.more_data.barometric_pressure');
            break;
            case 'solevo':
                $titleVariable = __('messages.stations.map.more_data.evapotranspiration');
            break;
            case 'soiltemp1':
                $titleVariable = __('messages.stations.map.more_data.soil_temperature');
            break;
            case 'soilhum1':
                $titleVariable = __('messages.stations.map.more_data.soil_moisture');
            break;
            case 'leafhum1':
                $titleVariable = __('messages.stations.map.more_data.foliar_moisture');
            break;
        }
        $this->chartData = [
            'title' => $titleVariable,
            'labels' => $records->pluck('receipt_date')->unique()->values()->toArray(),
            'data' => $records->pluck($this->variable)->toArray()
        ]; 

        $this->dispatch('updateChart', $this->chartData);
    }


    public function getDailyData() {
        
        $fromDate = Carbon::parse($this->selectedFromDate);
        $toDate = Carbon::parse($this->selectedToDate);
        $year = $fromDate->format('Y');
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$year}")) {
        //     return ;
        // }
        // $records = DB::connection('db_guest_h'.$year)
        //     ->table('daily_data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$year}")) {
            return ;
        }

        $records = DB::connection('senvatec_db_'.$year)
            ->table('senva_daily_data')
            ->select('station_id', 'registration_date', $this->variable)
            ->where('station_id', $this->varStationId)
            ->whereBetween('registration_date', [$fromDate, $toDate])
            ->orderBy('registration_date', 'asc')
            ->get();
            $titleVariable = '';
            switch($this->variable){
                case 'tempout':
                $titleVariable = __('messages.stations.map.more_data.temperature');
                break;
                case 'humout':
                    $titleVariable = __('messages.stations.map.more_data.humidity');
                break;
                case 'raintotal':
                    $titleVariable = __('messages.stations.map.more_data.precipitation');
                break;
                case 'dewptout':
                    $titleVariable = __('messages.stations.map.more_data.dew_point');
                break;
                case 'winddir':
                    $titleVariable = __('messages.stations.map.more_data.wind_direction');
                break;
                case 'windspeed':
                    $titleVariable = __('messages.stations.map.more_data.wind_direction');
                break;
                case 'uvindex':
                    $titleVariable = __('messages.stations.map.more_data.uv_rays');
                break;
                case 'press':
                    $titleVariable = __('messages.stations.map.more_data.barometric_pressure');
                break;
                case 'solevo':
                    $titleVariable = __('messages.stations.map.more_data.evapotranspiration');
                break;
                case 'soiltemp1':
                    $titleVariable = __('messages.stations.map.more_data.soil_temperature');
                break;
                case 'soilhum1':
                    $titleVariable = __('messages.stations.map.more_data.soil_moisture');
                break;
                case 'leafhum1':
                    $titleVariable = __('messages.stations.map.more_data.foliar_moisture');
                break;
            }  
            //dd($records,$this->variable,$this->varStationId,$fromDate,$toDate);          
            $this->chartData = [
                'title' => $titleVariable,
                'labels' => $records->pluck('registration_date')->unique()->values()->toArray(),
                'data' => $records->pluck($this->variable)->toArray()
            ]; 
    
            $this->dispatch('updateChart', $this->chartData);
    }

    



    public function returnToList(){
        $this->viewType = "table";
        $this->viewlist = true;
        $this->dataForSteelseries = null;
        $this->selectedStationIdForForecast = null; // Limpiar estación de pronóstico
        $this->formAdd = false;
        $this->formUpdate = false;
    }

    //*********************************FORECASTS **************************/
    // SIMPLIFICADO: Abre modal sin cambiar la vista

    public function openModalForecasts($stationId){
        // Solo configurar la estación y abrir el modal
        $this->selectedStationIdForForecast = $stationId;
        $this->varStationId = $stationId;
        
        // Abrir modal sin modificar la vista principal (se mantiene la tabla)
        $this->dispatch('openForecastModal', [
            'stationId'   => $stationId,
            'stationName' => StationM::where('id', $stationId)->value('name'),
        ]);
    }    

    public function openAlertModal($stationId)
    {
        $this->dispatch('openStationInfoModal', $stationId);
    }

    function pPrec($prec) {
		$cadena = "";
		if (($prec<=0.55)) { $cadena = $cadena . '0%';}
		if (($prec>0.55)&&($prec<=1.5)) { $cadena = $cadena . '70%';}
		if (($prec>1.5)&&($prec<=3.1)) { $cadena = $cadena . '80%';}
		if (($prec>3.1)&&($prec<=5.1)) { $cadena = $cadena . '90%';}
		if (($prec>5.1)&&($prec<=8.1)) { $cadena = $cadena . '95%';}
		if (($prec>8.1)) { $cadena = $cadena . '99%';}
		return $cadena;
	}
	
	function pPrecTXT($prec) {
		$cadena = "";
		if (($prec<=0.55)) { $cadena = $cadena . '';}
		if (($prec>0.55)&&($prec<=0.88)) { $cadena = $cadena . __('messages.stations.map.forecasts.pPrecTXT_1');}
		if (($prec>0.88)&&($prec<=3.1)) { $cadena = $cadena . __('messages.stations.map.forecasts.pPrecTXT_2');}
		if (($prec>3.1)&&($prec<=5)) { $cadena = $cadena . __('messages.stations.map.forecasts.pPrecTXT_3');}
		if (($prec>5)&&($prec<=8)) { $cadena = $cadena . __('messages.stations.map.forecasts.pPrecTXT_4');}
		if (($prec>8)&&($prec<=10)) { $cadena = $cadena . __('messages.stations.map.forecasts.pPrecTXT_5');}
		if (($prec>10)) { $cadena = $cadena . __('messages.stations.map.forecasts.pPrecTXT_5');}
		return $cadena;
	}

    public function getPeriodRange($dateTime){
        date_default_timezone_set('America/La_Paz');

        $time = Carbon::parse($dateTime)->format('H');
        if ($time >= 0 && $time <= 2) {
            return '00:00 - 02:00';
        } elseif ($time >= 3 && $time <= 5) {
            return '03:00 - 05:00';
        } elseif ($time >= 6 && $time <= 8) {
            return '06:00 - 08:00';
        } elseif ($time >= 9 && $time <= 11) {
            return '09:00 - 11:00';
        } elseif ($time >= 12 && $time <= 14) {
            return '12:00 - 14:00';
        } elseif ($time >= 15 && $time <= 17) {
            return '15:00 - 17:00';
        } elseif ($time >= 18 && $time <= 20) {
            return '18:00 - 20:00';
        } elseif ($time >= 21 && $time <= 23) {
            return '21:00 - 23:00';
        } else {
            return '';
        }
    }

    public function getPeriod($dateTime)
    {
        date_default_timezone_set('America/La_Paz');
        $time = Carbon::parse($dateTime)->format('H');

        if ($time >= 0 && $time < 6) {
            return __('messages.stations.periods.early_morning');
        } elseif ($time >= 6 && $time <= 11) {
            return __('messages.stations.periods.morning');
        } elseif ($time >= 12 && $time < 13) {
            return __('messages.stations.periods.midday');
        } elseif ($time >= 13 && $time <= 18) {
            return __('messages.stations.periods.afternoon');
        } elseif ($time >= 19 && $time <= 23) {
            return __('messages.stations.periods.night');
        } else {
            return '';
        }
    }

    //*********************************  ALERTS **************************/
    public function getAlerts($stationId){
        $this->viewType = "alerts";
        $this->varStationId = $stationId;
        $this->nameStationAlert = StationM::where('id',$stationId)->value('name');
        $this->dataAlerts = AlertM::where('station_id',$stationId)->get();

    }
    
    //********************************* MAINTENANCEPLAN **************************/
    public function setMaintenancePlan($id){
        $this->image1linkDT = asset(self::DEFAULT_IMAGE_PATH);
        $this->image2linkDT = asset(self::DEFAULT_IMAGE_PATH);
        $this->image3linkDT = asset(self::DEFAULT_IMAGE_PATH);
        $this->resetImageDefaults();
        $this->image_before = null;
        $this->image_after = null;
        $this->stationIdMP = $id;
        $stationItemMP = StationM::where('id','=',$id)->first();
        //dd($stationItemMP->weatherStation);
        //dd($stationItemMP);
        if($stationItemMP!=null){
            $this->stationNameMP = $stationItemMP->name;
            $this->locationMP = 'LAT:'.$stationItemMP->latitude .'  LON:'.$stationItemMP->longitude;            
            $this->reg_dateMP = $stationItemMP->reg_date;            

            $this->closedUpdate();
            $this->formAdd = false;
            $this->viewlist = false;
            $this->formUpdate = false;
            $this->viewMaintenancePlan = true;
            
        }
    }

    public function setFormMaintanancePlan(){

        $this->nextMaintenanceDate = null;
        $this->maintenanceDate = date('Y-m-d');
        $this->workOrder = null;
        $this->deliveryNote = null;
        $this->descriptionMaintenance = null;
        $this->preventiveMaintenance = -1;
        $this->correctiveMaintenance = -1;
        $this->viewFormMaintenancePlanAdd = true;
        $this->viewFormMaintenancePlanUpdate = false;
        $tiempoUnix = strtotime($this->maintenanceDate);
        $nuevaFechaTiempoUnix = strtotime('+1 year', $tiempoUnix);
        $this->nextMaintenanceDate = date('Y-m-d', $nuevaFechaTiempoUnix);
      
    }

    public function setFormMaintanancePlanU($id){
        $data = MaintenancePlanM::where('id',$id)->first();
        $this->idMPU = $id;
        $this->nextMaintenanceDate = $data->next_maintenance_date;
        $this->maintenanceDate = $data->maintenance_date;
        $this->workOrder = $data->work_order_number;
        $this->deliveryNote = $data->delivery_note_number;
        $this->descriptionMaintenance = $data->description;
        $this->recommendations = $data->recommendations;
        $this->preventiveMaintenance = $data->preventive_maintenance;
        $this->correctiveMaintenance = $data->corrective_maintenance;        
        $this->image_before = null;
        $this->image_after = null;

        $this->imagebeforelink = $data->image_before;
        $this->imageafterlink = $data->image_after;
        $this->image_signatureU = $data->image_signature;

        $this->stationNameMP = $data->station->name;
        $this->locationMP = 'LAT:'.$data->station->latitude .'  LON:'.$data->station->longitude;            
        $this->reg_dateMP = $data->station->reg_date; 
        $this->stationIdMP = $data->station_id; 
        
        $this->viewFormMaintenancePlanAdd = false;
        $this->viewFormMaintenancePlanUpdate = true;   
    }

    public function loadCanvas(){       
        $this->dispatch('set-signature-pad',['response'=> '']);
    }

    public function closeFormMaintanancePlan(){
        $this->nextMaintenanceDate = null;
        $this->maintenanceDate = null;
        $this->workOrder = null;
        $this->deliveryNote = null;
        $this->descriptionMaintenance = null;
        $this->preventiveMaintenance = -1;
        $this->correctiveMaintenance = -1;
        $this->image_before = null;
        $this->image_after = null;
        $this->viewFormMaintenancePlanAdd = false;
        $this->viewFormMaintenancePlanUpdate = false;
    }

    public function registerFormMaintanancePlan($signatureData){
        
        $flag = $this->validateMaintenancePlan();
        if(!$flag){
            try{
                DB::beginTransaction();
                $maintenance = MaintenancePlanM::create([
                    'maintenance_date' => $this->maintenanceDate,
                    'next_maintenance_date'=> $this->nextMaintenanceDate,
                    'delivery_note_number' =>  $this->deliveryNote,
                    'work_order_number' => $this->workOrder,
                    'preventive_maintenance' => $this->preventiveMaintenance,
                    'corrective_maintenance' => $this->correctiveMaintenance,
                    'description' => $this->descriptionMaintenance,
                    'recommendations' => $this->recommendations, 
                    'image_before' => null, 
                    'image_after' => null,                    
                    'state' => 1,
                    'user_id'=> Auth::user()->id,
                    'station_id'=> $this->stationIdMP,
                ]);

                if (!Storage::disk('public')->exists("imgmaintenance/$this->idAU")) {
                    Storage::disk('public')->makeDirectory("imgmaintenance/$this->idAU");
                }

                if($this->image_before!=null){
                    $dateCurrentTime = date('Y-m-d H:i:s');
                    $timestamp = strtotime($dateCurrentTime);
                    $nameImage = uniqid($timestamp).'.'.$this->image_before->getClientOriginalExtension();
                    $ruta = $this->image_before->storeAs('imgmaintenance/'.$maintenance->id, $nameImage,'public');
                    $url = str_replace("public/", "", $ruta);
                    MaintenancePlanM::where('id',$maintenance->id)
                    ->update(['image_before' => $url]);
                }

                if($this->image_after!=null){
                    $dateCurrentTime = date('Y-m-d H:i:s');
                    $timestamp = strtotime($dateCurrentTime);
                    $nameImage = uniqid($timestamp).'.'.$this->image_after->getClientOriginalExtension();
                    $ruta = $this->image_after->storeAs('imgmaintenance/'.$maintenance->id, $nameImage,'public');
                    $url = str_replace("public/", "", $ruta);
                    MaintenancePlanM::where('id',$maintenance->id)
                    ->update(['image_after' => $url]);
                }

                if($signatureData['signatureData']!=null){
                    $data_uri = $signatureData['signatureData'];
                    $encoded_image = explode(",", $data_uri)[1];
                    $dataSignature = base64_decode($encoded_image);
                    $nameImage = uniqid() . '_signature.png';
                    Storage::disk('public')->put('imgmaintenance/'.$maintenance->id.'/'.$nameImage, $dataSignature);
                    MaintenancePlanM::where('id',$maintenance->id)
                    ->update(['image_signature' => 'imgmaintenance/'.$maintenance->id.'/'.$nameImage]);
                }

                DB::commit();
                $this->closeFormMaintanancePlan();
                $this->dispatch('successAlert',[ 'success' =>  __('messages.stations.maintananceplan.msg_success')  ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.maintenance_create')) ]);
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedAlert ]);
        }
    }

    public function updateFormMaintanancePlan(){
        $flag = $this->validateMaintenancePlanU();
        if(!$flag){
            try{
                DB::beginTransaction();
                MaintenancePlanM::where('id',$this->idMPU)
                ->update([
                    'maintenance_date' => $this->maintenanceDate,
                    'next_maintenance_date'=> $this->nextMaintenanceDate,
                    'delivery_note_number' =>  $this->deliveryNote,
                    'work_order_number' => $this->workOrder,
                    'preventive_maintenance' => $this->preventiveMaintenance,
                    'corrective_maintenance' => $this->correctiveMaintenance,
                    'description' => $this->descriptionMaintenance,
                    'recommendations' => $this->recommendations,                          
                    'modified_by_user_id'=> Auth::user()->id,                    
                ]);

                if($this->image_before!=null){
                    $dateCurrentTime = date('Y-m-d H:i:s');
                    $timestamp = strtotime($dateCurrentTime);
                    $nameImage = uniqid($timestamp).'.'.$this->image_before->getClientOriginalExtension();
                    $ruta = $this->image_before->storeAs('imgmaintenance/'.$this->idMPU, $nameImage, 'public');
                    $url = str_replace("public/", "", $ruta);
                    MaintenancePlanM::where('id',$this->idMPU)
                    ->update(['image_before' => $url]);
                }
                if($this->image_after!=null){
                    $dateCurrentTime = date('Y-m-d H:i:s');
                    $timestamp = strtotime($dateCurrentTime);
                    $nameImage = uniqid($timestamp).'.'.$this->image_after->getClientOriginalExtension();
                    $ruta = $this->image_after->storeAs('imgmaintenance/'.$this->idMPU, $nameImage, 'public');
                    $url = str_replace("public/", "", $ruta);
                    MaintenancePlanM::where('id',$this->idMPU)
                    ->update(['image_after' => $url]);
                }
                DB::commit();
                $this->closeFormMaintanancePlan();
                $this->dispatch('successAlert',[ 'success' => __('messages.stations.maintananceplan.msg_updated')  ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.maintenance_update')) ]);
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedAlert ]);
        }
    }

    private function validateMaintenancePlan(){
        $flag = false;
        $this->failedAlert = '';
        $count = MaintenancePlanM::where('maintenance_date',$this->maintenanceDate)
        ->where('station_id',$this->stationIdMP)->count();
        if(MaintenancePlanM::where('maintenance_date',$this->maintenanceDate)
        ->where('station_id',$this->stationIdMP)->count() < 2){
            if ($this->maintenanceDate != null) {
                if ($this->nextMaintenanceDate != null) {
                    if (MaintenancePlanM::where('work_order_number', '=', $this->workOrder)
                        ->where('station_id', $this->stationIdMP)->doesntExist()) {
                        if ($this->workOrder != null) {
                            if (MaintenancePlanM::where('delivery_note_number', '=', $this->deliveryNote)
                                ->where('station_id', $this->stationIdMP)->doesntExist()) {
                                if ($this->deliveryNote != null) {
                                    if ($this->preventiveMaintenance != -1) {
                                        if ($this->correctiveMaintenance != -1) {
                                            if ($this->descriptionMaintenance != null && $this->descriptionMaintenance != '') {
                                                if ($this->image_before != null) {
                                                    if ($this->image_after == null) {
                                                        $flag = true;
                                                        $this->failedAlert = __('messages.stations.maintananceplan.validate_1');
                                                    }
                                                } else {
                                                    $flag = true;
                                                    $this->failedAlert = __('messages.stations.maintananceplan.validate_2');
                                                }
                                            } else {
                                                $flag = true;
                                                $this->failedAlert = __('messages.stations.maintananceplan.validate_3');
                                            }
                                        } else {
                                            $flag = true;
                                            $this->failedAlert = __('messages.stations.maintananceplan.validate_4');
                                        }
                                    } else {
                                        $flag = true;
                                        $this->failedAlert = __('messages.stations.maintananceplan.validate_5');
                                    }
                                } else {
                                    $flag = true;
                                    $this->failedAlert = __('messages.stations.maintananceplan.validate_6');
                                }
                            } else {
                                $flag = true;
                                $this->failedAlert = __('messages.stations.maintananceplan.validate_7');
                            }
                        } else {
                            $flag = true;
                            $this->failedAlert = __('messages.stations.maintananceplan.validate_8');
                        }
                    } else {
                        $flag = true;
                        $this->failedAlert = __('messages.stations.maintananceplan.validate_9');
                    }
                } else {
                    $flag = true;
                    $this->failedAlert = __('messages.stations.maintananceplan.validate_10');
                }
            } else {
                $flag = true;
                $this->failedAlert = __('messages.stations.maintananceplan.validate_11');
            }
        }else{
            $flag = true;
            $this->failedAlert =  __('messages.stations.maintananceplan.validate_12_1').$count.__('messages.stations.maintananceplan.validate_12_2').$this->maintenanceDate. __('messages.stations.maintananceplan.validate_12_3');
        }

        return $flag;
    }

    private function validateMaintenancePlanU(){
        $flag = false;
        $this->failedAlert = '';        
        if ($this->maintenanceDate != null) {
            if ($this->nextMaintenanceDate != null) {
                if (MaintenancePlanM::where('work_order_number', '=', $this->workOrder)
                    ->where('id', '!=', $this->idMPU)->doesntExist()) {
                    if ($this->workOrder != null) {
                        if (MaintenancePlanM::where('delivery_note_number', '=', $this->deliveryNote)
                            ->where('id', '!=', $this->idMPU)->doesntExist()) {
                            if ($this->deliveryNote != null) {
                                if ($this->preventiveMaintenance != -1) {
                                    if ($this->correctiveMaintenance != -1) {
                                        if ($this->descriptionMaintenance != null && $this->descriptionMaintenance != '') {
                                            // Código adicional si es necesario
                                        } else {
                                            $flag = true;
                                            $this->failedAlert = __('messages.stations.maintananceplan.validateU_1');
                                        }
                                    } else {
                                        $flag = true;
                                        $this->failedAlert = __('messages.stations.maintananceplan.validateU_2');
                                    }
                                } else {
                                    $flag = true;
                                    $this->failedAlert = __('messages.stations.maintananceplan.validateU_3');
                                }
                            } else {
                                $flag = true;
                                $this->failedAlert = __('messages.stations.maintananceplan.validateU_4');
                            }
                        } else {
                            $flag = true;
                            $this->failedAlert = __('messages.stations.maintananceplan.validateU_5');
                        }
                    } else {
                        $flag = true;
                        $this->failedAlert = __('messages.stations.maintananceplan.validateU_6');
                    }
                } else {
                    $flag = true;
                    $this->failedAlert = __('messages.stations.maintananceplan.validateU_7');
                }
            } else {
                $flag = true;
                $this->failedAlert = __('messages.stations.maintananceplan.validateU_8');
            }
        } else {
            $flag = true;
            $this->failedAlert = __('messages.stations.maintananceplan.validateU_9');
        }
        

        return $flag;
    }

    public function deleteMaintenancePlan($id){
        DB::beginTransaction();
        try {
            MaintenancePlanM::where('id',$id)->delete();
            DB::commit();
            $this->dispatch('successAlert',[ 'success' => __('messages.stations.maintananceplan.msg_delete_success') ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.stations.maintananceplan.msg_delete_failed') ]);
        }
    }

    //********************************* CALIBRATIONS **************************/
    public function openStationCalibrationModal($id)
    {
        $stationItem = StationM::where('id', '=', $id)->first();

        if ($stationItem === null) {
            $this->dispatch('failedAlert', ['failed' => __('messages.stations.calibration.messages.station_not_found')]);
            return;
        }

        $this->stationCalibrationStationId = $stationItem->id;
        $this->stationCalibrationStationName = $stationItem->name;
        $this->stationCalibrationLocation = 'LAT:' . $stationItem->latitude . '  LON:' . $stationItem->longitude;
        $this->stationCalibrationRegDate = $stationItem->reg_date;
        $this->closeStationCalibrationForm();
        $this->refreshStationCalibrations();

        $this->showCalibrationModal = true;
    }

    public function setStationCalibrationFormAdd()
    {
        if ($this->stationCalibrationStationId === null) {
            return;
        }

        $this->closeStationCalibrationForm();
        $this->stationCalibrationAvailableVariables = $this->getStationCalibrationAvailableVariables();

        if (count($this->stationCalibrationAvailableVariables) === 0) {
            $this->dispatch('failedAlert', ['failed' => __('messages.stations.calibration.messages.all_variables_taken')]);
            return;
        }

        $this->viewStationCalibrationForm = true;
        $this->editStationCalibrationForm = false;
    }

    public function setStationCalibrationFormEdit($id)
    {
        $item = StationCalibrationM::where('station_id', $this->stationCalibrationStationId)
            ->where('id', $id)
            ->first();

        if ($item === null) {
            $this->dispatch('failedAlert', ['failed' => __('messages.stations.calibration.messages.record_not_found')]);
            return;
        }

        $this->viewStationCalibrationForm = true;
        $this->editStationCalibrationForm = true;
        $this->stationCalibrationId = $item->id;
        $this->stationCalibrationVariableName = $item->variable_name;
        $this->stationCalibrationKey = $item->calibration_key;
        $this->stationCalibrationIsActive = (int) $item->is_active;
        $this->stationCalibrationParamsJson = empty($item->calibration_params)
            ? null
            : json_encode($item->calibration_params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->stationCalibrationAvailableVariables = $this->stationCalibrationVariableOptions;
    }

    public function saveStationCalibration()
    {
        $flag = $this->validateStationCalibrationForm(false);

        if ($flag) {
            $this->dispatch('failedAlert', ['failed' => $this->failedAlert]);
            return;
        }

        try {
            StationCalibrationM::create([
                'station_id' => $this->stationCalibrationStationId,
                'variable_name' => $this->stationCalibrationVariableName,
                'calibration_key' => trim($this->stationCalibrationKey),
                'calibration_params' => $this->normalizeStationCalibrationParams(),
                'is_active' => (int) $this->stationCalibrationIsActive,
            ]);

            $this->refreshStationCalibrations();
            $this->closeStationCalibrationForm();
            $this->dispatch('successAlert', ['success' => __('messages.stations.calibration.messages.saved')]);
        } catch (\Throwable $th) {
            $this->dispatch('failedAlert', ['failed' => ErrorMessage::forUser($th, __('messages.operation_errors.calibration_save'))]);
        }
    }

    public function updateStationCalibration()
    {
        $flag = $this->validateStationCalibrationForm(true);

        if ($flag) {
            $this->dispatch('failedAlert', ['failed' => $this->failedAlert]);
            return;
        }

        try {
            StationCalibrationM::where('id', $this->stationCalibrationId)
                ->where('station_id', $this->stationCalibrationStationId)
                ->update([
                    'calibration_key' => trim($this->stationCalibrationKey),
                    'calibration_params' => $this->normalizeStationCalibrationParams(),
                    'is_active' => (int) $this->stationCalibrationIsActive,
                ]);

            $this->refreshStationCalibrations();
            $this->closeStationCalibrationForm();
            $this->dispatch('successAlert', ['success' => __('messages.stations.calibration.messages.updated')]);
        } catch (\Throwable $th) {
            $this->dispatch('failedAlert', ['failed' => ErrorMessage::forUser($th, __('messages.operation_errors.calibration_update'))]);
        }
    }

    public function closeStationCalibrationForm()
    {
        $this->viewStationCalibrationForm = false;
        $this->editStationCalibrationForm = false;
        $this->stationCalibrationId = null;
        $this->stationCalibrationVariableName = null;
        $this->stationCalibrationKey = null;
        $this->stationCalibrationParamsJson = null;
        $this->stationCalibrationIsActive = 1;
        $this->stationCalibrationAvailableVariables = $this->getStationCalibrationAvailableVariables();
    }

    public function closeStationCalibrationModal()
    {
        $this->showCalibrationModal = false;
        $this->resetStationCalibrationModal();
    }

    public function resetStationCalibrationModal()
    {
        $this->stationCalibrationStationId = null;
        $this->stationCalibrationStationName = null;
        $this->stationCalibrationLocation = null;
        $this->stationCalibrationRegDate = null;
        $this->stationCalibrationRows = [];
        $this->closeStationCalibrationForm();
    }

    private function validateStationCalibrationForm(bool $isUpdate): bool
    {
        $this->failedAlert = '';

        if ($this->stationCalibrationStationId === null) {
            $this->failedAlert = __('messages.stations.calibration.messages.station_not_found');
            return true;
        }

        if (!$isUpdate && ($this->stationCalibrationVariableName === null || $this->stationCalibrationVariableName === '')) {
            $this->failedAlert = __('messages.stations.calibration.messages.variable_required');
            return true;
        }

        if ($this->stationCalibrationKey === null || trim($this->stationCalibrationKey) === '') {
            $this->failedAlert = __('messages.stations.calibration.messages.key_required');
            return true;
        }

        if (!$isUpdate && StationCalibrationM::where('station_id', $this->stationCalibrationStationId)
            ->where('variable_name', $this->stationCalibrationVariableName)
            ->exists()) {
            $this->failedAlert = __('messages.stations.calibration.messages.variable_already_exists');
            return true;
        }

        if ($this->stationCalibrationParamsJson !== null && trim($this->stationCalibrationParamsJson) !== '') {
            json_decode($this->stationCalibrationParamsJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->failedAlert = __('messages.stations.calibration.messages.invalid_params_json');
                return true;
            }
        }

        return false;
    }

    private function normalizeStationCalibrationParams(): ?array
    {
        if ($this->stationCalibrationParamsJson === null || trim($this->stationCalibrationParamsJson) === '') {
            return null;
        }

        return json_decode($this->stationCalibrationParamsJson, true);
    }

    private function refreshStationCalibrations(): void
    {
        if ($this->stationCalibrationStationId === null) {
            $this->stationCalibrationRows = [];
            $this->stationCalibrationAvailableVariables = $this->stationCalibrationVariableOptions;
            return;
        }

        $this->stationCalibrationRows = StationCalibrationM::where('station_id', $this->stationCalibrationStationId)
            ->orderBy('variable_name')
            ->get();

        $this->stationCalibrationAvailableVariables = $this->getStationCalibrationAvailableVariables();
    }

    private function getStationCalibrationAvailableVariables(): array
    {
        $availableVariables = $this->stationCalibrationVariableOptions;

        if ($this->stationCalibrationStationId === null) {
            return $availableVariables;
        }

        $usedVariables = StationCalibrationM::where('station_id', $this->stationCalibrationStationId)
            ->pluck('variable_name')
            ->all();

        foreach ($usedVariables as $variableName) {
            if (!$this->editStationCalibrationForm || $variableName !== $this->stationCalibrationVariableName) {
                unset($availableVariables[$variableName]);
            }
        }

        return $availableVariables;
    }

    private function buildStationCalibrationVariableOptions(): array
    {
        return [
            'tempout' => 'Temperatura exterior (tempout)',
            'tempin' => 'Temperatura interior (tempin)',
            'humout' => 'Humedad exterior (humout)',
            'humin' => 'Humedad interior (humin)',
            'humoutmax' => 'Humedad exterior maxima (humoutmax)',
            'humoutmin' => 'Humedad exterior minima (humoutmin)',
            'dewptout' => 'Punto de rocio exterior (dewptout)',
            'dewptin' => 'Punto de rocio interior (dewptin)',
            'heatindexout' => 'Indice de calor exterior (heatindexout)',
            'press' => 'Presion barometrica (press)',
            'seapress' => 'Presion a nivel del mar (seapress)',
            'windavg' => 'Velocidad promedio del viento (windavg)',
            'winddir' => 'Direccion del viento (winddir)',
            'windchill' => 'Sensacion termica por viento (windchill)',
            'windspeed' => 'Velocidad del viento (windspeed)',
            'windspeedhi' => 'Rafaga maxima (windspeedhi)',
            'winddirhi' => 'Direccion de rafaga maxima (winddirhi)',
            'windspeed2' => 'Velocidad del viento sensor 2 (windspeed2)',
            'windspeedhi2' => 'Rafaga maxima sensor 2 (windspeedhi2)',
            'winddirhi2' => 'Direccion de rafaga maxima sensor 2 (winddirhi2)',
            'rainrate' => 'Tasa de lluvia (rainrate)',
            'raintotal' => 'Lluvia acumulada (raintotal)',
            'uvindex' => 'Indice UV (uvindex)',
            'solrad' => 'Radiacion solar (solrad)',
            'solradhi' => 'Radiacion solar maxima (solradhi)',
            'solevo' => 'Evapotranspiracion (solevo)',
            'pm1' => 'PM1 (pm1)',
            'pm2_5' => 'PM2.5 (pm2_5)',
            'pm10' => 'PM10 (pm10)',
            'gas_ppm' => 'Gas (ppm) (gas_ppm)',
            'soiltemp1' => 'Temperatura de suelo 1 (soiltemp1)',
            'soiltemp2' => 'Temperatura de suelo 2 (soiltemp2)',
            'soiltemp3' => 'Temperatura de suelo 3 (soiltemp3)',
            'soiltemp4' => 'Temperatura de suelo 4 (soiltemp4)',
            'soilhum1' => 'Humedad de suelo 1 (soilhum1)',
            'soilhum2' => 'Humedad de suelo 2 (soilhum2)',
            'soilhum3' => 'Humedad de suelo 3 (soilhum3)',
            'soilhum4' => 'Humedad de suelo 4 (soilhum4)',
            'leaftemp1' => 'Temperatura foliar 1 (leaftemp1)',
            'leaftemp2' => 'Temperatura foliar 2 (leaftemp2)',
            'leaftemp3' => 'Temperatura foliar 3 (leaftemp3)',
            'leaftemp4' => 'Temperatura foliar 4 (leaftemp4)',
            'leafhum1' => 'Humedad foliar 1 (leafhum1)',
            'leafhum2' => 'Humedad foliar 2 (leafhum2)',
            'leafhum3' => 'Humedad foliar 3 (leafhum3)',
            'leafhum4' => 'Humedad foliar 4 (leafhum4)',
        ];
    }

    //public function updateStation($location){
    public function updateStation(){    
        date_default_timezone_set('America/La_Paz');

        //$this->latitudeAU = $location['lat'];
        //$this->longitudeAU = $location['lng'];
        
        $flag = $this->validateStationUpdate();
        if(!$flag){
            try {

                DB::beginTransaction();
                               
                
                $nameDpto = DepartmentM::where('id','=', $this->departmentIdAU)->first(); 
                $nameProv = ProvinceM::where('id','=', $this->provinceIdAU)->value('name');
                $nameMun = MunicipalityM::where('id','=', $this->municipalityIdAU)->value('name');
                $location = $nameMun.', '.$nameProv.', '.$nameDpto->abbreviation;
                if($this->placeAU!==null){
                    $search_concatenation = '┆'.$this->nameAU.'┆'.$location.'┆'.$this->placeAU;
                }else{
                    $search_concatenation = '┆'.$this->nameAU.'┆'.$location;
                }
                
                if($this->isTranslation==1){
                    $user = Auth::user();
                    $itemData = StationM::where('id',$this->idAU)->first();
                    if($itemData!==null){
                        TranslationM::create([
                            'reg_date' => now(),
                            'description' => $itemData->location." | Lat:".$itemData->latitude." | Lon:".$itemData->longitude,
                            'state' => 1,
                            'station_id' => $this->idAU,
                            'user_id' => $user->id,                       
                        ]);
                    }                    
                }
                

                StationM::where('id',$this->idAU)
                ->update([
                    'code' => $this->codeAU,
                    'name' => $this->nameAU,
                    'location' => $location,
                    'address' => $this->placeAU,
                    'search_concatenation' => $search_concatenation,
                    'reg_date' => date('Y-m-d'),
                    'latitude' => $this->latitudeAU,
                    'longitude' => $this->longitudeAU,                        
                    'state' => $this->stateAU,
                    'modifier_user_id'=> Auth::user()->id,                    
                    'municipality_id'=> $this->municipalityIdAU,
                ]);


                DetailStationM::where('station_id',$this->idAU)
                ->update([
                    'brand' => $this->brandDT,
                    'power_supply' => $this->power_supplyDT==-1?null:$this->power_supplyDT,
                    'model' => $this->modelDT==-1?null:$this->modelDT,
                    'station_model_id' => $this->stationModelIdDT != -1 ? $this->stationModelIdDT : null,
                    'installation_date' => $this->installation_dateDT,
                    'elevation' => $this->elevationDT,
                    'installer_user_id' => Auth::user()->id,
                    'state' => $this->stateDT,
                ]);

                DB::commit();

                
                $this->isTranslation = -1;
                $imagenes = [
                    'image1DT' => 'image1',
                    'image2DT' => 'image2',
                    'image3DT' => 'image3',
                ];

                $imagenesFiltradas = array_filter(array_keys($imagenes), fn($propiedad) => !is_null($this->$propiedad));
                if (!empty($imagenesFiltradas)) {                    
                    if (!Storage::disk('public')->exists("imgstation/$this->idAU")) {
                        Storage::disk('public')->makeDirectory("imgstation/$this->idAU");
                    }
                    foreach ($imagenesFiltradas as $propiedad) {
                        $campoDB = $imagenes[$propiedad];                        
                        if ($this->$propiedad) {                            
                            try {
                                $nombreImagen = uniqid() . '.' . $this->$propiedad->getClientOriginalExtension();
                                $path = $this->$propiedad->storeAs("imgstation/$this->idAU", $nombreImagen, 'public');                                
                                $url = str_replace("public/", "", $path); 
                                //dd($url);
                                DetailStationM::where('station_id', $this->idAU)->update([$campoDB => $url]);
                                //DB::commit();
                            } catch (\Exception $e) {
                                //DB::rollBack();
                                //dd($e); // Para depuración, puedes eliminar esto en producción
                            }
                        }
                    }
                    $imgs = DetailStationM::where('station_id', $this->idAU)->select('image1','image2','image3')->first();
                    $this->image1linkDT = $imgs->image1 ?? self::DEFAULT_IMAGE_PATH;
                    $this->image2linkDT = $imgs->image2 ?? self::DEFAULT_IMAGE_PATH;
                    $this->image3linkDT = $imgs->image3 ?? self::DEFAULT_IMAGE_PATH;

                    $this->image1DT = null;
                    $this->image2DT = null;
                    $this->image3DT = null;
                }
                //DB::commit();
                $this->dispatch('successAlert',[ 'success' => __('messages.stations.update.msg_success') ]);

            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.station_update')) ]);
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedAlert ]);
        }
    }

    public function deleteStation($data){        
        $stationId = $data['id'];
        
        try {
            DB::beginTransaction();

            if(CurrentDataM::where('station_id','=',$stationId)->doesntExist() && UsersSubscriptionM::where('station_id','=',$stationId)->doesntExist()){
                CustomizeSensorStationM::where('station_id',$stationId)->delete();
                DetailStationM::where('station_id',$stationId)->delete();
                ForecastM::where('station_id',$stationId)->delete();
                AlertM::where('station_id',$stationId)->delete();
                CustomizeSensorStationM::where('station_id', $stationId)->delete();
                MaintenancePlanM::where('station_id', $stationId)->delete();
                StationM::where('id',$stationId)->delete();
                DB::commit();
                //session()->flash('message', 'Eliminación existosa!');
                $this->dispatch('successAlert',[ 'success' => __('messages.stations.delete.msg_success') ]);
            }else{
                $this->dispatch('failedAlert',[ 'failed' => __('messages.stations.delete.msg_failed') ]);
                //session()->flash('failed', 'La estación no puede ser eliminada porque ya contiene registros de variables o esta sujeta a una suscripción');
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.station_delete')) ]);

        }  
    }

    //public function addStation($location){
    public function addStation(){
        //$this->latitudeAU = $location['lat'];
        //$this->longitudeAU = $location['lng'];
        date_default_timezone_set('America/La_Paz');
       
        $flag = $this->validateStation();
        if(!$flag){
            try {
                DB::beginTransaction();
                
                $nameDpto = DepartmentM::where('id','=', $this->departmentIdAU)->first();// value('abbreviation');
                $nameProv = ProvinceM::where('id','=', $this->provinceIdAU)->value('name');
                $nameMun = MunicipalityM::where('id','=', $this->municipalityIdAU)->value('name');
                $location = $nameMun.', '.$nameProv.', '.$nameDpto->abbreviation;
                if($this->placeAU!==null){
                    $search_concatenation = '┆'.$this->nameAU.'┆'.$location.'┆'.$this->placeAU;
                }else{
                    $search_concatenation = '┆'.$this->nameAU.'┆'.$location;
                }
                
                $station = StationM::create([
                    'code' => $this->codeAU,
                    'name' => $this->nameAU,
                    'location' => $location,
                    'address' => $this->placeAU,
                    'search_concatenation' => $search_concatenation,
                    'reg_date' => date('Y-m-d'),
                    'latitude' => $this->latitudeAU,
                    'longitude' => $this->longitudeAU,                        
                    'state' => $this->stateAU,
                    'user_creator_id'=> Auth::user()->id,
                    'municipality_id'=> $this->municipalityIdAU,
                ]);
                
                
                
                DetailStationM::create([
                    'brand' => $this->brandDT,
                    'power_supply' => $this->power_supplyDT==-1?null:$this->power_supplyDT,
                    'model' => $this->modelDT==-1?null:$this->modelDT,
                    'station_model_id' => $this->stationModelIdDT != -1 ? $this->stationModelIdDT : null,
                    'installation_date' => $this->installation_dateDT,
                    'image1' => null,
                    'image2' => null,
                    'image3' => null,
                    'elevation' => $this->elevationDT,
                    'installer_user_id' => Auth::user()->id,
                    'station_id' => $station->id,
                    'state' => $this->stateDT,
                ]);

                DB::commit();
                               
                if($this->image1DT!=null || $this->image2DT!=null || $this->image3DT!=null){
                    $imagenes = [
                        'image1DT' => 'image1',
                        'image2DT' => 'image2',
                        'image3DT' => 'image3',
                    ];

                    if (!Storage::exists("public/imgstation/$this->idAU/")) {
                        Storage::makeDirectory("public/imgstation/$this->idAU/");
                    }
                    foreach ($imagenes as $propiedad => $campoDB) {
                        if ($this->$propiedad) {
                            try {                                
                                $nombreImagen = uniqid() . '.' . $this->$propiedad->getClientOriginalExtension();
                                Storage::disk('public')->makeDirectory("imgstation/$this->idAU");
                                $url = Storage::url("imgstation/$this->idAU/$nombreImagen");
                                DetailStationM::updateOrInsert(
                                    ['station_id' => $this->idAU],
                                    [$campoDB => $url]
                                );
                            } catch (\Exception $e) {
                                //dd($e);
                            }
                            
                        }
                    }
                    
                }
                DB::commit();
                $this->goBack();
                $this->dispatch('successAlert',[ 'success' => __('messages.stations.create.msg_success') ]);


            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.station_create')) ]);
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedAlert ]);
        }
    }

    public function validateStation(){
        $this->failedAlert = '';

        if ($this->codeAU == null) {
            $this->failedAlert = 'El código de estación es obligatorio.';
            return true;
        }

        if (StationM::where('code', $this->codeAU)->exists()) {
            $this->failedAlert = 'El código de estación ya existe. Por favor, genere uno nuevo.';
            return true;
        }

        if (StationM::where('name', $this->nameAU)->exists()) {
            $this->failedAlert = __('messages.stations.create.validateAdd_2');
            return true;
        }

        if ($this->municipalityIdAU == -1 || $this->municipalityIdAU == null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_8');
            return true;
        }

        if ($this->latitudeAU == null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_9');
            return true;
        }

        if ($this->longitudeAU == null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_10');
            return true;
        }

        if ($this->elevationDT === null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_11');
            return true;
        }

        if ($this->power_supplyDT == -1 || $this->power_supplyDT === null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_12');
            return true;
        }

        if ($this->brandDT === null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_13');
            return true;
        }

        if ($this->modelDT == -1 || $this->modelDT === null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_14');
            return true;
        }

        if ($this->installation_dateDT == null) {
            $this->failedAlert = __('messages.stations.create.validateAdd_15');
            return true;
        }

        return false;
    }


    public function validateStationUpdate(){
        $this->failedAlert = '';

        if ($this->codeAU == null) {
            $this->failedAlert = 'El código de estación es obligatorio.';
            return true;
        }

        if (StationM::where('code', $this->codeAU)->where('id', '!=', $this->idAU)->exists()) {
            $this->failedAlert = 'El código de estación ya existe. Por favor, genere uno nuevo.';
            return true;
        }

        if (StationM::where('name', '=', $this->nameAU)->where('id', '!=', $this->idAU)->exists()) {
            $this->failedAlert = __('messages.stations.update.validateUS_2');
            return true;
        }

        if ($this->municipalityIdAU == -1 || $this->municipalityIdAU == null) {
            $this->failedAlert = __('messages.stations.update.validateUS_8');
            return true;
        }

        if ($this->latitudeAU == null) {
            $this->failedAlert = __('messages.stations.update.validateUS_9');
            return true;
        }

        if ($this->longitudeAU == null) {
            $this->failedAlert = __('messages.stations.update.validateUS_10');
            return true;
        }

        if ($this->elevationDT === null) {
            $this->failedAlert = __('messages.stations.update.validateUS_11');
            return true;
        }

        if ($this->power_supplyDT == -1 || $this->power_supplyDT === null) {
            $this->failedAlert = __('messages.stations.update.validateUS_12');
            return true;
        }

        if ($this->brandDT === null) {
            $this->failedAlert = __('messages.stations.update.validateUS_13');
            return true;
        }

        if ($this->modelDT == -1 || $this->modelDT === null) {
            $this->failedAlert = __('messages.stations.update.validateUS_14');
            return true;
        }

        if ($this->installation_dateDT == null) {
            $this->failedAlert = __('messages.stations.update.validateUS_15');
            return true;
        }

        return false;
    }


    public function setFormUpdateStation($id){
        
        $this->idAU = $id;
        $this->formAdd = false;
        $this->viewlist = false;
        $this->formUpdate = true;       

        $stationItem = StationM::where('id',$this->idAU)->first();
       
        //STATION FORM
        $this->codeAU = $stationItem->code;
        $this->nameAU = $stationItem->name;
        $this->modelDT = $stationItem->type;
        $this->placeAU = $stationItem->address;
        $this->locationAU = $stationItem->location;
        $this->stateAU = $stationItem->state;
        $this->elevationDT = $stationItem->detail_station->first()->elevation;
        $this->departmentAU = DepartmentM::where('state',1)->get();
        $this->provinceAU = [];
        $this->municipalityAU = [];
        $this->departmentIdAU = -1;
        $this->provinceIdAU = -1;
        $this->municipalityIdAU = $stationItem->municipality_id===null?-1:$stationItem->municipality_id;
        if($this->municipalityIdAU != null && $this->municipalityIdAU != -1){
            // Derivar la jerarquia (provincia y departamento) a partir del municipio guardado
            $this->provinceIdAU = MunicipalityM::where('id',$this->municipalityIdAU)->value('province_id');
            $this->departmentIdAU = ProvinceM::where('id',$this->provinceIdAU)->value('department_id');

            // Cargar TODAS las opciones de cada categoria para poder cambiarlas directamente
            $this->provinceAU = ProvinceM::where('state',1)->where('department_id',$this->departmentIdAU)->get();
            $this->municipalityAU = MunicipalityM::where('state',1)->where('province_id',$this->provinceIdAU)->get();
        }
        $this->latitudeAU = $stationItem->latitude;
        $this->longitudeAU = $stationItem->longitude;
        
        $this->isTranslation = -1;
        $this->listTranslation = TranslationM::where('station_id',$this->idAU)->orderby('reg_date','desc')->get();//->toArray();

        //ADD AND UPDATE DetailStationM
        $this->image1linkDT = $stationItem->detail_station->first()->image1 ?? self::DEFAULT_IMAGE_PATH;
        $this->image2linkDT = $stationItem->detail_station->first()->image2 ?? self::DEFAULT_IMAGE_PATH;
        $this->image3linkDT = $stationItem->detail_station->first()->image3 ?? self::DEFAULT_IMAGE_PATH;

        $this->brandDT = $stationItem->detail_station->first()->brand;
        $this->power_supplyDT = $stationItem->detail_station->first()->power_supply==null?-1:$stationItem->detail_station->first()->power_supply;
        $this->modelDT = $stationItem->detail_station->first()->model==null?-1:$stationItem->detail_station->first()->model;
        $this->installation_dateDT = $stationItem->detail_station->first()->installation_date;

        // Cascada marca → modelo
        $this->brandsListDT = BrandM::where('state', 1)->orderBy('name')->get();
        $this->brandIdDT = -1;
        $this->stationModelIdDT = -1;
        $this->modelsListDT = [];
        $savedModelId = $stationItem->detail_station->first()->station_model_id;
        if ($savedModelId) {
            $savedModel = StationModelM::find($savedModelId);
            if ($savedModel) {
                $this->stationModelIdDT = $savedModel->id;
                $this->brandIdDT = $savedModel->brand_id;
                $this->modelsListDT = StationModelM::where('state', 1)
                    ->where('brand_id', $savedModel->brand_id)
                    ->orderBy('name')
                    ->get();
            }
        }
        $this->elevationDT = $stationItem->detail_station->first()->elevation;
        $this->installer_user_idDT = $stationItem->detail_station->first()->installer_user_id;
        $this->station_idDT = $stationItem->detail_station->first()->station_id;
        $this->stateDT = $stationItem->detail_station->first()->state;

        if($stationItem->detail_station->first()->image1!=null){
            $this->image1linkDT = $stationItem->detail_station->first()->image1;
        }

        if($stationItem->detail_station->first()->image2!=null){
            $this->image2linkDT = $stationItem->detail_station->first()->image2;
        }
        
        if($stationItem->detail_station->first()->image3!=null){
            $this->image3linkDT = $stationItem->detail_station->first()->image3;
        }
        if(!is_numeric($this->latitudeAU)){
            $this->latitudeAU = '-17.784697';
        }

        if(!is_numeric($this->longitudeAU)){
            $this->longitudeAU = '-63.182028';
        }

        $this->dispatch('openMapStationAU', [
            'lat' => $this->latitudeAU,
            'lon' => $this->longitudeAU,
        ]);
    }

    public function visibleAdd(){
        $this->image1linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image2linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image3linkDT = self::DEFAULT_IMAGE_PATH;

        $this->closedUpdate();
        $this->formAdd = true;
        $this->viewlist = false;
        $this->formUpdate = false;
        $this->departmentAU =  DepartmentM::where('state',1)->get();
        
        // Generar código automáticamente al abrir formulario de agregar
        $this->generateStationCode();
        
        $this->latitudeAU = '-17.784697';
        $this->longitudeAU = '-63.182028';

        $this->dispatch('openMapStationAU', [
            'lat' => $this->latitudeAU,
            'lon' => $this->longitudeAU,
        ]);
    }

    public function chooseDepartment(){
        // Al cambiar de departamento: refrescar provincias y dejar provincia y municipio a la espera de seleccion
        $this->provinceIdAU = -1;
        $this->municipalityIdAU = -1;
        $this->municipalityAU = [];

        if($this->departmentIdAU != -1){
            $this->provinceAU = ProvinceM::where('state',1)->where('department_id',$this->departmentIdAU)->get();
        }else{
            $this->provinceAU = [];
        }
    }

    public function chooseProvince(){
        // Al cambiar de provincia: refrescar municipios y dejar el municipio a la espera de seleccion
        $this->municipalityIdAU = -1;

        if($this->provinceIdAU != -1){
            $this->municipalityAU = MunicipalityM::where('state',1)->where('province_id',$this->provinceIdAU)->get();
        }else{
            $this->municipalityAU = [];
        }
    }

    public function goBack(){
        $this->viewlist = true;
        $this->formAdd = false;
        $this->formUpdate = false;
        $this->viewMaintenancePlan = false;
        $this->viewFormMaintenancePlanAdd = false;
        $this->viewFormMaintenancePlanUpdate = false;
        $this->resetStationCalibrationModal();
        
        
        //ADD AND UPDATE StationM
        $this->codeAU = null;
        $this->nameAU = null;
        $this->placeAU = null;
        $this->latitudeAU = null;
        $this->longitudeAU = null;
        $this->codeDT = null;
        $this->modelDT = null;
        $this->municipalityIdAU = null;

        $this->departmentIdAU = null;
        $this->provinceIdAU = null;
        $this->departmentAU = [];
        $this->provinceAU = [];
        $this->municipalityAU = [];
        
        $this->image1linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image2linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image3linkDT = self::DEFAULT_IMAGE_PATH;
        $this->image1DT = null;
        $this->image2DT = null;
        $this->image3DT = null;


        //ADD AND UPDATE DetailStationM
        $this->equipmentDT = null;
        $this->brandDT = null;
        $this->power_supplyDT = null;
        $this->packageDT = null;
        $this->codeDT = null;
        $this->modelDT = null;
        $this->installation_dateDT = null;
        $this->to_hour_dayDT = null;
        $this->to_cvDT = null;
        $this->elevationDT = null;
        $this->installer_user_idDT = null;
        $this->station_idDT = null;
        $this->stateDT = 1;

        // Cascada marca → modelo
        $this->brandsListDT = BrandM::where('state', 1)->orderBy('name')->get();
        $this->brandIdDT = -1;
        $this->stationModelIdDT = -1;
        $this->modelsListDT = [];
    }

    public function chooseBrand(): void
    {
        $this->stationModelIdDT = -1;
        $this->modelsListDT = [];
        if ($this->brandIdDT != -1) {
            $this->modelsListDT = StationModelM::where('state', 1)
                ->where('brand_id', $this->brandIdDT)
                ->orderBy('name')
                ->get();
        }
    }

    public function closedUpdate(){
        $this->idAU = null;
        $this->formUpdate = false;
        $this->name = null;
        $this->state = null;
    }

    public function changeStatus($id,$estadoActual){
        DB::beginTransaction();
        try {
            MaintenancePlanM::where('id','=',$id)
                ->update([
                    'state'=> $estadoActual==1?0:1,
                ]);
            DB::commit();
            //session()->flash('message', 'Se actualizo el estado!');
            //$this->dispatch('ocultar-alert');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('failed', 'NO SE PUDO CAMBIAR EL ESTADO. INTENTE NUEVAMENTE');
        }
    }

    private function resultData(){       
        $data = [];
        
        $searchStation = $this->search;
        $searchSLocation = $this->searchLocation;
        if($this->viewlist && $this->viewType=='table'){
            $this->stationId = null;
            $this->stationName = null;
            if(strtolower($this->stateChange)=='all'){
                $data = StationM::with(['user_creator', 'modifier_user'])
                    ->orderBy($this->sortColumn, $this->sortDirection)
                    ->where(function ($query) use ($searchStation) {
                        if ($searchStation != null) {
                            $query->where('stations.name', 'LIKE', '%' . $searchStation . '%');
                        }
                    })
                    ->where(function ($query2) use ($searchSLocation) {
                        if ($searchSLocation != null) {
                            $query2->where('stations.location', 'LIKE', '%' . $searchSLocation . '%');
                        }
                    })
                    ->paginate($this->perPage);
            }else{

                $data = StationM::with(['user_creator', 'modifier_user'])
                ->where('stations.state','=',$this->stateChange)
                ->where(function ($query) use ($searchStation) {
                    if($searchStation!=null){
                        $query->where('stations.name','LIKE', '%'.$searchStation.'%');
                        $this->perPage=10;
                    }
                })
                ->where(function ($query2) use ($searchSLocation) {
                    if($searchSLocation!=null){
                        $query2->where('stations.location','LIKE', '%'.$searchSLocation.'%');
                        $this->perPage=10;
                    }
                })
                ->orderBy($this->sortColumn,$this->sortDirection)
                ->paginate($this->perPage);
            }
        }else if($this->viewMaintenancePlan){
            if($this->viewFormMaintenancePlanAdd){
                if($this->maintenanceDate==null){
                    $this->maintenanceDate = date('Y-m-d');
                }

                if($this->nextMaintenanceDate==null){
                    $tiempoUnix = new DateTime($this->maintenanceDate);
                    $tiempoUnix->modify('+1 year -1 days');
                    $this->nextMaintenanceDate = $tiempoUnix->format('Y-m-d');
                }

                $start = new DateTime($this->maintenanceDate);
                $next = new DateTime($this->nextMaintenanceDate);

                if($next < $start){
                    $this->nextMaintenanceDate = $this->maintenanceDate;
                }
                if($start > $next){
                    $this->nextMaintenanceDate = $this->maintenanceDate;
                }
                $phrase = __('messages.stations.maintananceplan.preventive_phrase');
                if($this->preventiveMaintenance == 'SI'){
                        
                        if($this->preventiveMaintenanceFlag==false){
                            $this->preventiveMaintenanceFlag = true;
                            
                            if($this->descriptionMaintenance != null){
                                if (strpos($this->descriptionMaintenance, $phrase) == false) {
                                    $this->descriptionMaintenance = $this->descriptionMaintenance.' '.$phrase;
                                }                        
                            }else{
                                $this->descriptionMaintenance = $phrase; 
                            }

                        }
                    
                }else{
                    if($this->preventiveMaintenance==-1 || $this->preventiveMaintenance=='NO'){
                        if($this->descriptionMaintenance != null && $this->descriptionMaintenance != ''){
                            $this->descriptionMaintenance = str_replace($phrase, "", $this->descriptionMaintenance);
                        }
                    }
                }           
            }else if($this->viewFormMaintenancePlanUpdate){
            }else{

                $maintenanceDateF = $this->maintenanceDateFilter;
                $workOrderF = $this->workOrderFilter;
                $deliveryNoteF = $this->deliveryNoteFilter;
                $maintenanceTypeNoteF = $this->maintenanceTypeNoteFilter;

                $this->dataResponse = [];
                $this->dataResponse = MaintenancePlanM::where('station_id','=',$this->stationIdMP)
                ->where(function ($query) use ($maintenanceDateF) {
                    if($maintenanceDateF!=null && $maintenanceDateF!=''){
                        return $query->where('maintenance_plan.maintenance_date','=', $maintenanceDateF);
                    }
                })
                ->where(function ($query2) use ($workOrderF) {
                    if($workOrderF!=null && $workOrderF!=''){
                        //dd('aaaaaa',$workOrderF);
                        return $query2->where('maintenance_plan.work_order_number','LIKE', '%'.$workOrderF.'%');
                    }
                })
                ->where(function ($query3) use ($deliveryNoteF) {
                    if($deliveryNoteF!=null && $deliveryNoteF!=''){
                        return $query3->where('maintenance_plan.delivery_note_number','LIKE','%'.$deliveryNoteF.'%');
                    }
                })
                ->where(function ($query4) use ($maintenanceTypeNoteF) {
                    if($maintenanceTypeNoteF!=-1 && $maintenanceTypeNoteF!=null){
                        if($maintenanceTypeNoteF=='preventive'){
                            return $query4->where('maintenance_plan.preventive_maintenance','=', 'SI');
                        }else{
                            if($maintenanceTypeNoteF=='corrective'){
                                return $query4->where('maintenance_plan.corrective_maintenance','=','SI');
                            }
                        }
                    }
                })
                ->orderBy('maintenance_plan.id','desc')
                ->limit($this->paginationMP)->get();
            }

        }else if($this->formAdd){            
            //$this->dispatch('openMapStationAU', [
            //  'lat' => $this->latitudeAU,
            //  'lon' => $this->longitudeAU,
            //]);
        }else{
            //$this->dispatch('openMapStationAU', [
            //    'lat' => $this->latitudeAU,
            //    'lon' => $this->longitudeAU,
            //]);
        }

        return $data;
    }


    private function buildOptionLabelMap(): array
    {
        return [
            'YES' => __('messages.common.options.yes'),
            'NO' => __('messages.common.options.no'),
            'NA' => __('messages.common.options.not_applicable'),
            'SI' => __('messages.common.options.yes'),
            'C2 = 3 Unidades' => __('messages.stations.form.option_labels.c2_3_units'),
            'AA = 4 Unidades' => __('messages.stations.form.option_labels.aa_4_units'),
            'Photovoltaic' => __('messages.stations.form.option_labels.photovoltaic'),
            'Electric' => __('messages.stations.form.option_labels.electric'),
            'Wireless' => __('messages.stations.form.option_labels.wireless'),
            'Wired' => __('messages.stations.form.option_labels.wired'),
            'Plastico' => __('messages.stations.form.option_labels.plastic'),
            'Aluminio' => __('messages.stations.form.option_labels.aluminium'),
            'Cobre' => __('messages.stations.form.option_labels.copper'),
            'Hierro' => __('messages.stations.form.option_labels.iron'),
            'Otro' => __('messages.stations.form.option_labels.other'),
        ];
    }

    public function render(){

        return view('livewire.station.station',[
            'data'=> $this->resultData(),
            'optionLabelMap' => $this->buildOptionLabelMap(),
        ]);
    }
}

<?php

namespace App\Livewire\Station;

use DateTime;
use DateInterval;
use App\Utils\Utils;
use Livewire\Component;
use App\Models\StationM;
use Livewire\WithPagination;
use App\Models\UserDependencyM;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthValidationTrait;
use Illuminate\Support\Facades\Auth;

class StationMap extends Component{

    use WithPagination;
    use AuthValidationTrait;
    
    protected $paginationTheme = 'tailwind';

    public $perPage = 10;
    public $search = null;
    public $searchLocation = null;
    public $weather_type='temperature'; //temperature,wind,rain
    public $goMarkerZoom = null;
    public $mapInteraction = 'initial';

    public $stationId = null;
    public $stationName = null;

    public $typeS = null;
    public $userVisibilityMapInternal = true;

    /**
     * Carga diferida: el primer pintado NO ejecuta las consultas pesadas.
     * Se pone en true vía wire:init (loadMarkers) tras renderizar la página,
     * para que el mapa aparezca de inmediato y las estaciones entren después.
     */
    public bool $mapReady = false;

    /**
     * Activa el pulso/onda tipo radar en las estaciones ACTIVAS del mapa.
     * Es opcional: ponlo en false para desactivar la animación. Las estaciones
     * no funcionales (delayed/offline/disabled) conservan su propio efecto de
     * alerta de todos modos, independientemente de esta bandera.
     */
    public bool $enablePulseEffect = true;

    /**
     * Estaciones con reglas de aviacion para el mapa.
     * Para agregar otra estacion, registra su id, altura y campos de viento en nudos.
     */
    public array $aviationStations = [
        [
            'stationId' => 491,
            'altitudeMeters' => 530,
            'windFieldsInKnots' => ['windspeed2', 'windspeedhi2'],
        ],
        [
            'stationId' => 20,
            'altitudeMeters' => 3837,
            'windFieldsInKnots' => ['windspeed', 'windspeedhi'],
        ],
    ];

    function mount($typeS = null){
        $this->typeS = $typeS;
        $user = Auth::user();
        if ($user->hasRole(['manager', 'subscriber'])) {
           $this->userVisibilityMapInternal = false;
        }
        $this->validarUsuario($user, []);
        abort_unless($user?->can('stations.map.view'), 403);
    }

    /**
     * Llamado vía wire:init después del primer pintado. Activa la carga real
     * (marcadores + lista); a partir de aquí render() ejecuta las consultas.
     */
    public function loadMarkers(){
        $this->mapReady = true;
        $this->loadMapMarker();
    }

    private function loadMapMarker(){
        $isInternalView = false;
        $stationList = null;
        if (Auth::user()->hasAnyRole(['super_manager', 'manager', 'meteorology', 'meteorologist', 'technical'])) {
            $isInternalView = true;
            if($this->typeS=="qair"){
                $stationList = StationM::join('current_data as dd', 'dd.station_id', 'stations.id')
                ->select('stations.id as stationId', 'stations.name', 'stations.location', 'stations.latitude', 'stations.longitude', 'stations.state as stateStation', 'dd.tempout','dd.humout', 'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press','dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2','dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2','dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2','dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date')
                ->orderBy('stations.name', 'asc')
                ->distinct()
                ->get();
            }else{
                $stationList = StationM::select('stations.id as stationId', 'stations.name', 'stations.location', 'stations.latitude', 'stations.longitude', 'stations.state as stateStation', 'dd.tempout','dd.humout', 'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press','dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2','dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2','dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2','dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date')
                ->join('current_data as dd', 'dd.station_id', 'stations.id')
                ->orderBy('stations.name', 'asc')
                ->distinct()
                ->get();
            }
        }else{
            if (Auth::user()->hasAnyRole(['subscriber'])) {
                
                if($this->typeS=="qair"){
                    $stationList = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                    ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                    ->join('stations as s', 'us.station_id', 's.id')
                    ->join('current_data as dd', 'dd.station_id', 's.id')
                    ->select('s.id as stationId', 's.name', 's.location', 's.latitude', 's.longitude', 's.state as stateStation', 'dd.tempout','dd.humout', 'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press','dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2','dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2','dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2','dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date')                            
                    ->where('s.state', true)
                    ->orderBy('s.name', 'asc')
                    ->distinct()
                    ->get();
                }else{
                    $stationList = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                    ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                    ->join('stations as s', 'us.station_id', 's.id')
                    ->join('current_data as dd', 'dd.station_id', 's.id')
                    ->select('s.id as stationId', 's.name', 's.location', 's.latitude', 's.longitude', 's.state as stateStation', 'dd.tempout','dd.humout', 'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press','dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2','dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2','dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2','dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date')                   
                    ->where('s.state', true)
                    ->orderBy('s.name', 'asc')
                    ->distinct()
                    ->get();
                }
            }
        }

        $this->dispatch('openMapStation', [
            'data' => $stationList,
            'weather_type' => $this->weather_type,
            'goMarkerZoom' => $this->goMarkerZoom,
            'typeS' => $this->typeS,
            'isInternalView' => $isInternalView,
            'userVisibilityMapInternal' => $this->userVisibilityMapInternal,
            'mapInteraction' => $this->mapInteraction,
            'enablePulseEffect' => $this->enablePulseEffect,
            'aviationStations' => $this->aviationStations,
        ]);

        $this->mapInteraction = 'refresh';
    }

    public function setGoMarkerZoom($data){
       $this->goMarkerZoom = $data;
       $this->stationId = $data['id'];
       $this->stationName = $data['name'];
       $this->mapInteraction = 'focus_station';
       $this->loadMapMarker();
    }

    public function setweatherType($type){
        $this->weather_type = $type;
        $this->mapInteraction = 'weather_filter';
        $this->loadMapMarker();
    }

    private function resultData(){
        // Carga diferida: el primer pintado no ejecuta consultas pesadas.
        // Los marcadores y la lista se cargan vía wire:init (loadMarkers).
        if (! $this->mapReady) {
            return [];
        }

        $data = [];

        $searchStation = $this->search;
        $searchSLocation = $this->searchLocation;
        
        $this->perPage=10;

        $stationList = null;
        if (Auth::user()->hasAnyRole(['super_manager', 'manager', 'meteorology', 'meteorologist', 'technical'])) {
            if($this->typeS=="qair"){
                $data = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                        ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                        ->join('stations as s', 'us.station_id', 's.id')
                        ->where(function ($query) use ($searchStation) {
                            if ($searchStation != null) {
                                $query->where('s.name', 'LIKE', '%' . $searchStation . '%');
                            }
                        })
                        ->where(function ($query) use ($searchSLocation) {
                            if ($searchSLocation != null) {
                                $query->where('s.location', 'LIKE', '%' . $searchSLocation . '%');
                            }
                        })
                        ->select('s.id', 's.name', 's.location', 's.latitude', 's.longitude')
                        ->orderBy('s.name', 'asc')
                        ->distinct()
                        ->get();
                
            }else{
                // Solo las columnas que usa el panel de búsqueda (nombre/localidad) y el zoom (id/lat/lng).
                $data = StationM::select('stations.id', 'stations.name', 'stations.location', 'stations.latitude', 'stations.longitude')
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
                ->orderBy('stations.name','asc')
                ->get();
            }
        }else{
            if (Auth::user()->hasAnyRole(['subscriber'])) {
                
                if($this->typeS=="qair"){
                    $data = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                    ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                    ->join('stations as s', 'us.station_id', 's.id')
                    ->where(function ($query) use ($searchStation) {
                        if ($searchStation != null) {
                            $query->where('s.name', 'LIKE', '%' . $searchStation . '%');
                        }
                    })
                    ->where(function ($query) use ($searchSLocation) {
                        if ($searchSLocation != null) {
                            $query->where('s.location', 'LIKE', '%' . $searchSLocation . '%');
                        }
                    })
                    ->select('s.id', 's.name', 's.location', 's.latitude', 's.longitude')
                    ->where('s.state', true)
                    ->orderBy('s.name', 'asc')
                    ->distinct()
                    ->get();
                }else{
                    $data = UserDependencyM::join('users_subscriptions as us', 'us.id', '=', 'user_dependency.user_subscription_id')
                    ->join('users as u', 'user_dependency.dependent_user_id', '=', 'u.id')
                    ->join('stations as s', 'us.station_id', '=', 's.id')
                    ->select('s.id', 's.name', 's.location', 's.latitude', 's.longitude')
                    ->where('s.state', true)
                    ->where(function ($query) use ($searchStation) {
                        if ($searchStation != null) {
                            $query->where('s.name', 'LIKE', '%' . $searchStation . '%');
                        }
                    })
                    ->where(function ($query) use ($searchSLocation) {
                        if ($searchSLocation != null) {
                            $query->where('s.location', 'LIKE', '%' . $searchSLocation . '%');
                        }
                    })
                    ->orderBy('s.name', 'asc')
                    ->distinct()
                    ->get();
                }
            }
        }

        return $data;
    }

    public function render(){
        return view('livewire.station.station-map',[
            'data'=> $this->resultData(),
        ]);
    }
}

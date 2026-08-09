<?php

namespace App\Http\Controllers;

use Pdf;
use DateTime;
use DateInterval;
//use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StationM;
use App\Models\ForecastM;
use App\Models\CurrentDataM;
use Illuminate\Http\Request;
use App\Models\DetailStationM;
use App\Models\MaintenancePlanM;

class StationC extends Controller
{
    public function index(){
        $typeS="station";
        return view('station.index',compact('typeS'));
    }

    public function indexMapOnly(){
        $typeS="station";
        return view('station.map',compact('typeS'));
    }

    public function reportMaintenanceFilter(Request $request){
        date_default_timezone_set('America/La_Paz');
        $type = $request->query('type');
        $hoy = date('Y-m-d H:i:s');
        $textOuput = "historial_mantenimiento_estacion_".(string)$request->station_id.".pdf";

        $maintenanceDateF = $request->maintenance_date;
        $workOrderF = $request->work_order;
        $deliveryNoteF = $request->delivery_note;
        $maintenanceTypeNoteF = $request->maintenance_type;
        
        $dataResponse = [];
        $dataResponse = MaintenancePlanM::where('station_id','=',$request->station_id)
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
        ->limit($request->pagination)->get();
        
        if($type == 'watch'){
            return Pdf::loadView('station.pdf_maintenance_item',compact('dataResponse'))
                    ->setPaper('a4', 'portrait')
                    ->stream($textOuput);
        }else if($type == 'download'){
            return Pdf::loadView('station.pdf_maintenance_item',compact('dataResponse'))
                    ->setPaper('a4', 'portrait')
                    ->download($textOuput);
        }        
    }


    public function reportMaintenanceIndividual(Request $request){
        date_default_timezone_set('America/La_Paz');
        $type = $request->query('type');
        $hoy = date('Y-m-d H:i:s');
        $textOuput = "mantenimiento_estacion_".(string)$request->id.".pdf";
        $dataResponse = MaintenancePlanM::where('id','=',$request->id)
        ->first();
        
        if($type == 'watch'){
            return Pdf::loadView('station.pdf_maintenance_item_individual',compact('dataResponse'))
                    ->setPaper('a4', 'portrait')
                    ->stream($textOuput);
        }else if($type == 'download'){
            return Pdf::loadView('station.pdf_maintenance_item_individual',compact('dataResponse'))
                    ->setPaper('a4', 'portrait')
                    ->download($textOuput);
        }        
    }

    public function reportStation(Request $request){
        date_default_timezone_set('America/La_Paz');
        $type = $request->query('type');
        $hoy = date('Y-m-d H:i:s');
        $textOuput = "estacion_".(string)$request->id.".pdf";
        $tag = 'station.pdf_station';
        $dataResponse = StationM::where('id','=',$request->id)
        ->first();

        if(is_null($dataResponse->detail_station->first())){            

            DetailStationM::create([
                'station_id' => $request->id,
                'state' => 1,
            ]);
        }
        //dd($dataResponse->detail_station->first());
        //dd($dataResponse->detail_station->first()->reception_type);
        
        if($request->blade_type == 'watch'){
            return Pdf::loadView($tag,compact('dataResponse'))
                    ->setPaper('a4', 'portrait')
                    ->stream($textOuput);
        }else if($type == 'download'){
            return Pdf::loadView($tag,compact('dataResponse'))
                    ->setPaper('a4', 'portrait')
                    ->download($textOuput);
        }
    }

    public function getShowForecast(Request $request){
        /*
        date_default_timezone_set('America/La_Paz');
        $meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
        $station = StationM::where('id','=',$request->stationId)->first();
        $info = CurrentDataM::where('station_id','=',$request->stationId)->first();

        $wind = '';
		if(($info->wind_dir!=-9999)){
            if (($info->wind_dir>=337.5) || ($info->wind_dir<22.5)){
                $wind = 'N ' ;
            }else if (($info->wind_dir>=22.5) && ($info->wind_dir<67.5)){
                $wind = 'NE ' ;
            }else if (($info->wind_dir>=67.5) && ($info->wind_dir<112.5)){
                $wind = 'E ' ;
            } else if (($info->wind_dir>=112.5) && ($info->wind_dir<157.5)){
                $wind = 'SE ';
            } else if (($info->wind_dir>=157.5) && ($info->wind_dir<202.5)){
                $wind = 'S ' ;
            } else if (($info->wind_dir>=202.5) && ($info->wind_dir<247.5)){
                $wind = 'SO ';
            } else if (($info->wind_dir>=247.5) && ($info->wind_dir<292.5)){
                $wind = 'O ' ;
            } else if (($info->wind_dir>=292.5) && ($info->wind_dir<337.5)){
                $wind = 'NO ';
            }else{ 
                $wind = 'NA'; 
            }       
        }else{
            $wind = 'NA'; 
        }
        $dt = new DateTime();		
        $date = $dt->format('Y-m-d');
        $forecast = ForecastM::where('station_id','=',$request->stationId)->where('reg_date','=',$date)->orderBy('reg_date','asc')->get();
        $forecast1 = $forecast;
        $starts_in = 0;
        foreach ($forecast1 as $value) {
            $now = new DateTime();
            //$now->modify('+2 hour');
            $dt1 = new DateTime(date($forecast1->reg_date));
            if (strtotime($now->format('Y-m-d H:i:s')) > (strtotime($dt1->format('Y-m-d H:i:s')))) {
                $starts_in = $starts_in + 1;
            }
        }

        $dtu = new DateTime(date($info->receipt_date));
        $now = new DateTime();
        
        //$now->modify('+3 hour');
        $tmax = $est->getTempMax($idestacion,$dtu->format('Y-m-d'));
        $result = mysqli_query($this->db_connection_current,"SELECT TEMP_OUT AS MAXIMO, DATE_FORMAT(fecha, '%T') as HORA FROM datos WHERE DISPOSITIVO_ID=" . $id . " AND DATE(FECHA)='".$fecha."' AND TEMP_OUT=(SELECT MAX(TEMP_OUT) FROM datos WHERE DISPOSITIVO_ID=".$id." AND DATE(fecha)='".$fecha."') ORDER BY fecha ASC LIMIT 1");

        
        $tmin = $est->getTempMin($idestacion,$dtu->format('Y-m-d'));
        result = mysqli_query($this->db_connection_current,"SELECT TEMP_OUT AS MINIMO, DATE_FORMAT(fecha, '%T') as HORA FROM datos WHERE DISPOSITIVO_ID=" . $id . " AND DATE(FECHA)='".$fecha."' AND TEMP_OUT=(SELECT MIN(TEMP_OUT) FROM datos WHERE DISPOSITIVO_ID=".$id." AND TEMP_OUT<>-9999 AND DATE(fecha)='".$fecha."') ORDER BY fecha ASC LIMIT 1");




        
        
        $pron_max = $forecast->getTempMax($idestacion,date($dtu->format('Y-m-d')));
        $pron_min = $forecast->getTempMin($idestacion,date($dtu->format('Y-m-d')));
        if ($pron_max[0]['MAXIMO']>$tmax['MAXIMO']) { $tmax['MAXIMO'] = $pron_max[0]['MAXIMO'];}
        if ($pron_min[0]['MINIMO']<$tmin['MINIMO']) { $tmin['MINIMO'] = $pron_min[0]['MINIMO'];} 
        else { if ($tmin['MINIMO']=='') { $tmin['MINIMO'] = $pron_min[0]['MINIMO'];}}
        if (($dtu->diff($now)->h<=3)&&($dtu->diff($now)->d<1)) {}
        */
		
    }
}

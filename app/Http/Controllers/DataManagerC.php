<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenancePlanM;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\QairImport; 
use Maatwebsite\Excel\Facades\Excel; 

class DataManagerC extends Controller
{
    public function resetdata(){
        return view('datamanager.resetdata');
    }

    public function reportMaintenanceFilter(Request $request){
        $type = $request->query('type');
        $hoy = date('Y-m-d H:i:s');
        $textOuput = "suscripcion_de_cliente.pdf";

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
    public function showLoadDataQuiar(){
        return view('datamanager.showloaddataquair');
    }

    public function setDataQuair(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        
        $file = $request->file('file');
        try {
            Excel::import(new QairImport, $file); 
            return redirect()->back()->with('success', 'Archivo importado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}

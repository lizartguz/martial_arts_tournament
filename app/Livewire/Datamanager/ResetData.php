<?php

namespace App\Livewire\Datamanager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\StationM;
use Illuminate\Http\Request;
use DateTime;
use DateInterval;
use App\Imports\ResetDataImport;
use Maatwebsite\Excel\Facades\Excel;

class ResetData extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $headers;
    protected $paginationTheme = 'tailwind';

    public $perPage = 10;
    public $search = null;
    public $dateStart = null;
    public $dateEnd = null;
    public $dataResponse = [];
    public $stationId = null;
    public $stationName = null;
    public $failedAlert = null;
    public $xlsxfile = null;

    function mount(){
       //$this->dateStart = date('Y-m-d');
       //$this->dateEnd = date('Y-m-d');
       $this->xlsxfile=null;
    }

    public function setStationId($id,$name){
        $this->stationId = $id;
        $this->stationName = $name;
    }

    public function resetStation(){
        $this->stationId = null;
        $this->stationName = null;
        $this->dateStart = null;
        $this->dateEnd = null;
    }

    public function processData(){

        //$fileContents = file_get_contents($this->xlsxfile->getRealPath());
        //Excel::import(new UsersImport, $this->xlsxfile, 'users.xlsx');
        if($this->xlsxfile!=null){
            try {
                $this->validate([
                    'xlsxfile' => 'required|file|mimes:xlsx',
                ]);
        
                $path = $this->xlsxfile->getRealPath();
                Excel::import(new ResetDataImport, $path);

                //(new ResetDataImport)->import($this->xlsxfile, null, \Maatwebsite\Excel\Excel::XLSX);
                //dd($this->xlsxfile);
            } catch (\Exception $e) {
                
                dd($e->getMessage());
            }            
        }else{
            $this->dispatch('failedAlert',[ 'failed' => 'Debe cargar un documento excel' ]);
        }
        
}

    private function viewData(){
            $dateRange = [$this->dateStart,$this->dateEnd];
            $stationId = $this->stationId;
            $this->dataResponse = DB::connection('mysql_pro')
                ->table('total_diario as t')
                ->where(function ($query) use ($stationId) {
                    if($stationId!=null){
                        $query->where('t.DISPOSITIVO_ID','=',$stationId);
                    }
                })
                ->where(function ($query2) use ($dateRange) {
                    if($dateRange[0]!=null && $dateRange[1]!=null){
                        $query2->where(DB::raw("CONCAT(t.F_YEAR, '-', t.F_MONTH, '-', t.F_DAY)"), '>=', $dateRange[0])
                            ->where(DB::raw("CONCAT(t.F_YEAR, '-', t.F_MONTH, '-', t.F_DAY)"), '<=', $dateRange[1]);
                    }
                })
                //->orderBy('t.ID','desc')
                ->get();

            if(count($this->dataResponse) <= 0){
                $this->stationId = null;

                $this->dateStart = null;
                $this->dateEnd = null;
                $this->dispatch('failedAlert',[ 'failed' => 'No hay datos para esta estación: '.$this->stationName ]);
                $this->stationName = null;
            }
            //dd($this->dataResponse);
    }

    private function resultData(){
        $data = [];
        $searchStation = $this->search;
        if($this->stationId!=null) {
            $this->viewData();
        }

        $data = DB::connection('mysql_pro')
            ->table('dispositivos as d')
            ->orderBy('d.ID','desc')
            ->where(function ($query) use ($searchStation) {
                if($searchStation!=null && $searchStation!=''){
                    $query->where('d.NAME','LIKE', '%'.$searchStation.'%');
                }
            })->paginate($this->perPage);//->limit(50)->get();
            /*
             $data = DB::connection('mysql_pro')
            ->table('dispositivos as d')
            ->join('total_diario as t','d.ID','t.DISPOSITIVO_ID')
            ->orderBy('d.ID','desc')
            //->where('d.ID','=',368)
            ->where(function ($query) use ($searchStation) {
                if($searchStation!=null && $searchStation!=''){
                    $query->where('d.NAME','LIKE', '%'.$searchStation.'%');
                }
            })
            ->where(function ($query2) use ($dateRange) {
                if($dateRange[0]!=null && $dateRange[1]!=null){
                    $query2->where(DB::raw("CONCAT(t.F_YEAR, '-', t.F_MONTH, '-', t.F_DAY)"), '>=', $dateRange[0])
                            ->where(DB::raw("CONCAT(t.F_YEAR, '-', t.F_MONTH, '-', t.F_DAY)"), '<=', $dateRange[1]);
                }
            })
            ->paginate($this->perPage);//->limit(50)->get();
            */


        return $data;
    }

    public function render(){
        return view('livewire.datamanager.resetdata',[
            'data'=> $this->resultData(),
        ]);
    }
}

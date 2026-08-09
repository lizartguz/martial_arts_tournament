<?php

namespace App\Livewire\OrganizeUsers;


use DateTime;
use DateTimeZone;
use Google\Client;
use App\Models\User;
use Livewire\Component;
use App\Models\StationM;
use App\Models\ForecastM;
use Livewire\WithPagination;
use App\Models\NotificationM;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\UserDependencyM;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthValidationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizeUsers extends Component
{
    use WithFileUploads;
    use WithPagination;
    use AuthValidationTrait;
    public $headers;
    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';
    public $perPage = 10;
    public $searchUser = null;
    public $searchUserEnd = null;
    public $searchDate = null;

    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    public $formAdd = false;
    public $formUpdate = false;
    public $notifications = [];  
    public $listTokens = [];
    
    public $idAU = null;
    public $titleAU = null;
    public $descriptionAU = null;
    public $typeAU = null;
    public $metadataAU = null;
    public $reg_dateAU = null;
    public $deadlineAU = null;
    public $linkAU = null;
    public $stateAU = 1;
    public $messageFailed = null;

    public $selectedUserId = null;
    public $selectedUserIdEnd = null;
    public string $rolSeleccionado = '';
    public string $tipoSuscripcion = '';
    
   
    protected $listeners = ['deleteNotif']; 

    public $titleNP=null;
    public $descriptionNP=null;
    public $messageNP = [];

    public function mount(){
        $user = Auth::user();
        $this->validarUsuario($user, []);
        abort_unless($user?->can('data.organize_users.view'), 403);
    }

    public function closeFormButton(){
        $this->formAdd = false;
        $this->formUpdate = false;
    }


    public function addNotification(){  
        date_default_timezone_set('America/La_Paz');
        if(strlen($this->descriptionAU) <= 0){
            $this->dispatch('failedAlert',[ 'failed' => 'Debe agregar una descripción' ]);            
        }else{

            try {                                
                $this->reg_dateAU = date('Y-m-d');  
                list($anio, $mes) = explode("-", $this->descriptionAU);
                $format = intval($mes) . ", " . $anio;              
                DB::beginTransaction();
                try {
                    NotificationM::create([
                        'reg_date' => $this->reg_dateAU,
                        'title' => $this->titleAU,
                        'description' => $format,
                        'deadline' => $this->deadlineAU,
                        'metadata' => $this->metadataAU,
                        'link' => $this->linkAU,
                        'state' => $this->stateAU,
                        'creator_user_id' => Auth::user()->id,
                    ]);
                    DB::commit();
                    ///$response = $this->sendNotification($this->titleAU,$this->descriptionAU);

                    $this->formAdd = false;
                    $this->formUpdate = false;
                    $this->clearVariable();
                    $this->dispatch('successAlert',[ 'success' => 'Notificación guardada!' ]);
                               
                } catch (\Throwable $th) {
                    DB::rollBack(); 
                    //dd($th);
                    $this->dispatch('failedAlert',[ 'failed' => 'No se pudo guardar la notificación. Intente mas tarde.' ]);                
                }                
            } catch (\Throwable $th) {
                //throw $th;
            }
        }          
    }

    
   
    public function visibleAdd(){
        $this->clearVariable();
        $this->formAdd = !$this->formAdd;
        $this->titleAU = "Boletín Semanal";
    }

    public function noVisibleAdd(){
        $this->clearVariable();
        $this->formAdd = false; 
        $this->formUpdate = false;
                 
    }

   
    public function clearVariable(){        
        $this->idAU = null;        
        $this->formUpdate = false; 
        $this->formAdd = false; 
        $this->deadlineAU = false;    
        $this->reg_dateAU = null;
        $this->titleAU = null;
        $this->descriptionAU = null;
        $this->metadataAU = null;
        $this->stateAU = null; 
        $this->linkAU = null;           
    }

    public function deleteNotif($id){
        //public function deleteNotif($data){
        //dd($id);
        //$id = $data['id'];
        DB::beginTransaction();
        try {
            NotificationM::where('id',$id)->delete();
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
            NotificationM::where('id','=',$id)->update(['state'=> $estadoActual==1?0:1]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo cambiar el estado. Intente mas tarde.']);
        }                    
    }

    public function notifPush($id){
        DB::beginTransaction();
        try {
            $data = NotificationM::where('id','=',$id)->first();
            $response = $this->sendNotification($data->title ?? '',$data->description ?? '');
            
            
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo procesar el envio.' ]);                    
        }                    
    }

    private function resultData(){
        $data = [];
        $searchUser = $this->searchUser; 
        
        $data = UserDependencyM::with(['user', 'subscription'])
        ->whereHas('user', function ($query) use ($searchUser) {
            if (!is_null($searchUser) && strlen($searchUser) > 0) {
                $query->where(function ($q) use ($searchUser) {
                    $q->where(DB::raw("CONCAT(COALESCE(name, ''), ' ', COALESCE(lastname, ''))"), 'LIKE', '%' . $searchUser . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchUser . '%');
                });
            }
        })
        ->orderBy($this->sortColumn, $this->sortDirection)
        ->limit($this->perPage)
        ->get();

        return $data;
    }
    
    public function render(){
        return view('livewire.organizeusers.organizeusers',['data' => $this->resultData()]);
    }
}

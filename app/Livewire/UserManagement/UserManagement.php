<?php

namespace App\Livewire\UserManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\SubscriptionM;
use App\Models\StationM;
use App\Models\UsersSubscriptionM;
use App\Models\UserDependencyM;
use App\Models\DetailsSuscriptionM;
use App\Models\HistoricalUsersSubscriptionM;
use App\Models\User;
use App\Models\InvitationDiscountM;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\ErrorMessage;
use DateTime;
use DateInterval;
use Carbon\Carbon;

class UserManagement extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $headers;
    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';
    public $perPage = 10;

    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    public $viewList = false;
    public $formAdd = false;
    public $formUpdate = false;
    
    public $latitud = '-17.784697';
    public $longitud = '-63.182028';
    public $elements = [];
    public $searchStationForAdd = null;
    
    public $referenceUsers = [];
    public $referenceUserId = null;
    public $totalA = 0.0;
    public $nameInvitate = null;
    public $phoneInvitate = null;
    public $durationListAdd = [];
    public $durationitemAdd = null;
    public $subscriptionAdd = null;

    public $beginDateExpiration = null;
    public $endDateExpiration = null;
    public $formExtension = false;

    public $subscriptionsE = [];
    public $subscriptionsIdsE = [];
    public $subscriptionDetailsE = [];
    public $userManagerListE = [];
    public $userManagerIdE = null;

    //Owner
    public $idOwnerE = null;
    public $subscriptionTagOwnerE = '-1';
    public $subscriptionNameOwnerE = null;
    public $business_nameOwnerE = null;
    public $nitOwnerE = null;
    public $ciOwnerE = null;
    public $nameOwnerE = null;
    public $lastnameOwnerE = null;
    public $number_phoneOwnerE = null;
    public $emailOwnerE = null;
    public $passwordOwnerE = null;
    public $stateOwnerE = 1;
    public $pinOwnerE = null;
    public $amountOwnerE = 1;
    public $user_idOwnerE = null;
    public $latitudOwnerE = null;
    public $longitudOwnerE = null;
    

    public $stationIdOwner = null;
    public $chooseStationListRegisteredOwner =[];
    public $chooseStationListUpdateOwner =[];
    public $chooseStationListRegisteredOwnerName = null;
    public $chooseStationListUpdateOwnerName =null;
    public $myStationListOwner =[];
    public $user_typeOwnerE = null;
    public $dateExpiryOwnerE = null;
    public $extensionOwnerE = null;

    //Manager
    public $idE = null;
    public $subscriptionTagE = '-1';
    public $subscriptionNameE = null;
    public $business_nameE = null;
    public $nitE = null;
    public $ciE = null;
    public $nameE = null;
    public $lastnameE = null;
    public $number_phoneE = null;
    public $emailE = null;
    public $passwordE = null;
    public $stateE = 1;
    public $pinE = null;
    public $amountE = 1;
    public $user_idE = null;
    public $latitudE = null;
    public $longitudE = null;
    public $currentPinE = null;
    public $elementsE = [];
    public $userOwnerNameAndLastNameE = null;
    public $userManagerNameAndLastNameE = null;
    public $user_typeE = null;
    public $search=null;
    public $stationIdE = null;
    public $stationNameE = null;
    public $stationNameNewE = null;
    public $dataStationListE = [];
    public $dateExpiryE = null;
    public $extensionE = null;
    public $selectStationE = false;
    
    public  $failedDescription = null;
    public $selectStation=false;    

    protected $listeners = ['verPoint','updateSubscription','delete','setPosition','resetDevice','changeStatusUser','setStationLanLonMarker','verifDiscountInvitate','deleteDiscountInvitate','deleteStationManager','addStationManager','changeStationManager','unLinkStationE','changeStationE','setCoordinates'];
    
    public function mount(){
        $userId = auth()->user()->id;
        //$subscriptionId = UsersSubscriptionM::where('user_id',$userId)->where('state',1)->value('id');
        $data = UserDependencyM::with(['user', 'subscription'])->where('dependent_user_id',$userId)->first();
        $subscriptionId= $data->subscription->id;
        $this->latitud = '-17.784697';
        $this->longitud = '-63.182028';
        $this->visibleUpdate($subscriptionId,$userId);        
    }

    public function refreshUpdateData(){
        $userId = auth()->user()->id;
        $data = UserDependencyM::with(['user', 'subscription'])->where('dependent_user_id',$userId)->first();
        $subscriptionId= $data->subscription->id;      
        $this->visibleUpdate($subscriptionId,$userId);        
    }

    public function setCoordinates($data){
        $this->latitudE = $data['lat'];
        $this->longitudE = $data['lng'];
    }
   
    public function setPosition($data){       
        if($data['type']=='add'){
            $this->latitud = $data['latitud'];
            $this->longitud = $data['longitud'];
        }else{
            $this->latitudE = $data['latitud'];
            $this->longitudE = $data['longitud'];
        }
        
    }

    public function setAmount(){
        $this->elements=[];
        
        $amountSubs = SubscriptionM::where('tag',$this->subscriptionTag)->value('amount');
        for ($i=0; $i < ($this->amount*$amountSubs); $i++) { 
            $this->elements[] = ['id' => $i+1, 'name' => '', 'mail' => ''];
        }
    }

    public function togglePasswordVisibility(){
        $this->attributes['type'] = ($this->attributes['type'] === 'password') ? 'text' : 'password';
    }
    
    public function togglePasswordVisibilityE(){
        $this->attributes['type'] = ($this->attributes['type'] === 'password') ? 'text' : 'password';
    }   

    public function closeFormButton(){
        
        $this->formAdd = false;
        $this->formUpdate = false;
       
        $this->formExtension = false;
        $this->viewList=true;        
        $this->elements=[];
       
      
        $this->latitud = '-17.784697';
        $this->longitud = '-63.182028';

        $this->elementsE = [];
        $this->subscriptionTagE = null;
        $this->business_nameE = null;
        $this->nitE = null;
        $this->ciE = null;
        $this->nameE = null;
        $this->number_phoneE = null;
        $this->emailE = null;
        $this->passwordE= null;
        $this->stateE = null;
        $this->latitudE = null;
        $this->longitudE = null;
        //$this->search = null;

        
      
    }

    public function updateUsersSubscription(){
        $this->validate(
            [
                'business_nameE' => 'max:50',
                'nitE' => 'max:20',
                'nameE' => 'required|min:3|max:50',
                'lastnameE' => 'required|min:3|max:50',
                'ciE' => 'max:15',
                'number_phoneE'=> 'required|min:6|max:15',                
                'passwordE' => 'max:50',
                'emailE' => 'required|min:10|max:150',               
            ], 
            [
                'business_nameE.max' => 'El campo nombre no debe superar los :max caracteres.',
                'nitE.max' => 'El campo nombre no debe superar los :max caracteres.',

                'nameE.required' => 'El campo Nombre es obligatorio.',
                'nameE.min' => 'El campo Nombre debe tener al menos :min caracteres.',
                'nameE.max' => 'El campo Nombre no debe superar los :max caracteres.',

                'lastnameE.required' => 'El campo Apellidos es obligatorio.',
                'lastnameE.min' => 'El campo Apellidos debe tener al menos :min caracteres.',
                'lastnameE.max' => 'El campo Apellidos no debe superar los :max caracteres.',      
                
                'ciE.max' => 'El campo CI no debe superar los :max caracteres.',

                'number_phoneE.required' => 'El campo Nro Teléfono es obligatorio.',
                'number_phoneE.min' => 'El campo Nro Teléfono debe tener al menos :min caracteres.',
                'number_phoneE.max' => 'El campo Nro Teléfono no debe superar los :max caracteres.',

                'emailE.required' => 'El campo Correo es obligatorio.',
                'emailE.min' => 'El campo Correo debe tener al menos :min caracteres.',
                'emailE.max' => 'El campo Correo no debe superar los :max caracteres.',               
                
                'passwordE.max' => 'La contraseña no debe superar los :max caracteres.',
            ],
        );
        $break = $this->checkDuplicatesE();        
        if(!$break){
            DB::beginTransaction();
            try {
                User::where('id',$this->user_idE)
                ->update([
                    'name'=>strlen($this->nameE)>0?$this->nameE:null,
                    'lastname'=>strlen($this->lastnameE)>0?$this->lastnameE:null,
                    'email' => strlen($this->emailE)>0?$this->emailE:null,
                    'ci'=> strlen($this->ciE)>0?$this->ciE:null,
                    'number_phone'=> strlen($this->number_phoneE)>0?$this->number_phoneE:null,
                ]);                
               
                if (!empty($this->passwordE) && strlen($this->passwordE) >= 6) {
                    User::where('id',$this->user_idE)
                    ->update([
                        'password' => Hash::make($this->passwordE),
                    ]);
                }

                if($this->subscriptionTagE == 'personal'){                   
                    UsersSubscriptionM::where('id',$this->idE)
                    ->update([
                        'business_name'=>strlen($this->business_nameE)>0?$this->business_nameE:null,
                        'nit'=>strlen($this->nitE)>0?$this->nitE:null, 
                        'latitude'=> $this->latitudE,
                        'longitude'=> $this->longitudE,
                    ]);                    
                             
                }else if($this->subscriptionTagE == 'professional'){
                    if($this->stationIdE===NULL){
                        UsersSubscriptionM::where('id',$this->idE)
                        ->update([
                            'business_name'=>strlen($this->business_nameE)>0?$this->business_nameE:null,
                            'nit'=>strlen($this->nitE)>0?$this->nitE:null, 
                            'latitude'=> $this->latitudE,
                            'longitude'=> $this->longitudE,
                        ]);
                    }else{                        
                        UsersSubscriptionM::where('id',$this->idE)
                        ->update([
                            'business_name'=>strlen($this->business_nameE)>0?$this->business_nameE:null,
                            'nit'=>strlen($this->nitE)>0?$this->nitE:null,                            
                        ]);
                    }
                    $this->dataStationListE = StationM::Where('state','=',1)->select('id','name')->get();
                
                }else if($this->subscriptionTagE == 'demo personal'){                   
                    UsersSubscriptionM::where('id',$this->idE)
                    ->update([
                        'latitude'=> $this->latitudE,
                        'longitude'=> $this->longitudE,
                    ]);
                                
                }else if($this->subscriptionTagE == 'demo professional'){
                    if($this->stationIdE===NULL){
                        UsersSubscriptionM::where('id',$this->idE)
                        ->update([
                            'latitude'=> $this->latitudE,
                            'longitude'=> $this->longitudE,
                        ]);
                    }
                   // $this->dataStationListE = StationM::Where('state','=',1)->select('id','name')->get();
        
                }else if($this->subscriptionTagE==='corporate'){    
                                
                    if($this->user_typeE==2){
                    
                        if(User::where('user_dependency_id',$this->user_idE)->distinct()->count() > 0){                        
                            for ($i=0; $i < count($this->elementsE); $i++) {
                                $idU = null;
                                $nameU = null;
                                $lastnameU = null;
                                $emailU = null;
                                $number_phoneU = null;
                                $passwordU = null;

                                
                                $nameFlagU = false;
                                $lastnameFlagU = false;
                                $emailFlagU = false;
                                $number_phoneFlagU = false;
                                $passwordFlagU = false;

                                $idE=$this->elementsE[$i]['idE'];
                                $name=$this->elementsE[$i]['nameE'];
                                $original_name=$this->elementsE[$i]['original_nameE'];                       
                                $lastname=$this->elementsE[$i]['lastnameE'];
                                $original_lastname=$this->elementsE[$i]['original_lastnameE'];
                                $email=$this->elementsE[$i]['mailE'];
                                $original_mail=$this->elementsE[$i]['original_mailE'];
                                $number_phone=$this->elementsE[$i]['phoneE'];
                                $original_number_phone=$this->elementsE[$i]['original_phoneE'];
                                $password = $this->elementsE[$i]['passwordE'];
                                $original_password = $this->elementsE[$i]['original_passwordE'];                           
                                
                                $idU = $idE;
                                $nameU = $name;
                                $lastnameU = $lastname;

                                if($name !== $original_name){
                                    if($name !== null && $name !==''){
                                        $nameU = $name;                                                                        
                                    }else{
                                        $nameU = null;
                                    } 
                                    $nameFlagU = true;                               
                                }else{
                                    if($original_name === null || $original_name ===''){
                                        $nameU = null;
                                    }else{
                                        $nameU = $original_name;
                                    }                                
                                }

                                if($lastname !== $original_lastname){
                                    if($lastname !== null && $lastname !==''){
                                        $lastnameU = $lastname;
                                    }else{
                                        $lastnameU = null;
                                    } 
                                    $lastnameFlagU = true;                               
                                }else{
                                    if($original_lastname === null || $original_lastname ===''){
                                        $lastnameU = null;
                                    }else{
                                        $lastnameU = $original_lastname;
                                    }                                
                                }

                                
                                if($password!=='' && $password!==null && strlen($password) >= 6){
                                    $passwordU = Hash::make($password);
                                    $passwordFlagU = true;
                                }else{
                                    if($original_password === null || $original_password ===''){
                                        $passwordU = null;
                                    }else{
                                        $passwordU = $original_lastname;
                                    }                                
                                }

                                if($email !== $original_mail){
                                    if($email !== null && $email !==''){
                                        if(User::where('id','!=',$idE)->where('email',$email)->count() <= 0){
                                            $emailU = $email;
                                        }else{
                                            $emailU = $original_mail;
                                        }                                    
                                    }else{
                                        $emailU = null;
                                    }
                                    $emailFlagU = true;                                
                                }else{
                                    if($original_mail === null || $original_mail ===''){
                                        $emailU = null;
                                    }else{
                                        $emailU = $original_mail;
                                    }                                
                                }

                                if($number_phone !== $original_number_phone){
                                    if($number_phone !== null && $number_phone !==''){
                                        if(User::where('id','!=',$idE)->where('number_phone',$number_phone)->count() <= 0){
                                            $number_phoneU = $number_phone;
                                        }else{
                                            $number_phoneU = $original_number_phone;
                                        }
                                    }else{
                                        $number_phoneU = null;
                                    } 
                                    $number_phoneFlagU = true;                               
                                }else{
                                    if($original_number_phone === null || $original_number_phone ===''){
                                        $number_phoneU = null;
                                    }else{
                                        $number_phoneU = $original_number_phone;
                                    }
                                }


                                if($passwordFlagU == true || $nameFlagU == true || $lastnameFlagU == true || $emailFlagU == true || $number_phoneFlagU == true){
                                    User::where('id',$idU)
                                    ->update([
                                        'name' => $nameU,
                                        'lastname' => $lastnameU,
                                        'email' => $emailU,
                                        'number_phone' =>  $number_phoneU,
                                        'password' =>  $passwordU,
                                    ]);
                                }                                            
                                //DB::commit();                        
                            }
                        }

                        $amountSubs = User::where('user_dependency_id',$this->user_idE)->get();
                        $this->elementsE=[];
                        if( count($amountSubs) > 0 ){
                            
                            $this->loadUsersDependent($amountSubs);                 

                            $this->dispatch('changeSubscriptionStation', [
                                'type' => 'update',
                                'latitud' => $this->latitudE,
                                'longitud' => $this->longitudE,
                                'selectStationStatus' => $this->selectStation,
                            ]);
                        }
                    }                   

                }                
                DB::commit();
                $this->passwordE=null;
                $this->dispatch('successAlert',[ 'success' => 'Cambios guardados exitosamente' ]);
                
            } catch (\Throwable $th) {
                DB::rollBack();
                dd($th);
                $this->dispatch('failedAlert',[ 'failed' => 'No se pudo guardar los cambios' ]);
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);
           
        } 
    }

    public function updateUsersSubscriptionOwner(){
        
        $this->validate(
            [
                'business_nameOwnerE' => 'max:50',
                'nitOwnerE' => 'max:20',
                'nameOwnerE' => 'required|min:3|max:50',
                'lastnameOwnerE' => 'required|min:3|max:50',
                'ciOwnerE' => 'max:15',
                'number_phoneOwnerE'=> 'required|min:6|max:15',               
                'passwordOwnerE' => 'max:50',
                'emailOwnerE' => 'required|min:10|max:150',
                              
            ],
            [
                
                'business_nameOwnerE.max' => 'El campo nombre no debe superar los :max caracteres.',
                
                'nitOwnerE.max' => 'El campo nombre no debe superar los :max caracteres.',

                'nameOwnerE.required' => 'El campo Nombre es obligatorio.',
                'nameOwnerE.min' => 'El campo Nombre debe tener al menos :min caracteres.',
                'nameOwnerE.max' => 'El campo Nombre no debe superar los :max caracteres.',

                'lastnameOwnerE.required' => 'El campo Apellidos es obligatorio.',
                'lastnameOwnerE.min' => 'El campo Apellidos debe tener al menos :min caracteres.',
                'lastnameOwnerE.max' => 'El campo Apellidos no debe superar los :max caracteres.',
                
                'ciOwnerE.required' => 'El campo CI es obligatorio.',
                'ciOwnerE.min' => 'El campo CI debe tener al menos :min caracteres.',
                'ciOwnerE.max' => 'El campo CI no debe superar los :max caracteres.',

                'number_phoneOwnerE.required' => 'El campo Nro Teléfono es obligatorio.',
                'number_phoneOwnerE.min' => 'El campo Nro Teléfono debe tener al menos :min caracteres.',
                'number_phoneOwnerE.max' => 'El campo Nro Teléfono no debe superar los :max caracteres.',                

                'emailOwnerE.required' => 'El campo Correo es obligatorio.',
                'emailOwnerE.min' => 'El campo Correo debe tener al menos :min caracteres.',
                'emailOwnerE.max' => 'El campo Correo no debe superar los :max caracteres.',                
                'passwordOwnerE.max' => 'La contraseña no debe superar los :max caracteres.',
            ],
        );
        

        $break = $this->checkDuplicatesOwnerE();
        
        if(!$break){
           
            try {   
                DB::beginTransaction();

                User::where('id',$this->user_idOwnerE)
                ->update([
                    'name'=>$this->nameOwnerE,
                    'lastname'=>$this->lastnameOwnerE,
                    'email' => $this->emailOwnerE,
                    'ci'=> $this->ciOwnerE,
                    'number_phone'=> $this->number_phoneOwnerE,
                ]);
                if (!empty($this->passwordOwnerE) && strlen($this->passwordOwnerE) >= 6) {
                    User::where('id',$this->user_idOwnerE)
                    ->update([
                        'password' => Hash::make($this->passwordOwnerE),
                    ]);
                }                
                UsersSubscriptionM::where('id',$this->idOwnerE)
                ->update([
                    'nit' => $this->nitOwnerE,
                    'business_name' => $this->business_nameOwnerE,                        
                ]);
                DB::commit();
                $this->passwordOwnerE = null;            
                $this->dispatch('successAlert',[ 'success' => 'Cambios guardados exitosamente' ]);                
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => 'No se pudo aplicar los cambios' ]);  
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);
           
        } 
    }   

    private function checkDuplicates(){
        $break = false;
        if(User::Where('email',$this->email)->count() > 0){
            $this->failedDescription='El correo: '.$this->email.'no puede ser registrado porque ya existe, y esta asignado a otro usuario';
            $break=true;
        }else{
            if(count($this->elements) > 0){
                for ($i=0; $i < count($this->elements); $i++) { 
                        if(strlen($this->elements[$i]['mail']) > 0){
                            if(filter_var($this->elements[$i]['mail'], FILTER_VALIDATE_EMAIL)){
                                if(User::where('email',$this->elements[$i]['mail'])->count() > 0){
                                    $this->failedDescription='El correo: '.$this->elementsE[$i]['mailE'].'no puede registrarse porque ya existe, y esta asignado a otro usuario';
                                    $break=true;
                                    break;
                                }
                            }else{
                                $this->failedDescription='El dato: '.$this->elements[$i]['mail'].' no es un formato de correo válido';
                                $break=true;
                                break;
                            } 
                        }
                        if(strlen($this->elements[$i]['phone']) > 0){
                            if(is_numeric($this->elements[$i]['phone'])){
                                if(User::where('number_phone',$this->elements[$i]['phone'])->count() > 0){
                                    $this->failedDescription='El número de teléfono: '.$this->elements[$i]['phone'].' no puede registrarse porque ya existe, y esta asignado a otro usuario';
                                    $break=true;
                                    break;
                                }
                                
                            }else{
                                $this->failedDescription='El dato: '.$this->elements[$i]['phone'].'  no es un número teléfonico válido';
                                $break=true;
                                break; 
                            } 
                        } 
                        /*if($this->elements[$i]['is_pin']==true){
                            if(strlen($this->elements[$i]['pin']) > 0){
                                if(strlen($this->elements[$i]['pin']) > 3){
                                    if(strlen($this->elements[$i]['pin']) > 50){
                                        $this->failedDescription='El pin: '.$this->elements[$i]['pin'].' no es válido. Debe tener 20 digitos como maximo.';
                                        $break=true;
                                        break;
                                    }
                                } else{
                                    $this->failedDescription='El pin: '.$this->elements[$i]['pin'].' no es válido. Debe tener mas de 3 digitos.';
                                        $break=true;
                                        break;
                                }
                            }
                        } */                   
                }
                
    
            }
        }
        //dd($this->failedDescription);
       
        return $break;
    }

    private function checkDuplicatesE(){
        $break = false;
        if(User::where('id','!=',$this->user_idE)->where('email',$this->emailE)->count() > 0){
            dd(User::where('id','!=',$this->user_idE)->where('email',$this->emailE)->first());
            $this->failedDescription='El correo: '.$this->emailE.' no puede ser registrado porque ya existe, y esta asignado a otro usuario';
            $break=true;
        }else if(User::where('id', '!=', $this->user_idE)->where('number_phone', $this->number_phoneE)->count() > 0){
            $this->failedDescription = 'El número de teléfono: '.$this->number_phoneE.' no puede ser registrado porque ya está asignado a otro usuario';
            $break = true;
        }else if (!empty($this->passwordE) && strlen($this->passwordE) < 6) {
            $this->failedDescription = 'La nueva contraseña debe tener por lo menos 6 o más caracteres';
            $break = true;
        }else{
            if($this->subscriptionTagE==='corporate'){            
                if(count($this->elementsE) > 0){
                    for ($i=0; $i < count($this->elementsE); $i++) { 
                        if(strlen($this->elementsE[$i]['mailE']) > 0){
                            if(filter_var($this->elementsE[$i]['mailE'], FILTER_VALIDATE_EMAIL)){
                                if(User::where('id','!=',$this->elementsE[$i]['idE'])->where('email',$this->elementsE[$i]['mailE'])->count() > 0){
                                    $this->failedDescription='El correo: '.$this->elementsE[$i]['mailE'].' ya existe, y esta asignado a otro usuario';
                                    $break=true;
                                    break;
                                } 
                            }else{
                                $this->failedDescription='El dato: '.$this->elementsE[$i]['mailE'].' no es un formato de correo válido';
                                $break=true;
                                break;
                            } 
                        }                   
                        if(strlen($this->elementsE[$i]['phoneE']) > 0){
                            if(is_numeric($this->elementsE[$i]['phoneE'])){
                                if(User::where('id','!=',$this->elementsE[$i]['idE'])->where('number_phone',$this->elementsE[$i]['phoneE'])->count() > 0){
                                    $this->failedDescription='El número de teléfono: '.$this->elementsE[$i]['phoneE'].'no puede ser registrado porque ya existe, y esta asignado a otro usuario';
                                    $break=true;
                                    break;
                                }
                                
                            }else{
                                $this->failedDescription='El dato: '.$this->elementsE[$i]['phoneE'].' no es un número teléfonico válido';
                                $break=true;
                                break; 
                            } 
                        }                       
                            
                    }
        
                }
            }
        }

       
        return $break;
    }

    private function checkDuplicatesOwnerE(){
        $break = false;
        if(User::where('id','!=',$this->user_idOwnerE)->where('email',$this->emailOwnerE)->count() > 0){
            $this->failedDescription='El correo: '.$this->emailOwnerE.' no puede ser registrado porque ya existe, y esta asignado a otro usuario';
            $break=true;
        }
        
        if(User::where('id', '!=', $this->user_idOwnerE)->where('number_phone', $this->number_phoneOwnerE)->count() > 0){
            $this->failedDescription = 'El número de teléfono: '.$this->number_phoneOwnerE.' no puede ser registrado porque ya está asignado a otro usuario';
            $break = true;
        }

        if (!empty($this->passwordOwnerE) && strlen($this->passwordOwnerE) < 6) {
            $this->failedDescription = 'La nueva contraseña debe tener por lo menos 6 o más caracteres';
            $break = true;  
        }

         

        return $break;
    }

    public function unLinkStationE(){
        DB::beginTransaction();
        try {
            UsersSubscriptionM::where('id',$this->idE)
            ->update(['station_id'=> null, 'latitude'=>'-17.784697', 'longitude'=>'-63.182028']);
            DB::commit(); 
            $this->stationIdE = null;
            $this->stationNameE = null;
            $this->latitudE ='-17.784697';
            $this->longitudE ='-63.182028';
            $this->dataStationListE = StationM::Where('state','=',1)->select('id','name')->get();
            $this->dispatch('successAlert',[ 'success' => 'Desvinculación aplicada exitosamente' ]); 
            $this->dispatch('openMapE',[
                'type' => 'update',
                'latitudE' =>  $this->latitudE,
                'longitudE' => $this->longitudE,
            ]);  
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo aplicar la desvinculación' ]);
        }
    }

    public function changeStationE(){
        DB::beginTransaction();
        try {
            $stationItem = StationM::Where('name',$this->stationNameNewE)->select('id','latitude','longitude')->first();
            if($stationItem!==null){
                UsersSubscriptionM::where('id',$this->idE)
                ->update([
                    'latitude'=> $stationItem->latitude,
                    'longitude'=> $stationItem->longitude,
                    'station_id'=> $stationItem->id,
                ]);
                DB::commit(); 
                $this->stationIdE = $stationItem->id;
                $this->stationNameE = $this->stationNameNewE;
                $this->latitudE = $stationItem->latitude;
                $this->longitudE = $stationItem->longitude;
                $this->stationNameNewE = null;
                $this->dataStationListE = StationM::Where('state','=',1)->select('id','name')->get();
                $this->dispatch('successAlert',[ 'success' => 'Cambio de estación aplicada exitosamente' ]); 
                
                $this->dispatch('openMapE',[
                    'type' => 'update',
                    'latitudE' =>  $this->latitudE,
                    'longitudE' => $this->longitudE,
                ]);  
            }else{
                $this->dispatch('failedAlert',[ 'failed' => 'La estación no existe' ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo aplicar el cambio' ]);
        }
    }

    public function visibleUpdate($id,$userId){
        $this->user_typeOwnerE = null;
        $data = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$id)->where('dependent_user_id',$userId)->first();
        //dd($data,$id,$userId,$data);
        $this->user_typeE = $data->user->user_type;
        $this->userOwnerNameAndLastNameE = null;
        $this->userManagerNameAndLastNameE = null;
        $this->userManagerListE = [];
        $this->subscriptionsIdsE = [];
        $this->subscriptionDetailsE = [];
        $this->subscriptionsE = SubscriptionM::all();
        
        $this->formAdd = false;
        $this->formUpdate = true;
        $this->viewList = false;        
        $this->formExtension = false;
        $this->elementsE = [];
            
        if($data->subscription->subscription->tag=='corporate'){
            if (in_array($this->user_typeE, [null,0,2,3])) {
                $this->idE = $id;
                $this->business_nameE = $data->subscription->business_name;
                $this->nitE = $data->subscription->nit;
                $this->ciE = $data->user!==null?$data->user->ci:null;
                $this->nameE = $data->user!==null?$data->user->name:null;
                $this->lastnameE = $data->user!==null?$data->user->lastname:null;
                $this->number_phoneE = $data->user!==null?$data->user->number_phone:null;
                $this->emailE = $data->user!==null?$data->user->email:null;
                $this->currentPinE = $data->user!==null?$data->user->pin:null;
                $this->subscriptionNameE = $data->subscription->subscription!==null?$data->subscription->subscription->name:null;
                $this->stateE = $data->subscription->state;
                $this->amountE = $data->subscription->amount;
                $this->latitudE = $data->subscription->latitude;
                $this->longitudE = $data->subscription->longitude;
                $this->dateExpiryE = $data->subscription->date_expiry;
                $this->extensionE = $data->subscription->temporary_extension_end;
                $this->user_idE = $data->user->id;
                $this->subscriptionTagE = $data->subscription->subscription!==null?$data->subscription->subscription->tag:null;
                $this->stationIdE = $data->subscription->station_id;
                $this->stationNameNewE = $data->subscription->station!==null?$data->subscription->station->name:null; 

                $this->user_typeOwnerE = null;
                $this->idOwnerE = null;
                $this->business_nameOwnerE = null;
                $this->nitOwnerE = null;
                $this->ciOwnerE = null;
                $this->nameOwnerE = null;
                $this->lastnameOwnerE = null;
                $this->number_phoneOwnerE = null;
                $this->emailOwnerE = null;                
                $this->subscriptionNameOwnerE = null;
                $this->stateOwnerE = null;
                $this->amountOwnerE = null;
                $this->latitudOwnerE = null;
                $this->longitudOwnerE = null;
                $this->dateExpiryOwnerE = null;
                $this->extensionOwnerE = null;
                $this->user_idOwnerE = null;
                $this->subscriptionTagOwnerE = null;
                $this->stationIdOwner = null;  
                $this->userManagerListE =null;
                $this->userManagerIdE = -1;
                $this->chooseStationListRegisteredOwner = null;
                $this->chooseStationListUpdateOwner = null;
                 
                if($this->user_typeE==3){
                    $userManager = User::where('id',$data->user->user_dependency_id)->select('id','name','lastname','user_dependency_id')->first();                    
                    $userOwner = User::where('id',$userManager->user_dependency_id)->select('id','name','lastname','user_dependency_id')->first();                    
                    $this->userOwnerNameAndLastNameE = $userOwner->name.' '.$userOwner->lastname;
                    $this->userManagerNameAndLastNameE = $userManager->name.' '.$userManager->lastname; 
                    $this->dispatch('openMapE',[
                        'type' => 'update',
                        'latitudE' =>  $this->latitudE,
                        'longitudE' => $this->longitudE,
                    ]);                                    
                }
                if($this->user_typeE==2){              
                    $userOwner = User::where('id',$data->user->user_dependency_id)->Select('name','lastname')->first();
                    $this->userOwnerNameAndLastNameE = $userOwner->name.' '.$userOwner->lastname;
                    $amountSubs = User::where('user_dependency_id',$this->user_idE)->get();
                    
                    if( count($amountSubs) > 0 ){
                        $this->loadUsersDependent($amountSubs);
                        
                        if($this->stationIdE!=null){
                            $this->selectStation = true;            
                        }else{
                            $this->selectStation = false; 
                        }
                        $this->dispatch('changeSubscriptionStation', [
                            'type' => 'update',
                            'latitud' => $this->latitudE,
                            'longitud' => $this->longitudE,
                            'selectStationStatus' => $this->selectStation,
                        ]);
                    }else{
                        for ($i=0; $i < 10; $i++) {
                            $subuser  = User::create([
                                'name' => null,
                                'lastname' => null,
                                'email' => null,
                                'number_phone' => null,
                                'password' => null,
                                'image' => 'user_perfil.jpg',                            
                                'device_identifier' => null,                            
                                'access_type' => 1,
                                'user_type' => 3,
                                'payment_state' => 1,
                                'own_state' => 1,
                                'state' => 1,
                                'user_dependency_id' => $this->user_idE,
                            ]);
                            $subuser->assignRole('subscriber');
                            
                            UserDependencyM::firstOrCreate([
                                'user_subscription_id' => $id,
                                'dependent_user_id' => $subuser ->id,
                            ]);
                        }
                        $amountSubs = User::where('user_dependency_id',$this->user_idE)->get();
                        if(count($amountSubs) > 0){
                            $this->loadUsersDependent($amountSubs);
                        }
                    }
    
                    $this->dataStationListE = StationM::Where('state','=',1)->select('id','name')->get();
                    $this->stationIdE = $data->subscription->station_id;
                    $this->stationNameE = $data->subscription->station->name;
                    $this->stationNameNewE = null;
               
                }

            }else{
                if($this->user_typeE==1){                    
                    $this->user_typeOwnerE = $this->user_typeE;
                    $this->idOwnerE = $id;
                    $this->business_nameOwnerE = $data->subscription->business_name;
                    $this->nitOwnerE = $data->subscription->nit;
                    $this->ciOwnerE = $data->user!==null?$data->user->ci:null;
                    $this->nameOwnerE = $data->user!==null?$data->user->name:null;
                    $this->lastnameOwnerE = $data->user!==null?$data->user->lastname:null;
                    $this->number_phoneOwnerE = $data->user!==null?$data->user->number_phone:null;
                    $this->emailOwnerE =$data->user!==null? $data->user->email:null;
                    
                    $this->subscriptionNameOwnerE = $data->subscription->subscription!==null?$data->subscription->subscription->name:null;
                    $this->stateOwnerE = $data->subscription->state;
                    $this->amountOwnerE = $data->subscription->amount;
                    $this->latitudOwnerE = $data->subscription->latitude;
                    $this->longitudOwnerE = $data->subscription->longitude;
                    $this->dateExpiryOwnerE = $data->subscription->date_expiry;
                    $this->extensionOwnerE = $data->subscription->temporary_extension_end;
                    $this->user_idOwnerE = $data->subscription->user_id;
                    $this->subscriptionTagOwnerE = $data->subscription!==null?$data->subscription->subscription->tag:null;
                    $this->stationIdOwner = $data->subscription->station_id;               
                    $this->userManagerIdE = -1;
                    $this->getMyStations();            

                }
            }            
              
           
        }else{

            $this->idE = $id;
            $this->business_nameE = $data->subscription->business_name;
            $this->nitE = $data->subscription->nit;
            $this->ciE = $data->user->ci;
            $this->nameE = $data->user->name;
            $this->lastnameE = $data->user->lastname;
            $this->number_phoneE = $data->user->number_phone;
            $this->emailE = $data->user->email;
            $this->currentPinE = $data->user->pin;
            $this->subscriptionNameE = $data->subscription->name;
            $this->stateE = $data->subscription->state;
            $this->amountE = $data->subscription->amount;
            $this->latitudE = $data->subscription->latitude;
            $this->longitudE = $data->subscription->longitude;
            $this->user_idE = $data->subscription->user_id;
            $this->dateExpiryE = $data->subscription->date_expiry;
            $this->extensionE = $data->subscription->temporary_extension_end;
            $this->subscriptionTagE = $data->subscription->subscription->tag;
            
            if($data->subscription->subscription->tag=='demo professional' || $data->subscription->subscription->tag=='professional'){
                $this->stationIdE = $data->subscription->station_id;
                $this->stationNameE = $data->subscription->station!==null?$data->subscription->station->name:null;
                $this->stationNameNewE = null;
                $this->dataStationListE = StationM::Where('state','=',1)->select('id','name')->get();
            }

            $this->dispatch('openMapE',[
                'type' => 'update',
                'latitudE' =>  $this->latitudE,
                'longitudE' => $this->longitudE,
            ]);
        }     
    }

    public function visibleUpdateManager($id = null,$userId=null){
        if($id ===null){  
            $this->dispatch('failedAlert',[ 'failed' => 'No se ha seleccionado una suscripción para actualizar' ]);
        }else{
            
            $data = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$id)->where('dependent_user_id',$userId)->first(); 
            $this->user_typeE = $data->user->user_type;
            $this->userOwnerNameAndLastNameE = null;
            $this->userManagerNameAndLastNameE = null;        
            $this->subscriptionsIdsE = [];
            $this->subscriptionDetailsE = [];
            $this->subscriptionsE = SubscriptionM::all();            
            $this->formAdd = false;
            $this->formUpdate = true;
            $this->viewList = false;            
            $this->formExtension = false;
            $this->elementsE = [];           
                
            if($data->subscription->subscription->tag=='corporate'){            
                if (in_array($this->user_typeE, [2])) {
                    $this->idE = $data->subscription->id;
                    $this->business_nameE = $data->subscription->business_name;
                    $this->nitE = $data->subscription->nit;
                    $this->ciE = $data->user->ci;
                    $this->nameE = $data->user->name;
                    $this->lastnameE = $data->user->lastname;
                    $this->number_phoneE = $data->user->number_phone;
                    $this->emailE = $data->user->email;
                    $this->currentPinE = $data->user->pin;
                    $this->subscriptionNameE = $data->subscription->subscription!==null?$data->subscription->subscription->name:null;
                    $this->stateE = $data->subscription->state;
                    $this->amountE = $data->subscription->amount;
                    $this->latitudE = $data->subscription->latitude;
                    $this->longitudE = $data->subscription->longitude;
                    $this->dateExpiryE = $data->subscription->date_expiry;
                    $this->extensionE = $data->subscription->temporary_extension_end;
                    $this->user_idE = $data->user->id;
                    $this->userManagerIdE==$data->user->id;
                    $this->subscriptionTagE = $data->subscription->subscription!==null?$data->subscription->subscription->tag:null;
                    $this->stationIdE = $data->subscription->station_id;                
                    $userOwner = User::where('id',$data->user->user_dependency_id)->Select('name','lastname')->first();                    
                    $this->userOwnerNameAndLastNameE = $userOwner->name.' '.$userOwner->lastname;
                    $amountSubs = User::where('user_dependency_id',$this->user_idE)->get();                    
                    //dd($amountSubs,$this->user_idE);
                    if( count($amountSubs) > 0 ){
                        $this->loadUsersDependent($amountSubs);
                    }else{
                        for ($i=0; $i < 10; $i++) {
                            $subuser  = User::create([
                                'name' => null,
                                'lastname' => null,
                                'email' => null,
                                'number_phone' => null,
                                'password' => null,
                                'image' => 'user_perfil.jpg',                            
                                'device_identifier' => null,                            
                                'access_type' => 1,
                                'payment_state' => 1,
                                'user_type' => 3,
                                'own_state' => 1,
                                'state' => 1,
                                'user_dependency_id' => $this->user_idE,
                            ]);
                            $subuser->assignRole('subscriber');
                            UserDependencyM::firstOrCreate([
                                'user_subscription_id' => $id,
                                'dependent_user_id' => $subuser->id,
                            ]);
                        }
                        $amountSubs = User::where('user_dependency_id',$this->user_idE)->get();
                        if(count($amountSubs) > 0){
                            $this->loadUsersDependent($amountSubs);
                        }
                    }
                }
            }else{
                $this->dispatch('openMapE',[
                    'type' => 'update',
                    'latitudE' =>  $this->latitudE,
                    'longitudE' => $this->longitudE,
                ]);
            }            
        }
    }

    private function loadUsersDependent($amountSubs){
        for ($i=0; $i < count($amountSubs); $i++) {
            $this->elementsE[] = [
                'idE' => $amountSubs[$i]->id,
                'nameE' => $amountSubs[$i]->name==null?'':$amountSubs[$i]->name,
                'original_nameE' => $amountSubs[$i]->name==null?'':$amountSubs[$i]->name,
                'lastnameE' => $amountSubs[$i]->lastname==null?'':$amountSubs[$i]->lastname,
                'original_lastnameE' => $amountSubs[$i]->lastname==null?'':$amountSubs[$i]->lastname,
                'mailE' => $amountSubs[$i]->email==null?'':$amountSubs[$i]->email ,
                'original_mailE' => $amountSubs[$i]->email==null?'':$amountSubs[$i]->email ,
                'phoneE' => $amountSubs[$i]->number_phone==null?'':$amountSubs[$i]->number_phone,
                'original_phoneE' => $amountSubs[$i]->number_phone==null?'':$amountSubs[$i]->number_phone,
                'is_password'=> $amountSubs[$i]->password==null?'No tiene contraseña':'Ya tiene contraseña',
                'passwordE'=> '',
                'original_passwordE'=> $amountSubs[$i]->password,
                'stateE' => $amountSubs[$i]->state==null?'0':$amountSubs[$i]->state,
            ];
        }
    }

    public function addStationManager($data){  
        DB::beginTransaction();
        try {
            if(UsersSubscriptionM::Where('users_subscriptions.id',$data['id'])->whereNull('station_id')->exists()){
                $registedStation = StationM::where('name',$this->chooseStationListUpdateOwnerName)->select('id','latitude','longitude')->first();
                if($registedStation!=null){
                    if(UsersSubscriptionM::where('user_id',$this->idOwnerE)->where('station_id',$registedStation->id)->exists()){
                        $this->dispatch('failedAlert',[ 'failed' => 'La estación a cambiar ya esta vinculada con otro usuario administrador' ]);
                    }else{
                        UsersSubscriptionM::where('id',$data['id'])
                        ->update([
                            'latitude' => $registedStation->latitude,
                            'longitude' => $registedStation->longitude,
                            'station_id' => $registedStation->id,
                        ]);                     
                        DB::commit(); 
                        $this->chooseStationListUpdateOwnerName = null;
                        $this->getMyStations();
                        $this->dispatch('successAlert',[ 'success' => 'Vinculación aplicada exitosamente' ]);   
                    }         
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => 'La estación no existe' ]);
                }
            }else{
                $this->dispatch('failedAlert',[ 'failed' => 'La suscripción ya tiene estación asignada' ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo aplicar la vinculación' ]);
        }

    }

    public function changeStationManager($data){     
        DB::beginTransaction();
        try {            
            if(UsersSubscriptionM::Where('users_subscriptions.id',$data['id'])->where('station_id',$data['stationId'])->exists()){
                $registedStation = StationM::where('name',$this->chooseStationListUpdateOwnerName)->select('id','latitude','longitude')->first();
                
                if($registedStation!==null){
                    if(UsersSubscriptionM::where('user_id',$this->idOwnerE)->where('station_id',$registedStation->id)->exists()){
                        $this->dispatch('failedAlert',[ 'failed' => 'La estación a cambiar ya esta vinculada con otro usuario administrador' ]);
                    }else{                       
                        UsersSubscriptionM::where('id',$data['id'])
                        ->update([
                            'latitude' => $registedStation->latitude,
                            'longitude' => $registedStation->longitude,
                            'station_id' => $registedStation->id,
                        ]);                        
                        DB::commit(); 
                        $this->chooseStationListUpdateOwnerName = null;
                        $this->getMyStations();
                        $this->dispatch('successAlert',[ 'success' => 'Cambio de vinculación de estación exitosa' ]);
                    }                
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => 'La estación no existe' ]);
                }
            }else{
                $this->dispatch('failedAlert',[ 'failed' => 'No existe la estación actual vinculada a esta suscripción' ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => ErrorMessage::forUser($th, 'No se pudo cambiar la vinculación.') ]);
        }

    }


    public function deleteStationManager($data){   
        DB::beginTransaction();
        try {            
            if(UsersSubscriptionM::Where('users_subscriptions.id',$data['id'])->where('station_id',$data['stationId'])->exists()){
                UsersSubscriptionM::where('id',$data['id'])
                ->update([
                    'latitude' => '-17.784100',
                    'longitude' => '-63.185344',
                    'station_id' => null,
                ]);                
                DB::commit(); 
                $ids = UsersSubscriptionM::where('user_id', $this->user_idOwnerE)->pluck('id')->toArray();
                $this->chooseStationListRegisteredOwner = UserDependencyM::leftJoin('users_subscriptions as us', 'user_dependency.user_subscription_id', 'us.id')
                ->leftJoin('users as u', 'user_dependency.dependent_user_id', 'u.id')
                ->leftJoin('stations as s', 'us.station_id', 's.id')
                ->whereIn('us.id', $ids)
                ->where(function ($query) {
                    $query->where('u.user_type', 2)->orWhereNull('user_dependency.dependent_user_id');
                })
                ->select(
                    's.id as stationId',
                    's.name as station',
                    'u.id as userId',
                    'u.name',
                    'u.lastname',
                    'us.id',
                    'us.date_expiry',
                    'us.temporary_extension_start',
                    'us.temporary_extension_end',
                    'us.is_it_paid_renewal'
                )
                ->get()->toArray();
                $this->getMyStations();
                $this->dispatch('successAlert',[ 'success' => 'Desvinculación de estación exitosa' ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se pudo aplicar la desvinculación' ]);
        }
    }

    private function getMyStations(){
        $ids = UsersSubscriptionM::where('user_id', $this->user_idOwnerE)->pluck('id')->toArray();
        $this->chooseStationListRegisteredOwner = UserDependencyM::leftJoin('users_subscriptions as us', 'user_dependency.user_subscription_id', 'us.id')
            ->leftJoin('users as u', 'user_dependency.dependent_user_id', 'u.id')
            ->leftJoin('stations as s', 'us.station_id', 's.id')
            ->whereIn('us.id', $ids)
            ->where(function ($query) {
                $query->where('u.user_type', 2)->orWhereNull('user_dependency.dependent_user_id');
            })
            ->select(
                's.id as stationId',
                's.name as station',
                'u.id as userId',
                'u.name',
                'u.lastname',
                'us.id',
                'us.date_expiry',
                'us.temporary_extension_start',
                'us.temporary_extension_end',
                'us.is_it_paid_renewal'
            )
            ->get()->toArray();
            $this->chooseStationListUpdateOwner = StationM::Where('stations.state',1)
            ->select('stations.name')
            ->get()->toArray();
    }

    public function closedUpdate(){
       
        $this->formAdd=false;
        $this->formUpdate=false;
        $this->viewList = true;        
        $this->formExtension = false;

        $this->elementsE = [];
        $this->subscriptionTagE = null;
        $this->business_nameE = null;
        $this->nitE = null;
        $this->ciE = null;
        $this->nameE = null;
        $this->number_phoneE = null;
        $this->emailE = null;
        $this->passwordE= null;
        $this->stateE = null;
        $this->latitudE = null;
        $this->longitudE = null;    
      
    }

    public function delete($data){
        DB::beginTransaction();
        try {
            $userId = UsersSubscriptionM::where('id',$data['id'])->value('user_id');
            UserDependencyM::where('dependent_user_id',$userId)->delete();
            HistoricalUsersSubscriptionM::where('user_subscription_id',$data['id'])->delete();
            UsersSubscriptionM::where('id',$data['id'])->delete();
            User::where('id',$userId)->delete();
            DB::commit();
            session()->flash('message', 'Eliminación exitosa!');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('failed', 'No se pudo Eliminar. Intente mas tarde');
        }
    }

    public function changeStatus($id,$estadoActual){
        DB::beginTransaction();
        try {
            UsersSubscriptionM::where('id','=',$id)
                ->update([
                    'state'=> $estadoActual==1?0:1,
                ]);
            DB::commit();
            //session()->flash('message', 'Se actualizo el estado!');
            ////$this->dispatch('ocultar-alert');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('failed', 'No se pudo cambiar el estado. Intente mas tarde.');            
        }
    }

    public function changeStatusUser($data){ 
        DB::beginTransaction();
        try { 
            User::where('id',$data['userId'])->update(['state' => $data['actualState']==1?0:1,]);
            DB::commit(); 
            if($data['type']=='owner'){
                $this->stateOwnerE = $data['actualState']==1?0:1;
            }else if($data['type']=='manager'){
                $this->stateE = $data['actualState']==1?0:1;
            }else if($data['type']=='delegate'){
                $this->elementsE[$data['position']]['stateE']=$data['actualState']==1?0:1;
            }
                
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se ha podido cambiar el estado' ]);                   
        }
    }

    public function resetDevice($data){                                                                                                                
        DB::beginTransaction();
        try {            
            User::where('id',$data['userId'])->update(['device_identifier' => null]);                   
            DB::commit();            
            $this->dispatch('successAlert',[ 'success' => 'reseteo de dispositivo exitoso' ]); 
        } catch (\Throwable $th) {            
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se ha podido aplicar el reseteo al dispositivo' ]); 
        }
    }

    private function resultData(){
        $data = [];
        if($this->user_typeOwnerE==1){
            
            $ids = UsersSubscriptionM::where('user_id', $this->user_idOwnerE)->pluck('id')->toArray();
            $this->chooseStationListRegisteredOwner = UserDependencyM::leftJoin('users_subscriptions as us', 'user_dependency.user_subscription_id', 'us.id')
            ->leftJoin('users as u', 'user_dependency.dependent_user_id', 'u.id')
            ->leftJoin('stations as s', 'us.station_id', 's.id')
            ->whereIn('us.id', $ids)
            ->where(function ($query) {
                $query->where('u.user_type', 2)->orWhereNull('user_dependency.dependent_user_id');
            })
            ->select(
                's.id as stationId',
                's.name as station',
                'u.id as userId',
                'u.name',
                'u.lastname',
                'us.id',
                'us.date_expiry',
                'us.temporary_extension_start',
                'us.temporary_extension_end',
                'us.is_it_paid_renewal'
            )
            ->get();

            $this->chooseStationListUpdateOwner = StationM::Where('stations.state',1)->select('stations.name')->get();
            $this->userManagerListE = UsersSubscriptionM::join('users', 'users.id', '=', 'users_subscriptions.user_id')
            ->leftJoin('stations', 'stations.id', '=', 'users_subscriptions.station_id')
            ->where('users.user_dependency_id', $this->user_idOwnerE)
            ->select(
                'users.id',
                DB::raw("COALESCE(CONCAT(users.name, ' ', users.lastname), 'Sin Nombre') AS full_name"),
                DB::raw("COALESCE(stations.id, 0) AS station_id"),
                DB::raw("COALESCE(stations.name, 'Sin Estación') AS station_name")
            )
            ->get();
        }
            
          
        return $data;
    }    
    
    public function render(){
        return view('livewire.user_management.user-management',['data' => $this->resultData()]);
    }
}

<?php

namespace App\Livewire\UsersSubscriptions;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\SubscriptionM;
use App\Models\StationM;
use App\Models\UsersSubscriptionM;
use App\Models\UserDependencyM;
use App\Models\HistoricalUsersSubscriptionM;
use App\Models\User;
use App\Models\InvitationDiscountM;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\AuthValidationTrait;
use App\Support\ErrorMessage;
use DateTime;
use DateInterval;
use Carbon\Carbon;

class UsersSubscriptions extends Component
{
    use WithFileUploads;
    use WithPagination;
    use AuthValidationTrait;
    public $headers;
    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';
    public $perPage = 10;

    public $stateContribution = 'all';
    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    public $viewList = false;
    public $formAdd = false;
    public $formUpdate = false;
    //SUBSCRIPTION DEMO
    public $freeDateStart = null;
    public $freeDateEnd = null;

    public $subscriber_user_id = null;

    public $subscriptions = [];
    public $subscriptionsIds = [];
    public $subscriptionDetails = [];
    public $specialDiscount = [];
    public $specialDiscountId =  "-1";
    public $subscriptionTag = '-1';
    public $business_name = null;
    public $nit = null;
    public $ci = null;
    public $name = null;
    public $lastname = null;
    public $number_phone = null;
    public $email = null;
    public $password = null;
    public $pin = null;
    public $state = 1;
    public $amount = 1;
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
    
    public $currentPinOwnerE = null;
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
    

    public $selectStation=false;
    public $dataRenewal = null;
    public $idR = null;
    public $durationR = null;
    public $expiredDateR = null;
    public $newDateBeginR = null;
    public $newDateEndR = null;
    public $numberOfYearsR = null;
    public $formRenewal = false;
    public $numberStationsDiscountR = null;
    public $discountValueStationsR = null;

    public $is_discountNumberStationsCheckR = false;
    public $is_discountDurationCheckR = false;    
    public $is_discountIncidenceCheckR = false;

    public $discountNumberStationsCheckR = false;
    public $discountDurationCheckR = false;    
    public $discountIncidenceCheckR = false;

    public $durationValueDiscountR = null;
    public $durationDiscountR = null;
    public $totalR = 0.0;
    public $totalRRegistered = null;
    public $totalRWithExpiration = null;
    public $flagDiscountDuration = false;
    public $invitateUsersR = [];
    public $listYearsR = [];

    //DISCOUNT FOR REGISTER
    public $discountByStation = false;
    public $discountByDuration = false;
    public $discountByrecommendation = false;

    //DISCOUNT INCIDENCE
    public $discountByIncidence = false;
    public $dateStartDiscountByIncidence = null;    
    public $dateEndDiscountByIncidence = null;
    public $dataIncidence = [];
    public $daysDiscountByIncidence = 1; 

    //DISCOUNT PORROGA
    public $temporaryExtension = false;
    
    //public $discountByIncidence = false;
    //public $dateStartDiscountByIncidence = false;    
    //public $dateEndDiscountByIncidence = false;
   

    public $dataNewUser = null;
    public $formAddNewUser = false;
    public $newUserName = null;
    public $newUserEmail = null;
    public $newUserPhone = null;
    public $newUserPassword = null;
    public $is_discountNU = null;
    public $totalNU = 0;
    public $costNewUser;

    
    public $stationId=null;
    public $dataHistorical = [];
    public $failedDescription = null;
    
    // FORMULARIO DE DESCUENTO POR INVITACION
    public $invitateUsers = [];
    public $dataSaveInvitateUser = null;
    public $totalInvitateValue = 0.0;
    public $totalInvitateValueR = 0.0;
    public $totalInvitatePercentageR = 0.0;
    public $flagDiscountByReferenceInvitateR = false;

    //VIEW-EXTENSION
    public $idExt = null;
    public $idUserExt = null;
    public $newDateBeginExt = null;
    public $newDateEndExt = null;
    public $numberOfYearsExt = null;    
    public $dateStartDiscountByExtension = null;
    public $dateEndDiscountByExtension = null;
    public $dataExtension = null;
    public $temporaryExtensionExt = false;
    public $date_expiryExt = null;
    public $extensionAlertWarningExt = false; // Se activa cuando no se puede agregar una prorroga por que aun no a caducado la suscripcion
  

    protected $listeners = ['verPoint','updateSubscription','delete','setPosition','resetDevice','changeStatusUser','setStationLanLonMarker','verifDiscountInvitate','deleteDiscountInvitate','deleteStationManager','addStationManager','changeStationManager','unLinkStationE','changeStationE','setCoordinates'];
    
    public function mount(){
        $user = Auth::user();
        $this->validarUsuario($user, []);
        abort_unless($user?->can('sales_subscribers.view'), 403);

        $this->latitud = '-17.784697';
        $this->longitud = '-63.182028';
        $this->subscriptions = SubscriptionM::where('state',1)->get();
        $this->viewList = true;
        $this->numberOfYearsR = 1;
        $this->costNewUser = 25;

    }

    public function setCoordinates($data){
        $this->latitudE = $data['lat'];
        $this->longitudE = $data['lng'];
    }

    public function validateDateFree(){
        if($this->freeDateStart==null && $this->freeDateEnd==null){
            $this->freeDateStart = date("Y-m-d");
            $this->freeDateEnd = date('Y-m-d', strtotime($this->freeDateStart . ' + 6 days'));
        }else if($this->freeDateStart!=null && $this->freeDateEnd==null){
            if (strtotime($this->freeDateStart) < strtotime(date("Y-m-d"))) {
                $this->freeDateStart = date("Y-m-d");
                $this->freeDateEnd = date('Y-m-d', strtotime($this->freeDateStart . ' + 6 days'));
            }else{
                $this->freeDateEnd = date('Y-m-d', strtotime($this->freeDateStart . ' + 6 days'));
            }
        }else if($this->freeDateStart==null && $this->freeDateEnd!=null){
            $this->freeDateStart = date("Y-m-d");
            $this->freeDateEnd = date('Y-m-d', strtotime($this->freeDateStart . ' + 6 days'));
        }else{
            if($this->freeDateStart!=null && $this->freeDateEnd!=null){
                if (strtotime($this->freeDateStart) > strtotime($this->freeDateEnd)) {
                    $this->freeDateEnd = date('Y-m-d', strtotime($this->freeDateStart . ' + 6 days'));
                }
            }
        }

        $date_start = new DateTime($this->freeDateStart);
        $date_end = new DateTime($this->freeDateEnd);
    }

    public function verifDiscountInvitate(){
        $this->invitateUsers = [];
        $this->totalInvitateValue = 0;
        $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.no_invitation_discount') ]);
    }

    public function applyDiscountInvitateRenewal(){
        $this->invitateUsersR = [];
        $this->flagDiscountByReferenceInvitateR = false; 
        $this->totalInvitatePercentageR = 0;
        $this->totalInvitateValueR = 0;
    }

    public function deleteDiscountInvitate(){
        $this->totalA = SubscriptionM::where('tag',$this->subscriptionTag)->value('price');
        $this->invitateUsers = [];
        $this->totalInvitateValue = 0.0;
    }

    public function applydiscountByIncidence(){
        $this->discountByIncidence = !$this->discountByIncidence;
    }

    public function setStationSelect(){
            $this->selectStation = false;
            $this->latitud = '-17.784697';
            $this->longitud = '-63.182028';
            $this->dispatch('changeSubscriptionStation', [
                'type' => 'add',
                'latitud' => $this->latitud,
                'longitud' => $this->longitud,
                'selectStationStatus' => $this->selectStation,
            ]);
            $this->stationId = null;

    }

    public function setStationLanLonMarker($data){
        $data = json_decode($data);       
        $this->stationId = $data->id;
        if($data->type=='add'){
            $this->latitud = $data->latitude;
            $this->longitud = $data->longitude;
        }else{
            $this->latitudE = $data->latitude;
            $this->longitudE = $data->longitude;
        }
        $this->selectStation = true;       
        $this->dispatch('changeSubscriptionStation', [
            'type' =>  $data->type,
            'latitud' => $data->latitude,
            'longitud' => $data->longitude,
            'selectStationStatus' => $this->selectStation,
        ]);
    }

    public function historical($id){
        $this->dataHistorical = HistoricalUsersSubscriptionM::Where('user_subscription_id',$id)->orderBy('id','desc')->select('start_date','date_expiry','start_date')->get();
    }

    public function viewRenewal($id){
        $this->formAdd = false;
        $this->formUpdate = false;
        $this->viewList = false;
        $this->formRenewal = true;
        $this->formExtension = false;
        $this->listYearsR = [];            
        $this->numberOfYearsR = 1;
        for($i=$this->numberOfYearsR; $i <= (10); $i++){
            $this->listYearsR[] = $i;
        }
        $this->dataRenewal = UsersSubscriptionM::with(['user', 'subscription'])->where('users_subscriptions.id',$id)->first();
        $this->idR = $id;
        $this->numberStationsDiscountR = $this->dataRenewal->subscription->number_stations_discount;
        $this->expiredDateR = $this->dataRenewal->date_expiry;        
        $this->totalRRegistered = $this->dataRenewal->subscription->price;
        $this->discountValueStationsR = $this->dataRenewal->subscription->discount_value_stations;
        $this->newDateBeginR = date('Y-m-d');
        
        if(($this->discountValueStationsR > 0) && (UsersSubscriptionM::where('user_id',$this->dataRenewal->user->id)->count() >= $this->numberStationsDiscountR)){
            $this->is_discountNumberStationsCheckR = true;
        }
        if($this->dataRenewal->subscription->duration_discount > 0){
            if($this->numberOfYearsR >= $this->dataRenewal->subscription->duration_discount){
                $this->is_discountDurationCheckR = true;
                $this->durationValueDiscountR = $this->dataRenewal->subscription->duration_value_discount;
            }
        }
        if($this->dataRenewal->temporary_extension_start!=null && $this->dataRenewal->temporary_extension_end!=null){
            $this->temporaryExtension = true;
        }else{
            $this->temporaryExtension = false;
        }
        $this->listYearsR = [];            
        $this->numberOfYearsR = 1;
        for($i=$this->numberOfYearsR; $i <= (10); $i++){
            $this->listYearsR[] = $i;
        }
        if($this->temporaryExtension){
            $temporary_extension_start = new DateTime($this->dataRenewal->temporary_extension_start);
            $temporary_extension_end = new DateTime($this->dataRenewal->temporary_extension_end);
            $interval = $temporary_extension_start->diff($temporary_extension_end);
            $days = $interval->days;
            if($days==0){
                $days=1;
            }
            $this->totalRWithExpiration = round(( ($this->dataRenewal->subscription->price * $days / (365*$this->dataRenewal->subscription->duration))), 2);
            $this->totalR =  $this->totalR + $this->totalRWithExpiration;
            $date_aux = $temporary_extension_end;
            $date_aux->modify('+1 days');
            $this->newDateBeginR = $date_aux->format('Y-m-d');
            $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');            
            $this->newDateEndR = $date_aux->format('Y-m-d');
            $newDateBeginR_aux = new DateTime( $this->newDateBeginR);
            $newDateEndR_aux = new DateTime($this->newDateEndR);           
            $interval_aux = $newDateBeginR_aux->diff($newDateEndR_aux);
            $days_aux = $interval_aux->days;
            $this->totalR = $this->totalR + round(( ($this->dataRenewal->subscription->price * $days_aux / (365*$this->dataRenewal->subscription->duration))), 2);
        }else{            
            $expired_date = new DateTime($this->expiredDateR);       
            $expired_date->modify('+1 days');
            $this->newDateBeginR = $expired_date->format('Y-m-d');
            $expired_date->modify('+'.(string)$this->numberOfYearsR.' year');            
            $this->newDateEndR = $expired_date->format('Y-m-d');
            /*$this->initialDateAssignment();
            $this->listYearsR = [];            
            $this->numberOfYearsR = 1;
            for($i=$this->numberOfYearsR; $i <= (10); $i++){
                $this->listYearsR[] = $i;
            }*/
        }
        //$this->dateStartDiscountByIncidence = $this->newDateBeginR;
        //$this->dateEndDiscountByIncidence = $this->newDateBeginR;
        //$this->applyDiscountInvitateRenewal();
    }

    public function initialDateAssignment(){
        $expired_date = new DateTime($this->expiredDateR);
        $new_date = new DateTime($this->newDateBeginR); 
        if ($expired_date > $new_date) {
            $date_aux = $expired_date;
            $date_aux->modify('+1 days');
            $this->newDateBeginR = $date_aux->format('Y-m-d');
            $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');
            $date_aux->modify('-1 days');
            $this->newDateEndR = $date_aux->format('Y-m-d');
        }else if($new_date > $expired_date){
            $newDateBeginR = new DateTime($this->newDateBeginR); 
            $newDateBeginR->modify('+'.(string)$this->numberOfYearsR.' year');
            $newDateBeginR->modify('-1 days');
            $this->newDateEndR = $newDateBeginR->format('Y-m-d');
        }else{
            if($new_date == $expired_date){
                $date_aux = $new_date;
                $date_aux->modify('+1 days');
                $this->newDateBeginR = $date_aux->format('Y-m-d');          
                $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');
                $date_aux->modify('-1 days');            
                $this->newDateEndR = $date_aux->format('Y-m-d');
            }
        }
    }

    public function adjustDiffDays($days){
        $this->listYearsR = [];
        $year = ($days * 1) / 365;
        $year = ceil($year);
        if($this->numberOfYearsR < $year){
            $this->numberOfYearsR = $year;
        }        
        for($i=$year; $i < ($year + 10); $i++){
            $this->listYearsR[] = $i;
        }
        $this->initialDateAssignment();
        $date_aux = new DateTime($this->newDateEndR);                   
        $date_aux->sub(new DateInterval('P' . $days . 'D'));
        $this->newDateEndR = $date_aux->format('Y-m-d');
    }

    public function viewExtension($id,$userId){        
        $this->formAdd = false;
        $this->formUpdate = false;
        $this->viewList = false;
        $this->formRenewal = false;
        $this->formExtension = true;
        $this->temporaryExtensionExt = false;
        $this->dataExtension = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$id)->where('dependent_user_id',$userId)->first();
        //dd($this->dataExtension);
        $this->idExt = $id;
        $this->idUserExt = $userId;        
        if($this->dataExtension->subscription->temporary_extension_start!=null && $this->dataExtension->subscription->temporary_extension_end!=null){
            $this->temporaryExtensionExt = true;
            $start_date = new DateTime($this->dataExtension->subscription->temporary_extension_end);                        
            $start_date->modify('+1 days');            
            $this->newDateBeginExt = $start_date->format('Y-m-d');
            $this->newDateEndExt = $this->newDateBeginExt;
        }else{
            if($this->dataExtension->subscription->date_expiry!=null){
                $start_date = new DateTime($this->dataExtension->subscription->date_expiry);
                $start_date->modify('+1 days');
            }else{
                $start_date = new DateTime();
            }  
            $this->newDateBeginExt = $start_date->format('Y-m-d');
            $this->newDateEndExt = $this->newDateBeginExt;        
        }
        $this->extensionAlertWarningExt = false;

        //dd($this->newDateBeginExt,$this->newDateEndExt);
      
    }

    public function viewNewStation($id){
        
        $this->visibleAdd();
        $this->subscriber_user_id = UsersSubscriptionM::Where('id','=',$id)->value('user_id');
       
       $data = UsersSubscriptionM::with(['user', 'subscription'])->where('id','=',$id)->first();
       //dd($data);
       $this->business_name = $data->business_name;
       $this->nit = $data->nit;
       $this->name = $data->user->name;
       $this->ci = $data->user->ci;
       $this->number_phone  = $data->user->number_phone;
       $this->subscriptionTag = $data->subscription->tag;

       $this->changeTypeSubscription();
    }

    public function viewNewUser($id){
        $this->formAdd = false;
        $this->formUpdate = false;
        $this->viewList = false;
        $this->formRenewal = false;
        $this->formExtension = false;
        $this->formAddNewUser = true;
        $this->is_discountNU = false;
        $this->dataNewUser = UsersSubscriptionM::with(['user', 'subscription'])->where('users_subscriptions.id',$id)->first();
        $this->totalNU = $this->dataNewUser->subscription->price;        
        if(($this->dataNewUser->subscription->discount > 0) && (UsersSubscriptionM::where('user_id',$this->dataNewUser->user->id)->count() >= $this->dataNewUser->subscription->discount_from)){

            $this->is_discountNU = true;            
            $this->totalNU = ($this->costNewUser * $this->dataNewUser->amount) * ($this->dataNewUser->subscription->discount / 100);
        
        }else{
            $this->totalNU = ($this->costNewUser * $this->dataNewUser->amount);
        }
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

    public function changeTypeSubscription(){
        $this->elements = [];
        if($this->subscriptionTag=="-1"){
            $this->totalA = 0;
        }else{
            $this->subscriptionAdd=null;
            try {
                $this->subscriptionAdd = DB::table('subscriptions')->where('tag','=',$this->subscriptionTag)->first();
                //dd('aa',$this->subscriptionAdd);        
                if($this->subscriptionAdd!=null){
                    //dd('cc',$this->subscriptionAdd);       
                    $this->specialDiscount = [];
                    if($this->subscriptionAdd->duration > 0){
                       $this->totalA = ($this->subscriptionAdd->price / $this->subscriptionAdd->duration) * $this->durationitemAdd;
                    }else{
                        $this->totalA = 0; 
                    }                    
                    $this->amount = $this->subscriptionAdd->amount;
                    
                    if($this->subscriptionTag=='corporate'){            
                        for ($i=0; $i < ($this->amount); $i++) {
                            $this->elements[] = [
                                'id' => $i+1, 
                                'name' => '',
                                'lastname' => '', 
                                'mail' => '',
                                'phone' => '',
                                'password' => '',
                                'is_pin' => false,
                                'pin' => '',
                            ];
                        }
                        $this->elements[0]['is_pin'] = true;
                    }else{

                        if($this->subscriptionTag=='demo personal' || $this->subscriptionTag=='demo professional'){
                            $this->freeDateStart = date("Y-m-d");
                            $this->freeDateEnd = date('Y-m-d', strtotime($this->freeDateStart . ' + 6 days'));
                        }
                    }
                    
                    
                          
                    $this->dispatch('changeSubscription', [
                        'type' => 'add',
                        'latitud' => $this->latitud, //-17.784697
                        'longitud' => $this->longitud,//-63.182028
                    ]);
                    //dd($this->latitud,$this->longitud);
                }
                

            } catch (\Throwable $th) {
                //throw $th;
                
            }
        }

        //dd($this->subscriptionTag);
    }

    public function togglePasswordVisibility(){
        $this->attributes['type'] = ($this->attributes['type'] === 'password') ? 'text' : 'password';
    }
    
    public function togglePasswordVisibilityE(){
        $this->attributes['type'] = ($this->attributes['type'] === 'password') ? 'text' : 'password';
    }

    public function save(){
        //dd($this->invitateUsers);
        $this->validate(
            [
                'business_name' => 'max:50',
                'nit' => 'max:20',
                'name' => 'required|min:3|max:50',
                'lastname' => 'required|min:3|max:50',
                'ci' => 'required|min:6|max:15',
                'number_phone'=> 'required|min:6|max:15',
                'subscriptionTag' => 'not_in:-1',
                'password' => 'required|min:6|max:50',
                //'pin' => 'required|min:4|max:20',
               
                'email' => 'required|min:10|max:150',
                'password' => 'required|min:6|max:255',
            ], 
            [
                'business_name.min' => __('messages.subscriptions.validation.business_name_min'),
                'business_name.max' => __('messages.subscriptions.validation.business_name_max'),

                'nit.min' => __('messages.subscriptions.validation.nit_min'),
                'nit.max' => __('messages.subscriptions.validation.nit_max'),

                'name.required' => __('messages.subscriptions.validation.name_required'),
                'name.min' => __('messages.subscriptions.validation.name_min'),
                'name.max' => __('messages.subscriptions.validation.name_max'),
                
                'ci.required' => __('messages.subscriptions.validation.ci_required'),
                'ci.min' => __('messages.subscriptions.validation.ci_min'),
                'ci.max' => __('messages.subscriptions.validation.ci_max'),

                'number_phone.required' => __('messages.subscriptions.validation.number_phone_required'),
                'number_phone.min' => __('messages.subscriptions.validation.number_phone_min'),
                'number_phone.max' => __('messages.subscriptions.validation.number_phone_max'),

                'subscriptionTag.not_in' => __('messages.subscriptions.validation.subscription_required'),

                'email.required' => __('messages.subscriptions.validation.email_required'),
                'email.min' => __('messages.subscriptions.validation.email_min'),
                'email.max' => __('messages.subscriptions.validation.email_max'),

                'password.required' => __('messages.subscriptions.validation.password_required'),
                'password.min' => __('messages.subscriptions.validation.password_min'),
                'password.max' => __('messages.subscriptions.validation.password_max'),

                //'pin.required' => __('messages.subscriptions.validation.pin_required'),
                //'pin.min' => __('messages.subscriptions.validation.pin_min'),
                //'pin.max' => __('messages.subscriptions.validation.pin_max'),
            ],
        );
        $break = $this->checkDuplicates();
       
        if(!$break){
            $subscript = SubscriptionM::where('tag',$this->subscriptionTag)->first();
            $reg_date = date('Y-m-d');
            
            $fecha_obj = new DateTime($reg_date);
            $fecha_obj->modify('+'.(string)(($subscript->duration*12)-1).' months');
            $fecha_obj->modify('-1 days');
            $expiration_notice = $fecha_obj->format('Y-m-d');

            $fecha_obj2 = new DateTime($reg_date);
            $fecha_obj2->modify('+'.(string)$subscript->duration.' year');
            $fecha_obj2->modify('-1 days');
            $date_expiry = $fecha_obj2->format('Y-m-d');

            DB::beginTransaction();
            try {
                $user = User::create([
                    'name'=>$this->name,
                    'email' => $this->email,
                    'ci' => $this->ci,
                    'number_phone' => $this->number_phone,
                    'password'=> Hash::make($this->password),
                    'pin'=> $this->pin,
                    'image'=> null,
                    'user_type' => 1,
                    'type'=> 1,//externo
                    'device_identifier' => null,
                    'own_state' => $this->state,
                    'state' => $this->state,
                ]);

                $user->assignRole('subscriber');

                $is_discount = false;

                $usersSubscription = UsersSubscriptionM::create([
                    'business_name' => $this->business_name,
                    'nit' => $this->nit,
                    'reg_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'expiration_notice' => $expiration_notice,
                    'date_expiry' => $date_expiry,
                    'amount' => $this->amount,
                    'is_discount' => $is_discount,
                    'cost' => $this->totalA,
                    'latitude' => $this->latitud,
                    'longitude' => $this->longitud,
                    'invitation_reference_data' => null,
                    'data_save' => $subscript,
                    'state' => $this->state,
                    'user_id' => $user->id,
                    'subscription_id' => $subscript->id,
                    'station_id' => $this->stationId,
                ]);
                /*
                HistoricalUsersSubscriptionM::create([
                    'business_name' => $this->business_name,
                    'nit' => $this->nit,
                    'reg_date' => date('Y-m-d'),
                    'start_date' => date('Y-m-d'),
                    'accumulation_date' => date('Y-m-d'),
                    'expiration_notice' => $expiration_notice,
                    'date_expiry' => $date_expiry,
                    'amount' => $this->amount,
                    'is_discount' => $is_discount,
                    'cost' => $this->totalA,
                    'latitude' => $this->latitud,
                    'longitude' => $this->longitud,
                    'invitation_reference_data' => count($this->invitateUsers)>0?json_encode($this->invitateUsers):null,  
                    'data_save' => $subscript,
                    'state' => $this->state,
                    'user_id' => $user->id,
                    'subscription_id' => $subscript->id,
                    'station_id' => $this->stationId,
                    'user_subscription_id' => $usersSubscription->id,
                ]);
                */
                if($this->subscriptionTag=='corporate'){
                    for ($i=0; $i <count($this->elements); $i++) { 
                        $passwordAux=null;
                        $emailAux = filter_var($this->elements[$i]['mail'], FILTER_VALIDATE_EMAIL)?$this->elements[$i]['mail']:null;
                        $phoneAux = is_numeric($this->elements[$i]['phone'])?$this->elements[$i]['phone']:null;
                        
                        if($emailAux != null){
                            if(strlen($emailAux) > 0){
                                $passwordAux=$emailAux;
                            }else if($phoneAux != null){
                                if(strlen($phoneAux) > 0){
                                    $passwordAux=$phoneAux;
                                }
                            }            
                        }else{
                            if($phoneAux != null){
                                if(strlen($phoneAux) > 0){
                                    $passwordAux=$phoneAux;
                                }
                            } 
                        }
                        if($this->elements[$i]['password']!='' && ($emailAux != null || $phoneAux != null)){
                            $passwordAux = $this->elements[$i]['password'];
                        }                        

                        $subuser  = User::create([
                            'name' => $this->elements[$i]['name'],
                            'lastname' =>$this->elements[$i]['lastname'],
                            'email' => $emailAux,
                            'number_phone' => $phoneAux,
                            'password' => $passwordAux!=null?Hash::make($passwordAux):null,
                            'image' => 'user_perfil.jpg',                            
                            'device_identifier' => null,                            
                            'access_type' => 1,
                            'user_type' => 3,
                            'own_state' => $this->state,
                            'state' => $this->state,
                            'user_dependency_id' => $user->id,
                        ]);
                        $subuser->assignRole('subscriber');

                        UsersSubscriptionM::create([
                            'business_name' => $this->business_name,
                            'nit' => $this->nit,
                            'reg_date' => date('Y-m-d'),
                            'accumulation_date' => date('Y-m-d'),
                            'start_date' => date('Y-m-d'),
                            'expiration_notice' => $expiration_notice,
                            'date_expiry' => $date_expiry,
                            'amount' => $this->amount,
                            'is_discount' => $is_discount,
                            'cost' => $this->totalA,
                            'latitude' => $this->latitud,
                            'longitude' => $this->longitud,
                            'invitation_reference_data' => null,
                            'data_save' => $subscript,
                            'state' => $this->state,
                            'user_id' => $subuser->id,
                            'subscription_id' => $subscript->id,
                            'station_id' => null,
                        ]);

                        /*dd($subuser);
                        if($subuser!=null){
                            DB::table('model_has_roles')->insert([
                                'role_id' => 8,
                                'model_type' => 'App\Models\User',
                                'model_id' => $subuser->id,
                            ]);
                        }*/                  
                    }
                }
                $this->viewList = true;
                $this->elements=[];
                $this->formAdd=false;
                $this->formUpdate=false;
                $this->formAddNewUser = false;
                $this->formRenewal = false;
                $this->formExtension = false;
                $this->subscriptionTag = -1;
                $this->business_name= null;
                $this->nit = null;
                $this->ci = null;
                $this->name= null;
                $this->number_phone = null;
                $this->email= null;
                $this->password= null;
                $this->state= 1;
                $this->latitud = '-17.784697';
                $this->longitud = '-63.182028';
                $this->failedDescription = null;
                $this->stationId = null;
                session()->flash('success', __('messages.subscriptions.session.registered'));
                //$this->dispatch('ocultar-alert');
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                session()->flash('error', ErrorMessage::forUser($th, __('messages.operation_errors.subscription_save')));
                ////$this->dispatch('ocultar-alert');
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]); 
        }
       
    }

    public function closeFormButton(){
        $this->subscriber_user_id = null;
        $this->formAdd = false;
        $this->formUpdate = false;
        $this->formAddNewUser = false;
        $this->formRenewal = false;
        $this->formExtension = false;
        $this->viewList=true;        
        $this->elements=[];
        $this->subscriptionTag = -1;
        $this->business_name = null;
        $this->nit = null;
        $this->ci = null;
        $this->name= null;
        $this->number_phone = null;
        $this->email= null;
        $this->password= null;
        $this->state= 1;
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
        
        $this->newUserName = null;
        $this->newUserEmail = null;
        $this->newUserPhone = null;
        $this->newUserPassword = null;
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
                'business_nameE.max' => __('messages.subscriptions.validation.business_name_max'),
                'nitE.max' => __('messages.subscriptions.validation.nit_max'),

                'nameE.required' => __('messages.subscriptions.validation.name_required'),
                'nameE.min' => __('messages.subscriptions.validation.name_min'),
                'nameE.max' => __('messages.subscriptions.validation.name_max'),

                'lastnameE.required' => __('messages.subscriptions.validation.lastname_required'),
                'lastnameE.min' => __('messages.subscriptions.validation.lastname_min'),
                'lastnameE.max' => __('messages.subscriptions.validation.lastname_max'),      
                
                'ciE.max' => __('messages.subscriptions.validation.ci_max'),

                'number_phoneE.required' => __('messages.subscriptions.validation.number_phone_required'),
                'number_phoneE.min' => __('messages.subscriptions.validation.number_phone_min'),
                'number_phoneE.max' => __('messages.subscriptions.validation.number_phone_max'),

                'emailE.required' => __('messages.subscriptions.validation.email_required'),
                'emailE.min' => __('messages.subscriptions.validation.email_min'),
                'emailE.max' => __('messages.subscriptions.validation.email_max'),               
                
                'passwordE.max' => __('messages.subscriptions.validation.password_max'),
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
                DB::commit();
                $this->passwordE = null;
                $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.changes_saved') ]);
                
            } catch (\Throwable $th) {
                DB::rollBack();
                //dd($th);
                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.error_saving_changes') ]);
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
                
                'business_nameOwnerE.max' => __('messages.subscriptions.validation.business_name_max'),
                
                'nitOwnerE.max' => __('messages.subscriptions.validation.nit_max'),

                'nameOwnerE.required' => __('messages.subscriptions.validation.name_required'),
                'nameOwnerE.min' => __('messages.subscriptions.validation.name_min'),
                'nameOwnerE.max' => __('messages.subscriptions.validation.name_max'),

                'lastnameOwnerE.required' => __('messages.subscriptions.validation.lastname_required'),
                'lastnameOwnerE.min' => __('messages.subscriptions.validation.lastname_min'),
                'lastnameOwnerE.max' => __('messages.subscriptions.validation.lastname_max'),
                
                'ciOwnerE.required' => __('messages.subscriptions.validation.ci_required'),
                'ciOwnerE.min' => __('messages.subscriptions.validation.ci_min'),
                'ciOwnerE.max' => __('messages.subscriptions.validation.ci_max'),

                'number_phoneOwnerE.required' => __('messages.subscriptions.validation.number_phone_required'),
                'number_phoneOwnerE.min' => __('messages.subscriptions.validation.number_phone_min'),
                'number_phoneOwnerE.max' => __('messages.subscriptions.validation.number_phone_max'),                

                'emailOwnerE.required' => __('messages.subscriptions.validation.email_required'),
                'emailOwnerE.min' => __('messages.subscriptions.validation.email_min'),
                'emailOwnerE.max' => __('messages.subscriptions.validation.email_max'),                
                'passwordOwnerE.max' => __('messages.subscriptions.validation.password_max'),
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
                $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.changes_saved') ]);                
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.error_apply_changes') ]);  
            }
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);
           
        } 
    }

    public function saveRenewal(){
        $expiration_noticeObj = new DateTime($this->newDateEndR);
        $expiration_noticeObj->modify('-1 month');
        $expiration_notice_add = $expiration_noticeObj->format('Y-m-d');
        
        $expired_date = new DateTime($this->dataRenewal->date_expiry);
        $current_date = new DateTime(date('Y-m-d'));
        $amount = null;
        $accumulation_date = null;
        if($current_date >= $expired_date){
            $amount = $this->numberOfYearsR;
            $accumulation_date = $this->newDateBeginR;
        }else{
            $amount = $this->dataRenewal->amount + $this->numberOfYearsR;
            $accumulation_date = $this->dataRenewal->start_date;
        }

        if($this->flagDiscountByReferenceInvitateR || $this->discountByIncidence || $this->flagDiscountDuration){
            $is_discount = true;
        }

        DB::beginTransaction();
        try {
              
            HistoricalUsersSubscriptionM::create([
                'business_name' => $this->newUserName,
                'nit' => $this->dataRenewal->nit,
                'reg_date' => $this->dataRenewal->reg_date,
                'accumulation_date' => $this->dataRenewal->accumulation_date,
                'start_date' => $this->dataRenewal->start_date,
                'expiration_notice' =>$this->dataRenewal->expiration_notice,
                'date_expiry' => $this->dataRenewal->date_expiry,
                'amount' => $this->dataRenewal->amount,
                'is_discount' => $this->dataRenewal->is_discount,
                'cost' => $this->dataRenewal->cost,
                'latitude' => $this->dataRenewal->latitude,
                'longitude' => $this->dataRenewal->longitude,
                'temporary_extension_start' => $this->dataRenewal->temporary_extension_start,
                'temporary_extension_end' => $this->dataRenewal->temporary_extension_end,
                //'data_save' => SubscriptionM::where('id',$this->dataRenewal->subscription_id)->first(),
                //'incident_extension' => count($this->dataIncidence) > 0?json_encode($this->dataIncidence):null,
                'state' => 1,
                'user_id' => $this->dataRenewal->user_id,
                'subscription_id' => $this->dataRenewal->subscription_id,
                'user_subscription_id' => $this->idR,                
            ]);
            
            UsersSubscriptionM::where('id',$this->idR)
            ->update([
                'business_name' => $this->dataRenewal->business_name,
                'nit' => $this->dataRenewal->nit,
                'reg_date' => $this->dataRenewal->reg_date,
                'accumulation_date' => $accumulation_date,
                'start_date' => $this->newDateBeginR,                
                'expiration_notice' => $expiration_notice_add,
                'date_expiry' => $this->newDateEndR,
                'amount' => $amount,
                'is_discount' => $is_discount,
                'cost' => $this->totalR,
                'latitude' => $this->dataRenewal->latitude,
                'longitude' => $this->dataRenewal->longitude,
                'data_save' => SubscriptionM::where('id',$this->dataRenewal->subscription_id)->first(),                
                'state' => 1,
                'user_id' => $this->dataRenewal->user_id,
                'subscription_id' => $this->dataRenewal->subscription_id,
            ]);

            DB::commit();
            $this->dataRenewal = null;            
            $this->formAdd = false;
            $this->formUpdate = false;
            $this->formRenewal = false;
            $this->formExtension = false;
            $this->formAddNewUser = false;
            $this->viewList = true;
            session()->flash('success', __('messages.subscriptions.session.renewal_registered') );
            $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.renewal_registered') ]); 
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_failed') ]);
        }
    }

    public function saveExtension(){
        DB::beginTransaction();
        try {
            UsersSubscriptionM::where('id',$this->idExt)
            ->update([                
                'temporary_extension_start' => $this->newDateBeginExt,
                'temporary_extension_end' => $this->newDateEndExt,              
            ]);
           
            DB::commit();
            $this->temporaryExtensionExt = false;
            $this->dataExtension = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$this->idExt)->where('dependent_user_id',$this->idUserExt)->first();
                  
            if($this->dataExtension->subscription->temporary_extension_start!=null && $this->dataExtension->subscription->temporary_extension_end!=null){
                $this->temporaryExtensionExt = true;
                $start_date = new DateTime($this->dataExtension->subscription->temporary_extension_end);                        
                $start_date->modify('+1 days');            
                $this->newDateBeginExt = $start_date->format('Y-m-d');
                $this->newDateEndExt = $this->newDateBeginExt;
            }else{
                if($this->dataExtension->subscription->date_expiry!=null){
                    $start_date = new DateTime($this->dataExtension->subscription->date_expiry);
                    $start_date->modify('+1 days');
                }else{
                    $start_date = new DateTime();
                }                            
                $this->newDateBeginExt = $start_date->format('Y-m-d');
                $this->newDateEndExt = $this->newDateBeginExt;        
            }
            $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.extension_applied') ]); 
            $this->dataRenewal = null;            
            $this->formAdd = false;
            $this->formUpdate = false;
            $this->formRenewal = false;
            $this->formExtension = true;
            $this->formAddNewUser = false;
            $this->viewList = false;

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.extension_failed') ]);
        }
    }

    public function removeExtension(){
        DB::beginTransaction();
        try {
            UsersSubscriptionM::where('id',$this->idExt)
            ->update([                
                'temporary_extension_start' => null,
                'temporary_extension_end' => null,              
            ]);
           
            DB::commit();
            $this->dataExtension = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$this->idExt)->where('dependent_user_id',$this->idUserExt)->first();
            $this->temporaryExtensionExt = false;
                  
            if($this->dataExtension->subscription->temporary_extension_start!=null && $this->dataExtension->subscription->temporary_extension_end!=null){
                $this->temporaryExtensionExt = true;
                $start_date = new DateTime($this->dataExtension->subscription->temporary_extension_end);                        
                $start_date->modify('+1 days');            
                $this->newDateBeginExt = $start_date->format('Y-m-d');
                $this->newDateEndExt = $this->newDateBeginExt;
            }else{
                if($this->dataExtension->subscription->date_expiry!=null){
                    $start_date = new DateTime($this->dataExtension->subscription->date_expiry);
                    $start_date->modify('+1 days');
                }else{
                    $start_date = new DateTime();
                }  
                $this->newDateBeginExt = $start_date->format('Y-m-d');
                $this->newDateEndExt = $this->newDateBeginExt;        
            }
            $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.extension_removed') ]); 
            $this->dataRenewal = null;            
            $this->formAdd = false;
            $this->formUpdate = false;
            $this->formRenewal = false;
            $this->formExtension = true;
            $this->formAddNewUser = false;
            $this->viewList = false;

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.extension_remove_failed') ]);
        }
    }

    public function saveNewUser(){
        $this->validate(
            [
                'newUserName' => 'required|min:3|max:50',
                'newUserPhone'=> 'required_without:newUserEmail',
                'newUserPassword' => 'required|min:6|max:50',                
                'newUserEmail' => 'required_without:newUserPhone|max:150',
            ], 
            [
                'newUserName.required' => __('messages.subscriptions.validation.new_user_name_required'),
                'newUserName.min' => __('messages.subscriptions.validation.new_user_name_min'),
                'newUserName.max' => __('messages.subscriptions.validation.new_user_name_max'),               


                'newUserPhone.required_without' => __('messages.subscriptions.validation.new_user_phone_required'),
                /*'newUserPhone.min' => __('messages.subscriptions.validation.new_user_phone_min'),*/
                /*'newUserPhone.max' => __('messages.subscriptions.validation.new_user_phone_max'),*/
                /*'newUserPhone.numeric' => __('messages.subscriptions.validation.new_user_phone_numeric'),*/
                
                'newUserEmail.required_without' => __('messages.subscriptions.validation.new_user_email_required'),
                /*'newUserEmail.min' => __('messages.subscriptions.validation.new_user_email_min'),*/
                'newUserEmail.max' => __('messages.subscriptions.validation.new_user_email_max'),

                'newUserPassword.required' => __('messages.subscriptions.validation.new_user_password_required'),
                'newUserPassword.min' => __('messages.subscriptions.validation.new_user_password_min'),
                'newUserPassword.max' => __('messages.subscriptions.validation.new_user_password_max'),
            ],
        );
        DB::beginTransaction();
        try {
            User::create([
                'name' => $this->newUserName,
                'email' => $this->newUserEmail,
                'number_phone' => $this->newUserPhone,
                'password' => Hash::make($this->newUserPassword),
                'image' => null,
                'is_pin' => false,
                'pin' => null,
                'status_subscription' => 1,
                'device_identifier' => null,
                'data' => json_encode([
                    'subscription_id' => $this->dataNewUser->subscription_id,
                    'is_discount' => $this->is_discountNU,
                    'discount' => $this->dataNewUser->subscription->discount,
                    'discount_from' => $this->dataNewUser->subscription->discount_from,
                    'amount' => $this->dataNewUser->amount, 
                    'cost' => $this->dataNewUser->cost,
                    'final_cost' => $this->totalNU,
                ]),
                'state' => 1,
                'user_dependency_id' => $this->dataNewUser->user_id,
            ]);
            
            DB::commit();
            $this->is_discountNU=false;
            $this->totalNU = null;
            $this->newUserName=null;
            $this->newUserEmail=null;
            $this->newUserPhone=null;
            $this->newUserPassword=null;
            $this->dataNewUser=null;
            $this->formAdd=false;
            $this->formUpdate=false;
            $this->formRenewal=false;
            $this->formAddNewUser=false;
            $this->viewList=true;
            session()->flash('success', __('messages.subscriptions.session.user_registered') );
        } catch (\Throwable $th) {
            //$th->getMessage()
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.new_user_failed') ]);
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
            //dd('Hello!',User::where('id','!=',$this->user_idE)->where('email',$this->emailE)->first());
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

    public function visibleAdd(){
        $this->closedUpdate();
        $this->formAdd = true;
        $this->formUpdate = false;
        $this->viewList = false;
        $this->formRenewal = false;
        $this->formExtension = false;
        //$this->dispatch('initMap');
        $this->durationListAdd = [1,2,3,4,5,6,7,8,9,10];
        $this->durationitemAdd = 1;
        $this->user_typeOwnerE = null;        
    }

    public function refreshUpdateData(){
        if($this->idOwnerE===null){
            $this->visibleUpdate($this->idE,$this->user_idE);
        }else{
            $this->visibleUpdate($this->idOwnerE,$this->user_idOwnerE);
        }
        
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
            $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.unlink_applied') ]); 
            $this->dispatch('openMapE',[
                'type' => 'update',
                'latitudE' =>  $this->latitudE,
                'longitudE' => $this->longitudE,
            ]);  
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.unlink_failed') ]);
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
                $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.station_change_applied') ]); 
                
                $this->dispatch('openMapE',[
                    'type' => 'update',
                    'latitudE' =>  $this->latitudE,
                    'longitudE' => $this->longitudE,
                ]);  
            }else{
                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.station_not_found') ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.change_failed') ]);
        }
    }

    public function visibleUpdate($id,$userId){
        $this->user_typeOwnerE = null;
        $data = UserDependencyM::with(['user', 'subscription'])->where('user_subscription_id',$id)->where('dependent_user_id',$userId)->first();
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
        $this->formRenewal = false;
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
                $this->currentPinOwnerE = null;
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
                        
                        if($this->stationId!=null){
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
                    $this->currentPinOwnerE = $data->user!==null?$data->user->pin:null;
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
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.subscription_not_selected') ]);
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
            $this->formRenewal = false;
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
                    $this->stationId = $data->subscription->station_id;                
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
                'is_password'=> $amountSubs[$i]->password==null
                    ? __('messages.subscriptions.view.form.text.no_password')
                    : __('messages.subscriptions.view.form.text.has_password'),
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
                        $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.station_already_linked') ]);
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
                        $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.link_applied') ]);   
                    }         
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.station_not_found') ]);
                }
            }else{
                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.subscription_already_linked') ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.link_failed') ]);
        }

    }

    public function changeStationManager($data){     
        DB::beginTransaction();
        try {            
            if(UsersSubscriptionM::Where('users_subscriptions.id',$data['id'])->where('station_id',$data['stationId'])->exists()){
                $registedStation = StationM::where('name',$this->chooseStationListUpdateOwnerName)->select('id','latitude','longitude')->first();
                
                if($registedStation!==null){
                    if(UsersSubscriptionM::where('user_id',$this->idOwnerE)->where('station_id',$registedStation->id)->exists()){
                        $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.station_already_linked') ]);
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
                        $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.link_change_applied') ]);
                    }                
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.station_not_found') ]);
                }
            }else{
                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.station_missing_for_subscription') ]);
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
                $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.station_unlink_applied') ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.unlink_station_failed') ]);
        }
    }

    public function createUserManager(){
        if($this->user_typeOwnerE==1){
           
            $data = UsersSubscriptionM::where('user_id', $this->user_idOwnerE)->select('users_subscriptions.id')->get();
            //dd($data,$this->user_idOwnerE);
            DB::beginTransaction();
            try {
                for ($i=0; $i < count($data); $i++) { 
                    $user = User::create([
                        'name' => null,
                        'lastname' => null,
                        'email' => null,
                        'number_phone' => null,
                        'password' => null,
                        'image' => 'user_perfil.jpg',                            
                        'device_identifier' => null,                            
                        'access_type' => 1,
                        'payment_state' => 1,
                        'user_type' => 2,
                        'own_state' => 1,
                        'state' => 1,
                        'user_dependency_id' => $this->user_idOwnerE,
                    ]);
                    $user->assignRole('subscriber');
                    UserDependencyM::firstOrCreate([
                        'user_subscription_id' => $data[$i]->id,
                        'dependent_user_id' => $user->id,
                    ]);

                    for ($i2=0; $i2 < 10; $i2++) { 
                        $userDep = User::create([
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
                            'user_dependency_id' => $user->id,
                        ]);
                        $userDep->assignRole('subscriber');
                        UserDependencyM::firstOrCreate([
                            'user_subscription_id' => $data[$i]->id,
                            'dependent_user_id' => $userDep->id,
                        ]);
                    }
                }
                
                DB::commit(); 
                $this->getMyStations();
                $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.user_created') ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.create_manager_failed') ]);
            }
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
        //dd($this->chooseStationListRegisteredOwner,$ids);
        $this->chooseStationListUpdateOwner = StationM::Where('stations.state',1)
            ->select('stations.name')
            ->get()->toArray();
    }

    public function closedUpdate(){
        $this->subscriber_user_id = null;
        $this->formAdd=false;
        $this->formUpdate=false;
        $this->viewList = true;
        $this->formRenewal = false;
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
            User::where('user_dependency_id',$userId)->delete();
            HistoricalUsersSubscriptionM::where('user_subscription_id',$data['id'])->delete();
            UsersSubscriptionM::where('id',$data['id'])->delete();
            User::where('id',$userId)->delete();
            DB::commit();
            session()->flash('message', __('messages.subscriptions.session.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('failed', __('messages.subscriptions.session.delete_failed'));
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
            session()->flash('failed', __('messages.subscriptions.session.status_failed'));            
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
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.status_change_failed') ]);                   
        }
    }

    public function resetDevice($data){
        DB::beginTransaction();
        try {
            User::where('id',$data['userId'])->update(['device_identifier' => null]);
            DB::commit();
            $this->dispatch('successAlert',[ 'success' => __('messages.subscriptions.success.device_reset') ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.device_reset_failed') ]);
        }
    }

    private function resultData(){
        $data = [];
        if($this->formRenewal){
            if($this->temporaryExtension){
                //$temporary_extension_start = new DateTime($this->dataRenewal->temporary_extension_start);
                $temporary_extension_end = new DateTime($this->dataRenewal->temporary_extension_end);
                $newDateBeginR_aux = new DateTime( $this->newDateBeginR);
                $newDateEndR_aux = new DateTime($this->newDateEndR);
                if( $newDateBeginR_aux > $temporary_extension_end){
                    $date_aux = $newDateBeginR_aux;
                    $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');
                    $this->newDateEndR = $date_aux->format('Y-m-d');
                    $newDateEndR_aux = new DateTime($this->newDateEndR);
                    if($newDateEndR_aux >= $newDateBeginR_aux){
                        
                        /*$interval_aux = $newDateBeginR_aux->diff($newDateEndR_aux);
                        $days_aux = $interval_aux->days;
                        $this->totalR = $this->totalRWithExpiration + round(( ($this->dataRenewal->subscription->price * $days_aux / (365*( ($this->dataRenewal->subscription->price * $this->numberOfYearsR) /$this->dataRenewal->subscription->duration)))), 2);*/

                        $this->totalR = $this->totalRWithExpiration + ( ($this->dataRenewal->subscription->price * $this->numberOfYearsR) / $this->dataRenewal->subscription->duration);
                        
                        if($this->discountIncidenceCheckR){                            
                            if($this->daysDiscountByIncidence <= 365 && $this->daysDiscountByIncidence > 0){
                                $this->totalR =  $this->totalR - round(( ($this->dataRenewal->subscription->price * $this->daysDiscountByIncidence / (365*$this->dataRenewal->subscription->duration))), 2);
                            }else{
                                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.incidence_days_invalid') ]);
                            }
                        }
                        if($this->is_discountNumberStationsCheckR){
                            if($this->discountNumberStationsCheckR){
                                $this->totalR = $this->totalR - ( $this->totalR * $this->discountValueStationsR) / 100;
                            }
                        }

                        if($this->is_discountDurationCheckR){
                            if($this->discountDurationCheckR){
                                $this->totalR = $this->totalR - ( $this->totalR * $this->durationValueDiscountR) / 100;
                            }
                        }

                    }else{
                        $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_end_before_start') ]);
                    }
                    
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_start_after_extension') ]);
                }          
                	
            }else{

                $expired_date = new DateTime($this->expiredDateR);
                $newDateBeginR_aux = new DateTime( $this->newDateBeginR);
                
                if( $newDateBeginR_aux > $expired_date){
                    $date_aux = $newDateBeginR_aux;
                    $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');
                    $this->newDateEndR = $date_aux->format('Y-m-d');
                    $newDateEndR_aux = new DateTime($this->newDateEndR);
                    
                    if($newDateEndR_aux >= $newDateBeginR_aux){   
                                           
                        /*$interval_aux = $newDateBeginR_aux->diff($newDateEndR_aux);
                        $days_aux = $interval_aux->days;
                        $this->totalR = $this->totalRWithExpiration + round(( ($this->dataRenewal->subscription->price * $days_aux / (365*( ($this->dataRenewal->subscription->price * $this->numberOfYearsR) /$this->dataRenewal->subscription->duration)))), 2);*/

                        $this->totalR = $this->totalRWithExpiration + ( ($this->dataRenewal->subscription->price * $this->numberOfYearsR) / $this->dataRenewal->subscription->duration);
                        
                        if($this->discountIncidenceCheckR){                            
                            if($this->daysDiscountByIncidence <= 365 && $this->daysDiscountByIncidence > 0){
                                $this->totalR =  $this->totalR - round(( ($this->dataRenewal->subscription->price * $this->daysDiscountByIncidence / (365*$this->dataRenewal->subscription->duration))), 2);
                            }else{
                                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.incidence_days_invalid') ]);
                            }
                        }

                        if($this->is_discountNumberStationsCheckR){
                            if($this->discountNumberStationsCheckR){
                                $this->totalR =  round( $this->totalR - (( $this->totalR * $this->discountValueStationsR) / 100) , 2);
                            }
                        }

                        if($this->is_discountDurationCheckR){
                            if($this->discountDurationCheckR){
                                $this->totalR = round($this->totalR - (( $this->totalR * $this->durationValueDiscountR) / 100),2);
                            }
                        }
                    }else{
                        $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_end_before_start') ]);
                    }
                    
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_start_after_current') ]);
                }           
                
                
                /*if($new_date < $expired_date){
                    $this->validateDate($expired_date,$new_date);
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_start_invalid') ]);
                }else{
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.renewal_start_after_extension') ]);
                }*/
            }
            $this->totalR = round($this->totalR,2);

            //Prorroga
            /*$daysExtension = 0;
            if($this->temporaryExtension){
                $current_date = new DateTime(date('Y-m-d'));
                $temporary_extension_start = new DateTime($this->dataRenewal->temporary_extension_start);
                $temporary_extension_end = new DateTime($this->dataRenewal->temporary_extension_end);
                    $interval = $temporary_extension_start->diff($temporary_extension_end);
                    $daysExtension = $interval->days;
            }

            //Descuento por Incidencia            
            $daysIncidence = 0;
            $this->dataIncidence = [];
            if($this->discountByIncidence){                
                $newDateBeginR = new DateTime($this->newDateBeginR);
                $newDateEndR = new DateTime($this->newDateEndR);            
                $dateStartDiscountByIncidence = new DateTime($this->dateStartDiscountByIncidence);
                $dateEndDiscountByIncidence = new DateTime($this->dateEndDiscountByIncidence);
                if(($dateStartDiscountByIncidence < $newDateBeginR || $dateStartDiscountByIncidence > $newDateEndR) ){
                    $this->dateStartDiscountByIncidence = $this->newDateBeginR;
                    $this->dateEndDiscountByIncidence = $this->newDateBeginR;
                    $dateStartDiscountByIncidence = new DateTime($this->dateStartDiscountByIncidence);
                    $dateEndDiscountByIncidence = new DateTime($this->dateEndDiscountByIncidence);
                    $intervalo = $dateStartDiscountByIncidence->diff($dateEndDiscountByIncidence);
                    $days = 0;
                    if($intervalo->days==0){
                        $days = 1; 
                    }else{
                        $days = $intervalo->days;
                    }
                    $daysIncidence = $days;
                    
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.discount_start_invalid') ]);
                }else if(($dateEndDiscountByIncidence < $newDateBeginR || $dateEndDiscountByIncidence > $newDateEndR)){

                    $this->dateStartDiscountByIncidence = $this->newDateBeginR;
                    $this->dateEndDiscountByIncidence = $this->newDateBeginR;
                    $dateStartDiscountByIncidence = new DateTime($this->dateStartDiscountByIncidence);
                    $dateEndDiscountByIncidence = new DateTime($this->dateEndDiscountByIncidence);
                    $intervalo = $dateStartDiscountByIncidence->diff($dateEndDiscountByIncidence);
                    $days = 0;
                    if($intervalo->days==0){
                        $days = 1; 
                    }else{
                        $days = $intervalo->days;
                    }
                    $daysIncidence = $days;
                    $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.discount_end_invalid') ]);
                }else{
                    $dateStartDiscountByIncidence = new DateTime($this->dateStartDiscountByIncidence);
                    $dateEndDiscountByIncidence = new DateTime($this->dateEndDiscountByIncidence);
                    $intervalo = $dateStartDiscountByIncidence->diff($dateEndDiscountByIncidence);
                    $days = 0;
                    if($intervalo->days==0){
                        $days = 1; 
                    }else{
                        $days = $intervalo->days;
                    }
                    $daysIncidence = $days;                                  
                }          
                $this->dataIncidence = [
                    'date_start'=>$this->dateStartDiscountByIncidence,'date_end'=>$this->dateEndDiscountByIncidence,
                ];
            }

            if(($daysExtension + $daysIncidence) > 0){
                $this->adjustDiffDays(($daysExtension+$daysIncidence));
            }            
           
            $this->totalR = $this->dataRenewal->subscription->price;
            $this->durationDiscountR = $this->dataRenewal->subscription->duration_discount;
            $this->durationValueDiscountR = $this->dataRenewal->subscription->duration_value_discount;
            $this->discountValueStationsR = $this->dataRenewal->subscription->discount_value_stations;
            $this->durationR = $this->dataRenewal->subscription->duration;        

            //Descuento por numero de estacion
            $totalDiscountStation = 0;
            if($this->is_discountValueStationsR){
                $totalDiscountStation =  ($this->dataRenewal->subscription->price/$this->durationR) * $this->numberOfYearsR;
                $totalDiscountStation = $totalDiscountStation * ($this->discountValueStationsR / 100);   
                $totalDiscountStation = round($totalDiscountStation, 2);
            }

            //Descuento por cantidad de duracion (años)
            $totalDiscountDuration = 0;
            $this->flagDiscountDuration = false;
            if($this->durationValueDiscountR > 0 && $this->numberOfYearsR >= $this->durationDiscountR){
                $totalDiscountDuration =  ($this->dataRenewal->subscription->price/$this->durationR) * $this->numberOfYearsR;
                $totalDiscountDuration = $totalDiscountDuration * ($this->durationValueDiscountR / 100);           
                $totalDiscountDuration = round($totalDiscountDuration, 2);
                $this->flagDiscountDuration = true;
            }
            
            $this->totalR = ($this->totalR/$this->durationR) * $this->numberOfYearsR;
            $this->totalR = $this->totalR  - ($totalDiscountStation + $totalDiscountDuration);
            */
        }else if($this->formExtension){
                if(!$this->extensionAlertWarningExt){
                    $date_expiry = new DateTime($this->date_expiryExt);
                    $newDateBeginExt = new DateTime($this->newDateBeginExt);
                    $newDateEndExt = new DateTime($this->newDateEndExt);    
                    
                    if($newDateBeginExt > $date_expiry){
                        if($newDateBeginExt <= $newDateEndExt){
                            if($newDateEndExt > $date_expiry){
                                if($newDateEndExt >= $newDateBeginExt){
                                   
                                }else{
                                    $this->newDateEndExt = $this->newDateBeginExt;
                                    $this->dispatch('failedAlert', [ 'failed' => 'La fecha final de la prorroga, debe ser mayor e igual a la  fecha de inicio de la prorroga.<br><br>Se reajusto nuevamente la fecha.' ]);
                                }
                            }else{
                                $this->newDateEndExt = $this->newDateBeginExt;
                                $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.extension_end_before_expiration') ]);
                            }
                        }else{
                            
                            $this->newDateEndExt = $this->newDateBeginExt;
                            $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.extension_start_after_end') ]);
                        }
                    }else{
                        $date_expiry->modify('+1 days');            
                        $this->newDateBeginExt = $date_expiry->format('Y-m-d');
                        $this->newDateEndExt = $this->newDateBeginExt;
                        $this->dispatch('failedAlert',[ 'failed' => __('messages.subscriptions.alerts.extension_start_before_expiration') ]);
                    }
                }
        }else if($this->formAdd || $this->formUpdate){
            if($this->subscriptionTag=='corporate' || $this->subscriptionTagE=='corporate'){
                $searchStation = $this->searchStationForAdd;
                $this->dataStationAdd = StationM::Where(function ($query) use ($searchStation) {
                    if($searchStation!=null){
                        $query->where('stations.name','LIKE', '%'.$searchStation.'%');                        
                    }
                })
                ->orderBy('id','desc')
                ->select('id','name','location','latitude','longitude')
                ->get();
            }

            if($this->formAdd){
                try {
                    //$this->referenceUsers = User::where('type',1)->get();
                    if(is_null($this->subscriptionAdd)){
                        $this->totalA = 0;
                    }else{
                        if($this->subscriptionAdd->duration > 0){
                            $this->totalA = ($this->subscriptionAdd->price / $this->subscriptionAdd->duration) * $this->durationitemAdd;
                        }else{
                            $this->totalA = 0;
                        }

                        if($this->subscriptionTag=="demo personal" || $this->subscriptionTag=="demo professional"){
                            $this->validateDateFree();
                        }

                        $valueSpecialDiscount = 0;

                        //Aplicar descuento por duracion
                        $valueDurationDiscount=0;
                        if($this->discountByDuration){
                            if($this->subscriptionAdd->state_dd==1){
                                if($this->durationitemAdd >= $this->subscriptionAdd->duration_discount){
                                    if($this->subscriptionAdd->duration_value_discount > 0){
                                        $valueDurationDiscount = $this->totalA - (($this->totalA * $this->subscriptionAdd->duration_value_discount) / 100);
                                    }
                                }
                            }
                        }

                        //Aplicar descuento por cantidad de estacion
                        $valueStationDiscount=0;
                        if($this->discountByStation){
                            if($this->subscriber_user_id != null){
                                $numberOfStations =  UsersSubscriptionM::where('user_id',$this->subscriber_user_id)->select('station_id')->distinct()->count();
                                if($this->subscriptionAdd->state_de==1){
                                    if($numberOfStations >= $this->subscriptionAdd->number_stations_discount){
                                        if($this->subscriptionAdd->discount_value_stations > 0){
                                            $valueStationDiscount = $this->totalA - (($this->totalA * $this->subscriptionAdd->duration_value_discount) / 100);
                                        }
                                    }
                                }
                            }
                        }                  


                        $this->totalA = $this->totalA - ($valueDurationDiscount + $valueSpecialDiscount + $valueStationDiscount);
                        $this->totalA = round($this->totalA,2);
                        
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    //dd($th);
                }
               
            }
            if($this->formUpdate){
                
            }
        }else{
            $rangeDate=[$this->beginDateExpiration,$this->endDateExpiration];
            $searchUser = $this->search;
            $data = UserDependencyM::with(['user', 'subscription'])
            ->whereHas('user', function ($query) use ($searchUser) {
                if ($searchUser != null) {
                    //$query->where(DB::raw("CONCAT(name, ' ', lastname)"), 'LIKE', '%' . $searchUser . '%');
                    $query->where(DB::raw("CONCAT(COALESCE(name, ''), ' ', COALESCE(lastname, ''))"), 'LIKE', '%' . $searchUser . '%');

                }
            })
            ->whereHas('subscription', function ($query2) use ($rangeDate) {
                if($rangeDate[0]!=null && $rangeDate[1]!=null){
                    $query2->whereBetween('date_expiry', [$rangeDate[0], $rangeDate[1]]);
                }else if($rangeDate[0]!=null){
                    $query2->where('date_expiry', $rangeDate[0]);
                }else if($rangeDate[1]!=null){
                    $query2->where('date_expiry', $rangeDate[1]);
                }
            })
            ->orderBy($this->sortColumn,$this->sortDirection)
            ->limit($this->perPage)->get();
            //->paginate($this->perPage);
        }
            
        return $data;
    }

    private function validateDate($expired_date,$new_date){
        if ($expired_date > $new_date) {
            $date_aux = $expired_date;
            $date_aux->modify('+1 days');
            $this->newDateBeginR = $date_aux->format('Y-m-d');
            $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');
            $date_aux->modify('-1 days');
            $this->newDateEndR = $date_aux->format('Y-m-d');         
        }else if($new_date > $expired_date){
            $newDateBeginR = new DateTime($this->newDateBeginR); 
            $newDateBeginR->modify('+'.(string)$this->numberOfYearsR.' year');
            $newDateBeginR->modify('-1 days');            
            $this->newDateEndR = $newDateBeginR->format('Y-m-d');
        }else{
            if($new_date == $expired_date){
                $date_aux = $new_date;
                $date_aux->modify('+1 days');
                $this->newDateBeginR = $date_aux->format('Y-m-d');          
                $date_aux->modify('+'.(string)$this->numberOfYearsR.' year');
                $date_aux->modify('-1 days');            
                $this->newDateEndR = $date_aux->format('Y-m-d');
            }
        }
    }
    
    public function render(){
        return view('livewire.usersSubscriptions.users-subscriptions',['data' => $this->resultData()]);
    }
}

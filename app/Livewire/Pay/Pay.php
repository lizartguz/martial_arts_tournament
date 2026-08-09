<?php

namespace App\Livewire\Pay;


use DateTime;
use DateTimeZone;
use Google\Client;
use App\Models\User;
use Livewire\Component;
use App\Models\StationM;
use App\Models\ForecastM;
use Livewire\WithPagination;
use App\Models\NotificationM;
use App\Models\SubscriptionM;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\UserDependencyM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthValidationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\HistoricalUsersSubscriptionM;

class Pay extends Component{

    use WithFileUploads;
    use WithPagination;
    use AuthValidationTrait;
    
    public $headers;
    protected $paginationTheme = 'tailwind';
   
    public $searchTitle = null;
    public $externalUserId = null;
    public $identifierPay = null;
   
    public $user_type = null;
    public $subscription_type = null;
    public $total_to_pay = null;
    public string $selectedOption = '';
    public $currencyOfExchange = 6.96;//1.00;
   
    public $listItemSubscript = [];
    public $selectAmount = [];
    public $selectDateAndCash = [];
    public $selectionType = null;
    public $userId = null;

    // pay type -------
    public $payType = null;
    public $listPayType = ['QR/Tarjeta','Efectivo','Cortesía'];
    public $namefullUser= null;
    //form -----
    public $name = null;
    public $lastname = null;
    public $email = null;
    public $number_phone = null;
    public $password = null;
    public $stationList = [];
    public $selectedStationId = null;
    public $valueDuration = null;
    public $dateNewDateStation = null;
    public $failedDescription = null;

    // new subscription -----
    public $newStationSuspcriptId = null;
    public $listIds = [];

    //form personal -----
    public $business_name = null;
    public $nit = null;
    public $expiration_date = null;
    public $extension = null;
    public $dataPersonal = null;
    public $renewalSuspcriptId = null;
    public $isItPaidRenewal = null;
    public $linkPayPersonal = null;

    //PASARELA
    public $qrUrl = null;
    public $linkDePago = null;
    public $enablePayment = false;

    //DESCUENTOS---------
    public $quantityAnddurationDiscount = null;
    public $validateTag = null;


    protected $listdbeners = [''];

    protected function rules(){
        return [
            'name' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|max:100|unique:users,email',
            'number_phone' => 'required|numeric|digits_between:6,15',
            'password' => 'required|string|min:6|max:100',            
        ];
    }

    protected function rulesPersonal(){
        return [
            'business_name' => 'required|string|max:40',
            'nit' => 'required|string|max:40',                        
        ];
    }

    protected function messagesPersonal()
    {
        return [
            'business_name.required' => 'La Razón social obligatorio.',
            'business_name.string' => 'La Razón social debe ser una cadena de texto.',
            'business_name.max' => 'La Razón social no debe tener más de 40 caracteres.',
        
            'lastname.required' => 'El nit es obligatorio.',
            'lastname.string' => 'El nit debe ser una cadena de texto.',
            'lastname.max' => 'El nit no debe tener más de 40 caracteres.',  
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.string' => 'El nombre debe ser una cadena de texto.',
        'name.max' => 'El nombre no debe tener más de 40 caracteres.',
    
        'lastname.required' => 'El apellido es obligatorio.',
        'lastname.string' => 'El apellido debe ser una cadena de texto.',
        'lastname.max' => 'El apellido no debe tener más de 40 caracteres.',
    
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico no es válido.',
        'email.max' => 'El correo electrónico no debe superar los 100 caracteres.',
        'email.unique' => 'Este correo ya está registrado.',
    
        'number_phone.required' => 'El número de teléfono es obligatorio.',
        'number_phone.numeric' => 'El número de teléfono debe contener solo números.',
        'number_phone.digits_between' => 'El número de teléfono debe tener entre 6 y 15 dígitos.',
    
        'password.required' => 'La contraseña es obligatoria.',
        'password.string' => 'La contraseña debe ser una cadena de texto.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.max' => 'La contraseña no debe superar los 100 caracteres.',    
       
    ];

    /*public function boot(): void{
        Livewire::component('payandrenew.payandrenew', PayAndRenew::class);
    }*/

    public function mount($transaction_id = null,$payment_method=null,$message = null,$userId = null,$forPay=null){             
        if($transaction_id != null){            
            $data = $this->textEncryption($transaction_id,'decode');
            $array = explode(',', $data);            
            $typePayment = null;
            $typeRenewal = null; 
            if($array[count($array) - 1]=="renewal" || $array[count($array) - 1]=="user"){
                $typeRenewal = $array[count($array) - 1];
                unset($array[count($array) - 1]);
                $array = array_values($array);
            }else{
                $typeRenewal = $array[count($array) - 2];
                $this->userId = intval($array[count($array) - 1]);
                $this->externalUserId = intval($array[count($array) - 1]);                
                unset($array[count($array) - 1]);
                $array = array_values($array);
                unset($array[count($array) - 1]);
                $array = array_values($array);
            }
            if($payment_method!=null){
                if($payment_method=="ATC_QR"){
                    $typePayment = 3;
                }else if($payment_method=="ATC_QR"){
                    $typePayment = 2;
                }else{
                    $typePayment = 2;
                }
            }
            
            try {
                DB::beginTransaction();                
                UsersSubscriptionM::whereIn("id",$array)->where("transaction_".$typeRenewal."_id",$transaction_id)
                ->update([
                    "is_it_paid_".$typeRenewal => 1,
                    "payment_type_".$typeRenewal => $typePayment,
                ]);
                if($typeRenewal=="user"){                    
                    $data = UserDependencyM::join('users', 'users.id', '=', 'user_dependency.dependent_user_id')
                    ->whereIn('user_dependency.user_subscription_id', $array)
                    ->pluck('users.id')
                    ->toArray();
                    User::whereIn('id',$data)
                    ->update([
                        'payment_state' => 1,                            
                    ]);
                }
                DB::commit();
            } catch (\Throwable $e) {
                    DB::rollBack();
                    //dd($e);                                                            
            }
            if($this->externalUserId!=null && $transaction_id != null){
                $route = env('PAYMENT_ROUTE_CALLBACK');
                redirect()->to($route."?userId={$this->externalUserId}&forPay=1");
            }else if($this->externalUserId==null && $transaction_id != null){
                $route = env('PAYMENT_ROUTE_CALLBACK');
                redirect()->to($route);
            }
        }
        
        $user = null;
        if($userId != null){
            $this->userId = $userId;
            $this->externalUserId = $userId;
            $user = User::find($this->userId);
        }else{
            $this->userId = Auth::user()->id;
            $user = Auth::user();
        }
        if($user){
            $this->namefullUser=$user->name.' '.$user->lastname;
        }

        $this->validarUsuario($user, [], $forPay);
        abort_unless($user?->can('pay_renewals.view'), 403);
        $this->user_type =$user->user_type;
        if($this->user_type==1){
            $susbs = UsersSubscriptionM::where('user_id',$user->id)->first();
            $this->subscription_type = $susbs->subscription->tag;
            
            if($this->user_type==1){
                if($this->subscription_type=='corporate'){
                    $this->selectionType ='renewal_station';
                    $this->selectedOption ='renewal_station';
                    $this->getData();
                }else if($this->subscription_type=='professional'){
                    $this->selectionType ='renewal_professional';
                    $this->selectedOption ='renewal_station';
                    $this->getData();
                }else if($this->subscription_type=='demo professional'){
                    $this->selectionType ='renewal_professional';
                    $this->selectedOption ='renewal_station'; 
                    $this->getData();
                }else if($this->subscription_type=='personal'){
                    $this->selectionType ='renewal_personal'; 
                    $this->selectedOption ='renewal_personal';
                    $this->getData();
                }else if( $this->subscription_type=='demo personal'){
                    $this->selectionType ='renewal_personal'; 
                    $this->selectedOption ='renewal_personal';
                    $this->getData($this->selectedOption);
                } 
            }else{
                return redirect()->route('dashboard')->send();
            }
        }
    }

    public function addPayNewStation(){
        $this->validate();
        $flag = $this->permitted();
        if($flag){
           $this->enablePayment = true;
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);
        }
    }

    public function fromBack(){
        $this->enablePayment = false;
        $this->payType = -1;
    }

    private function permitted(){
        $break = true;
        if(User::where('email',$this->email)->count() > 0){
            $this->failedDescription='El correo: '.$this->email.' no puede ser registrado porque ya existe, y esta asignado a otro usuario';
            $break=false;
        }else if(User::where('number_phone', $this->number_phone)->count() > 0){
            $this->failedDescription = 'El número de teléfono: '.$this->number_phone.' no puede ser registrado porque ya está asignado a otro usuario';
            $break = false;
        }else if (!empty($this->password) && strlen($this->password) < 6) {
            $this->failedDescription = 'La nueva contraseña debe tener por lo menos 6 o más caracteres';
            $break = false;
        }else if ($this->valueDuration==null || $this->valueDuration==-1 || !in_array($this->valueDuration, ['1', '2', '3', '4', '5'])) {
            $this->failedDescription = 'Debe seleccionar un tiempo de vigencia valido';
            $break = false;
        }
        return $break;
    }

    public function addPayNewPersonal(){        
        $this->validate($this->rulesPersonal(), $this->messagesPersonal());
        $flag = $this->permittedPersonal();       
        if($flag){            
            $this->enablePayment = true;
        }else{
            $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);  
        } 
    }

    private function permittedPersonal(){
        $break = true;
        if ($this->valueDuration==null || $this->valueDuration==-1 || !in_array($this->valueDuration, ['1', '2', '3', '4', '5'])) {
            $this->failedDescription = 'Debe seleccionar un tiempo de vigencia valido';
            $break = false;
        }
        return $break;
    }

    public function updatedSelectedOption($value){        
        $this->getData();
    }

    public function getData(){

        $this->validateTag =null;
        $this->total_to_pay = 0.00;
        $this->dateNewDateStation = null;
        if($this->subscription_type=="demo professional"){
            $this->validateTag='professional';
        }else if($this->subscription_type=="demo personal"){
            $this->validateTag='personal';
        }else{
            $this->validateTag=$this->subscription_type;
        }

        $this->quantityAnddurationDiscount = SubscriptionM::where('tag',$this->validateTag)->where('state',1)->first()->toArray();

       
        if($this->selectedOption=='renewal_station' || $this->selectedOption=='new_user'){
            $this->listItemSubscript = UsersSubscriptionM::join('users', 'users.id', '=', 'users_subscriptions.user_id')
            ->leftJoin('stations', 'stations.id', '=', 'users_subscriptions.station_id')
            ->where('users_subscriptions.user_id', $this->userId)
            ->select(
                'users.id as userId','users_subscriptions.id','users_subscriptions.date_expiry','users_subscriptions.temporary_extension_end','users_subscriptions.is_it_paid_renewal','users_subscriptions.is_it_paid_user','users_subscriptions.payment_link_renewal','users_subscriptions.payment_link_user',
                DB::raw("COALESCE(CONCAT(users.name, ' ', users.lastname), 'Sin Nombre') AS full_name"),
                DB::raw("COALESCE(stations.id, 0) AS station_id"),
                DB::raw("COALESCE(stations.name, '') AS station_name")
            )
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'station_name' => $item->station_name,
                    'date_expiry' => $item->date_expiry,
                    'temporary_extension_end' => $item->temporary_extension_end,
                    'is_it_paid_renewal' => $item->is_it_paid_renewal,
                    'is_it_paid_user' => $item->is_it_paid_user,
                    'payment_link_renewal' => $item->payment_link_renewal,
                    'payment_link_user' => $item->payment_link_user,
                ];
            })->toArray();
            //dd($this->listItemSubscript);           
            foreach ($this->listItemSubscript as $value) {                
                $this->selectAmount[$value['id']] = null;
                $this->selectDateAndCash[$value['id']] = [null, null, null]; 
            }
            $this->total_to_pay = null;

            
        }else if($this->selectedOption=='new_station'){
            $this->stationList = StationM::select('id', 'name')
            ->where('state', true)            
            ->orderBy('name', 'asc')
            ->distinct()
            ->get()
            ->toArray();
        }else if ($this->selectedOption=='renewal_personal'){
            
            $this->dataPersonal = UsersSubscriptionM::where('user_id', $this->userId)
            ->first();
            //dd($this->dataPersonal);
            $this->business_name = $this->dataPersonal->business_name;
            $this->nit = $this->dataPersonal->nit;
            $this->expiration_date = $this->dataPersonal->date_expiry;
            $this->extension = $this->dataPersonal->temporary_extension_end;
            $this->isItPaidRenewal = $this->dataPersonal->is_it_paid_renewal;
            $this->linkPayPersonal = $this->dataPersonal->payment_link_renewal;
        }
        $this->enablePayment = false;
    }
     
    public function applyQuantity($id = null,$pos = null){
        date_default_timezone_set('America/La_Paz');
        if($this->selectedOption=="renewal_station"){
            if($this->selectAmount[$id]==-1){   
                $this->selectDateAndCash[$id] = [null, null, null];
            }else{
                $tag='';
                if($this->subscription_type=="demo professional"){
                    $tag='professional';
                }else{
                    $tag=$this->subscription_type;
                }

                $date_expiry = Carbon::parse($this->listItemSubscript[$pos]['date_expiry']);
                $temporary_extension_end = $this->listItemSubscript[$pos]['temporary_extension_end'];
                $amount = floatval($this->selectAmount[$id]);

                if (is_null($temporary_extension_end)) {   
                    $now = now();
                    if ($date_expiry->isSameDay($now)) {
                        $result = $now->copy()->addYears($amount)->addDay();
                    } elseif ($now->greaterThan($date_expiry)) {
                        $result = $now->copy()->addYears($amount);
                    } else {
                        $result =$date_expiry->copy()->addYears($amount);
                    }                   
                    $data = $this->applyDiscount($amount);
                    $price = $data[0];
                    $is_discount = $data[1];
                    if(is_null($price)){
                        $this->selectAmount[$id]=-1;
                        $this->selectDateAndCash[$id] = [null, null, null];
                    }else{
                        $this->selectDateAndCash[$id] = [$result->toDateString(), $price, $is_discount]; 
                    }               
                } else {
                    $end = Carbon::parse($temporary_extension_end);
                    if ($date_expiry->eq($end)) {        
                        $roundedYears = $amount;       
                        $result = $date_expiry->copy()->addYears($roundedYears);
                    }else{    

                        
                        $diffYears = $date_expiry->diffInDays($end) / 365;
                        $aux = ceil($diffYears);
                        if($amount>$aux){
                            $roundedYears = $amount;
                        }else{
                            $roundedYears = $aux;
                        }
                        $this->selectAmount[$id]=$roundedYears;                    
                        $result = $date_expiry->copy()->addYears($roundedYears); 
                    }
                    
                    $data = $this->applyDiscount($roundedYears);
                    $price = $data[0];
                    $is_discount = $data[1];
                    if(is_null($price)){
                        $this->selectAmount[$id]=-1;
                        $this->selectDateAndCash[$id] = [null, null, null];
                    }else{
                        $this->selectDateAndCash[$id] = [$result->toDateString(), $price, $is_discount]; 
                    }
                }

                $this->total_to_pay = null;
                $this->listIds=[];
                foreach ($this->selectDateAndCash as $key => $item) {
                    if (!is_null($item[1])) {
                        $this->total_to_pay += $item[1];
                        $this->listIds[] = $key;
                    }
                }
            }

            $this->enablePayment = false;
            foreach($this->selectAmount as $value){               
                if($value!==null && $value!=-1){
                   $this->enablePayment = true;
                   break;
                }
            }
            if($this->enablePayment == false){
                $this->total_to_pay=0.00;
            }
        }else if($this->selectedOption=="new_user"){
            if($this->selectAmount[$id]==-1 || $this->selectAmount[$id]==null){   
                $this->selectDateAndCash[$id] = [null, null, null];                
            }else{
                $amount = floatval($this->selectAmount[$id]);            
                $data = $this->applyDiscount($amount);
                $price = $data[0];
                $is_discount = $data[1];
                if(is_null($price)){
                    $this->selectAmount[$id]=-1;
                    $this->selectDateAndCash[$id] = [null, null, null];
                }else{
                    $this->selectDateAndCash[$id] = [null, $price,null];
                }
            }

            $this->total_to_pay = 0.00;
            foreach ($this->selectDateAndCash as $item) {
                if (!is_null($item[1])) {
                    $this->total_to_pay += $item[1];
                }
            } 
            $this->enablePayment = false;
            foreach($this->selectAmount as $value){
                if($value!=null && $value!=-1){
                   $this->enablePayment = true;
                   break;
                }
            }
        }else if($this->selectedOption=="new_station"){
            if($this->valueDuration!=null && $this->valueDuration!=-1 && in_array($this->valueDuration, ['1', '2', '3', '4', '5'])){
                
                $amount = floatval($this->valueDuration);               
                $fechaActual = Carbon::now();
                $nuevaFecha = $fechaActual->copy()->addYears($amount);
                $this->dateNewDateStation = $nuevaFecha->toDateString();

                $data = $this->applyDiscount($amount);
                $price = $data[0];
                $is_discount = $data[1];
                if(is_null($price)){
                    $this->valueDuration=-1;
                    $this->total_to_pay = null;
                }else{
                    $this->total_to_pay = $price; 
                }            
            }else{
                $this->enablePayment = false;
                $this->dateNewDateStation = null;
                $this->total_to_pay = null;
                $this->valueDuration = -1;
            }                
        }else if($this->selectedOption=="renewal_personal"){
            if($this->valueDuration!=null && $this->valueDuration!=-1 && in_array($this->valueDuration, ['1', '2', '3', '4', '5'])){
                $amount = floatval($this->valueDuration);               
                //$fechaActual = Carbon::now();
                //$nuevaFecha = $fechaActual->copy()->addYears($amount);
                //$this->dateNewDateStation = $nuevaFecha->toDateString();
                $userSubs = UsersSubscriptionM::where('user_id',$this->userId)->select('date_expiry','temporary_extension_end')->first();
                
                $date_expiry = Carbon::parse($userSubs->date_expiry);
                $temporary_extension_end = $userSubs->temporary_extension_end;     

                if (is_null($temporary_extension_end)) {                    
                    //$result = $date_expiry->copy()->addYears($amount);
                    $now = now();
                    if ($date_expiry->isSameDay($now)) {
                        $result = $now->copy()->addYears($amount)->addDay();
                    } elseif ($now->greaterThan($date_expiry)) {
                        $result = $now->copy()->addYears($amount);
                    } else {
                        $result =$date_expiry->copy()->addYears($amount);
                    }                    
                    $this->dateNewDateStation = $result->toDateString(); 

                } else {

                    $end = Carbon::parse($temporary_extension_end);
                    if ($date_expiry->eq($end)) {        
                        $roundedYears = $amount;       
                        $result = $date_expiry->copy()->addYears($roundedYears);
                    }else{    
                        $diffYears = $date_expiry->diffInDays($end) / 365;
                        $aux = ceil($diffYears);
                        if($amount>$aux){
                            $roundedYears = $amount;
                        }else{
                            $roundedYears = $aux;
                        }
                        $this->valueDuration=$roundedYears; 
                        $amount = floatval($this->valueDuration);                   
                        $result = $date_expiry->copy()->addYears($roundedYears);
                        $this->dateNewDateStation = $result->toDateString(); 
                    }         
                }
                
                $data = $this->applyDiscount($amount);
                $price = $data[0];
                $is_discount = $data[1];
                if(is_null($price)){
                    $this->valueDuration=-1;
                    $this->total_to_pay = null;
                }else{
                    $this->total_to_pay = $price; 
                }                
            }else{
                $this->enablePayment = false;
                $this->dateNewDateStation = null;
                $this->total_to_pay = null;
                $this->valueDuration = -1;
            }                
        }
    }

    private function applyDiscount($amount){
        $precio = null;
        $discount = null;
        if($this->selectedOption=="renewal_station"){
            if($this->quantityAnddurationDiscount!=null){
                if($this->quantityAnddurationDiscount['state']==1){
                    if($this->quantityAnddurationDiscount['state_dd']==1){
                        if($amount >= floatval($this->quantityAnddurationDiscount['duration_discount'])){
                            try{
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $descuento = ($precio * floatval($this->quantityAnddurationDiscount['duration_value_discount'])) / 100;
                                $precio = $precio - $descuento;
                                $discount = 'yes';
                            } catch (\Throwable $e) {}
                        }else{
                            try{
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $discount = 'no';
                            } catch (\Throwable $e) {}                       
                        }
                    }else{
                        try{
                            $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                            $discount = 'no';
                        } catch (\Throwable $e) {}    
                    }
                }
            }
        }else if($this->selectedOption=="new_user"){
            if($this->quantityAnddurationDiscount['state']==1){                
                try{
                    $quantityAnddurationDiscountExeption = SubscriptionM::where('tag','personal')->where('state',1)->first()->toArray();
                    $precio = (($this->currencyOfExchange * floatval($quantityAnddurationDiscountExeption['price'])) * $amount);
                } catch (\Throwable $e) {}    
                
            }
        }else if($this->selectedOption=="new_station"){
            if($this->quantityAnddurationDiscount!=null){
                if($this->quantityAnddurationDiscount['state']==1){
                    
                    if($this->quantityAnddurationDiscount['state_dd']==1){
                        if($amount >= floatval($this->quantityAnddurationDiscount['duration_discount'])){
                            try{
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $descuento = ($precio * floatval($this->quantityAnddurationDiscount['duration_value_discount'])) / 100;
                                $precio = $precio - $descuento;
                                $discount = 'yes';
                            } catch (\Throwable $e) {}
                        }else{
                            try{
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $discount = 'no';
                            } catch (\Throwable $e) {}                       
                        }
                    }else{
                        try{
                            $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                            $discount = 'no';
                        } catch (\Throwable $e) {}    
                    }

                    if($this->quantityAnddurationDiscount['state_de']==1){
                        if(count($this->selectDateAndCash) >= intval($this->quantityAnddurationDiscount['number_stations_discount'])){
                            try{
                                if($precio!=null){                                   
                                    $descuento = ($precio * floatval($this->quantityAnddurationDiscount['discount_value_stations'])) / 100;
                                    $precio = $precio - $descuento;
                                    $discount = 'yes';
                                }else{
                                    $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                    $descuento = ($precio * floatval($this->quantityAnddurationDiscount['discount_value_stations'])) / 100;
                                    $precio = $precio - $descuento;
                                    $discount = 'yes';
                                }
                                
                            } catch (\Throwable $e) {}
                        }else{
                            //si no plico ninguno de lso dos descuentos
                            if($precio==null){
                                try{
                                    $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                    $discount = 'no';
                                } catch (\Throwable $e) {}
                            }                   
                        }
                    }else{
                        try{
                            //si no plico ninguno de lso dos descuentos
                            if($precio==null){
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $discount = 'no';
                            }
                            
                        } catch (\Throwable $e) {}    
                    }
                }
            }
        }else if($this->selectedOption=="renewal_personal"){
            if($this->quantityAnddurationDiscount!=null){
                if($this->quantityAnddurationDiscount['state']==1){
                    if($this->quantityAnddurationDiscount['state_dd']==1){
                        if($amount >= floatval($this->quantityAnddurationDiscount['duration_discount'])){
                            try{
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $descuento = ($precio * floatval($this->quantityAnddurationDiscount['duration_value_discount'])) / 100;
                                $precio = $precio - $descuento;
                                $discount = 'yes';
                            } catch (\Throwable $e) {}
                        }else{
                            try{
                                $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                                $discount = 'no';
                            } catch (\Throwable $e) {}                       
                        }
                    }else{
                        try{
                            $precio = (($this->currencyOfExchange * floatval($this->quantityAnddurationDiscount['price'])) * $amount) / floatval($this->quantityAnddurationDiscount['duration']);
                            $discount = 'no';
                        } catch (\Throwable $e) {}    
                    }
                }
            }     
        }        
        return [$precio,$discount];      
    }

    private function textEncryption($text, $type){    
        $cypher = env('ENCRYPTION_KEY');
        $strategy = "AES-256-CBC";

        if($type == 'encode') {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($strategy));        
            $encryption = openssl_encrypt($text, $strategy, $cypher, 0, $iv);        
            
            $res = base64_encode(json_encode([
                'iv' => base64_encode($iv),
                'value' => $encryption
            ]));
        } else {
            $decoded = json_decode(base64_decode($text), true);
        
            if (!isset($decoded['iv'], $decoded['value'])) {
                return null; // Fallback si el formato es inválido
            }
        
            $iv = base64_decode($decoded['iv']);
            $code = $decoded['value'];
        
            $res = openssl_decrypt($code, $strategy, $cypher, 0, $iv);
        }
        return $res;
    }
    
    public function payWith(){
        $this->linkDePago = null;
        if (in_array($this->payType, ['QR/Tarjeta'])) {
            date_default_timezone_set('America/La_Paz');
            $fechaActual = new DateTime();
            $fechaActual->modify('+1 year');
            $fecha_vencimiento = $fechaActual->format('Y-m-d');
            $identificador = '';
            $userOwner = User::find($this->userId);
            $userOwnerSubs = UsersSubscriptionM::where('user_id',$this->userId)->first();
            $metaData = [];
            $items = [];            
            $email_cliente = $userOwner->email;
            $descripcion = 'Pago Online Senvatec';
            $nombre_cliente = trim($userOwner->name);
            $apellido_cliente = trim($userOwner->lastname);

            
            if($this->selectedOption=="renewal_personal"){
                $razon_social = $this->business_name;
                $numero_documento = $this->nit;
            }else{
                $razon_social = $userOwnerSubs!=null?$userOwnerSubs->business_name:'Sin Nombre';
                $numero_documento = $userOwnerSubs!=null?$userOwnerSubs->nit:'';
            }


            if($this->selectedOption=="renewal_station"){
                $price = SubscriptionM::where('tag', 'corporate')->value('price');
                for ($i = 0; $i < count($this->listItemSubscript); $i++){
                    if($this->selectDateAndCash[$this->listItemSubscript[$i]['id']][1]!==null){
                        
                        $identificador = $identificador.','.$this->listItemSubscript[$i]['id'];
                        $costo = floatval($this->selectDateAndCash[$this->listItemSubscript[$i]['id']][1]) /intval($this->selectAmount[$this->listItemSubscript[$i]['id']]);

                        $metaData[] = [
                            'nombre' => (strpos($this->subscription_type, 'professional'))?'Renovación profesional':'Renovación corporativa',
                            'dato' => "Cant. de años: ".(string)$this->selectAmount[$this->listItemSubscript[$i]['id']].", Fecha expiración: ".(string)$this->selectDateAndCash[$this->listItemSubscript[$i]['id']][0].", Costo: ".(string) $this->selectDateAndCash[$this->listItemSubscript[$i]['id']][1]. " Bs",
                        ];

                        $items[] =[
                            'concepto' => 'Renovación de Suscripción con codigo '.(string) $this->listItemSubscript[$i]['id'],
                            'cantidad' => $this->selectAmount[$this->listItemSubscript[$i]['id']],
                            'costo_unitario' => $costo,
                            'codigo_producto' => (string) $this->listItemSubscript[$i]['id'].'-RNVST',
                            'descuento_unitario' => 0,
                            'ignora_factura' => false,
                        ];
                    }
                }
                $identificador = trim($identificador, ',');
                $identificador = $identificador.',renewal';
                if($this->externalUserId!=null){
                    $identificador = $identificador.','.$this->externalUserId;

                }
                $identificador = $this->textEncryption($identificador,'encode');
                
            
            }else if($this->selectedOption=="new_user"){
                $price = SubscriptionM::where('tag', 'personal')->value('price');
                for ($i = 0; $i < count($this->listItemSubscript); $i++){
                    if($this->selectDateAndCash[$this->listItemSubscript[$i]['id']][1]!==null){
                        
                        $identificador = $identificador.','.$this->listItemSubscript[$i]['id'];
                        $costo = floatval($this->selectDateAndCash[$this->listItemSubscript[$i]['id']][1]) /intval($this->selectAmount[$this->listItemSubscript[$i]['id']]);

                        $metaData[] = [
                            'nombre' => 'Asignacion de usuarios a suscripción con codigo '.(string) $this->listItemSubscript[$i]['id'],
                            'dato' => "Cantidad de usuarios: ".(string)$this->selectAmount[$this->listItemSubscript[$i]['id']].", Costo: ".(string) $this->selectDateAndCash[$this->listItemSubscript[$i]['id']][1]. " Bs",
                        ];

                        $items[] =[
                            'concepto' => 'Renovación de Suscripción con codigo '.(string) $this->listItemSubscript[$i]['id'],
                            'cantidad' => $this->selectAmount[$this->listItemSubscript[$i]['id']],
                            'costo_unitario' => $costo,
                            'codigo_producto' => (string) $this->listItemSubscript[$i]['id'].'-RNVUS',
                            'descuento_unitario' => 0,
                            'ignora_factura' => false,
                        ];
                    }
                }
                $identificador = trim($identificador, ',');
                $identificador = $identificador.',user';
                if($this->externalUserId!=null){
                    $identificador = $identificador.','.$this->externalUserId;

                }
                $identificador = $this->textEncryption($identificador,'encode');

            }else if($this->selectedOption=="new_station"){
                $cantidad=1; //uno porque el costo total ya ha sido calculado con o sin los descuentos correspondiente 
                $metaData[] = [
                    'nombre' => 'Nueva suscripción creada con codigo '.(string)$this->newStationSuspcriptId,
                    'dato' => "Tiempo de vigencia: ".(string)$this->valueDuration.",  Expiración:".$this->dateNewDateStation.",  Costo: ".(string) $this->total_to_pay. " Bs",
                ];

                $items[] = [
                    'concepto' => 'Nueva Suscripción con codigo '.(string)$this->newStationSuspcriptId,
                    'cantidad' => $cantidad,
                    'costo_unitario' => floatval($this->total_to_pay),
                    'codigo_producto' => (string)$this->newStationSuspcriptId.'-ADDSUBS',
                    'descuento_unitario' => 0,
                    'ignora_factura' => false,
                ];
                
            }else if($this->selectedOption=="renewal_personal"){            
                //Preparar identificador_transactionId
                $identificador = $userOwnerSubs->id;
                $identificador = $identificador.',renewal';
                if($this->externalUserId!=null){
                    $identificador = $identificador.','.$this->externalUserId;
                }
                //dd($identificador);
                $identificador = $this->textEncryption($identificador,'encode');

                $costo = floatval($this->total_to_pay)/intval($this->valueDuration);                 
                $metaData[] = [
                    'nombre' => trim($nombre_cliente.' '.$apellido_cliente),
                    'dato' => "Tiempo de vigencia: ".(string)$this->valueDuration.",  Expiración:".$this->renewalSuspcriptId.",  Costo: ".(string) $this->total_to_pay. " Bs",
                ];

                $metaData[] = [
                    'nombre' => 'Nueva suscripción creada con codigo '.(string)$this->renewalSuspcriptId,
                    'dato' => "Tiempo de vigencia: ".(string)$this->valueDuration.",  Expiración:".$this->renewalSuspcriptId.",  Costo: ".(string) $this->total_to_pay. " Bs",
                ];

                $items[] =[
                    'concepto' => 'Renovación de Suscripción con codigo '.(string)'',
                    'cantidad' => intval($this->valueDuration),
                    'costo_unitario' =>$costo,
                    'codigo_producto' => (string)$this->newStationSuspcriptId.'-RNVUP',
                    'descuento_unitario' => 0,
                    'ignora_factura' => false,
                ];
            }
            
            $callback_url = '';
            if($this->externalUserId==null){
                $callback_url = env('APP_URL').env('PAYMENT_ROUTE_CALLBACK');
            }else{
                $callback_url = env('APP_URL').env('PAYMENT_ROUTE_CALLBACK').'?userId='.(string)$this->externalUserId.'&forPay=1';
            }
            
            if(in_array($this->selectedOption,["renewal_station","new_user","renewal_personal"])){
                $payload = [
                    'appkey' => env('LIBELULA_APP_KEY'),
                    'identificador' => $identificador,
                    'email_cliente' => $email_cliente,
                    'descripcion' => $descripcion,
                    'nombre_cliente' => $nombre_cliente,
                    'apellido_cliente' => $apellido_cliente,
                    'razon_social' => $razon_social,
                    'numero_documento' => $numero_documento,
                    'codigo_tipo_documento' => '1',
                    'emite_factura' => 0,
                    'callback_url' => $callback_url,
                    'codigo_documento_sector' => '1',
                    'fecha_vencimiento' => $fecha_vencimiento,
                    'lineas_detalle_deuda' => $items,
                    'lineas_metadatos' => $metaData,
                    'canal_caja' => '',
                    'canal_caja_sucursal' => '',
                    'canal_caja_usuario' => '',
                ];
    
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post(env('PAYMENT_ROUTE'), $payload);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $this->linkDePago = $data['url_pasarela_pagos'] ?? null;
                    //$this->qrUrl = $data['qr_simple_url'];
                    $this->registerDataHistorical($identificador ?? null);
                } else {
                    $this->payType = -1;
                                
                    $this->dispatch('failedAlert',[ 'failed' => 'Error al conectarse con el proceso de pago.' ]);
                }

            }else if (in_array($this->selectedOption,["new_station"])){

                $this->registerDataHistorical($identificador ?? null);

                $payload = [
                    'appkey' => env('LIBELULA_APP_KEY'),
                    'identificador' => $this->identifierPay,
                    'email_cliente' => $email_cliente,
                    'descripcion' => $descripcion,
                    'nombre_cliente' => $nombre_cliente,
                    'apellido_cliente' => $apellido_cliente,
                    'razon_social' => $razon_social,
                    'numero_documento' => $numero_documento,
                    'codigo_tipo_documento' => '1',
                    'emite_factura' => 0,
                    'callback_url' => $callback_url,
                    'codigo_documento_sector' => '1',
                    'fecha_vencimiento' => $fecha_vencimiento,
                    'lineas_detalle_deuda' => $items,
                    'lineas_metadatos' => $metaData,
                    'canal_caja' => '',
                    'canal_caja_sucursal' => '',
                    'canal_caja_usuario' => '',
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post(env('PAYMENT_ROUTE'), $payload);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $this->linkDePago = $data['url_pasarela_pagos'] ?? null;
                    UsersSubscriptionM::where('id',$this->newStationSuspcriptId)
                    ->update([ 
                        'payment_link_renewal' => $this->linkDePago,
                    ]);
                    //$this->qrUrl = $data['qr_simple_url'];
                } else {
                    $this->deleteRegisterGenerate();
                    $this->payType = -1;
                    $this->dispatch('failedAlert',[ 'failed' => 'Error al conectarse con el proceso de pago.' ]);
                }

            }

           

        }else{
            if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                if (Auth::user()->hasAnyRole(['super_manager', 'manager', 'meteorology','meteorologist'])) {
                    $this->registerDataHistorical($identificador ?? null);
                }else{
                    $this->payType = -1;
                }
            }else{
                $this->payType = -1;
            }
        }
    }

    private function deleteRegisterGenerate(){
        try {
            DB::beginTransaction();            
            if (in_array($this->selectedOption,["new_station"])){   
                $listUsers = UserDependencyM::where('user_subscription_id',$this->newStationSuspcriptId)->select('dependent_user_id')->get();                
                UserDependencyM::where('user_subscription_id',$this->newStationSuspcriptId)->delete();
                UsersSubscriptionM::where('id',$this->newStationSuspcriptId)->delete();            
                $usersToDelete = User::whereIn('id', $$listUsers)->get();
                foreach ($usersToDelete as $user) {
                    $user->syncRoles([]);
                    $user->syncPermissions([]);
                    $user->delete();
                }
            }
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('failedAlert',[ 'failed' => 'No se ha podido limpiar los registros generados' ]);
        }
    }

    private function registerDataHistorical($transactionId){
        $this->identifierPay = $transactionId;
        date_default_timezone_set('America/La_Paz');
        if($this->selectedOption=="renewal_station"){
            $fechaFinal = Carbon::parse($this->dateNewDateStation)->subMonth();
            $dateBeforeOneMonth = $fechaFinal->toDateString();
            $usersSubscriptions = UsersSubscriptionM::whereIn('id',$this->listIds)->get();
            try {
                DB::beginTransaction();
                foreach ($usersSubscriptions as $item) {   
                    HistoricalUsersSubscriptionM::create([
                        'business_name'=>$item->business_name,
                        'nit'=>$item->nit,               
                        'reg_date' => $item->reg_date,
                        'accumulation_date' => $item->accumulation_date,
                        'start_date' => $item->start_date,
                        'expiration_notice' => $item->expiration_notice,
                        'date_expiry' =>  $item->date_expiry,
                        'is_discount'  => $item->is_discount,
                        'added_user_count' => $item->added_user_count,
                        'added_user_total_cost' => $item->added_user_total_cost,
                        'amount'  => $item->amount,
                        'cost' => $item->cost,
                        'latitude' => $item->latitude,
                        'longitude' => $item->longitude,
                        'invitation_reference_data' => $item->invitation_reference_data,
                        'temporary_extension_start' => $item->temporary_extension_start,
                        'temporary_extension_end' => $item->temporary_extension_end,
                        'incident_extension' => $item->incident_extension,
                        'data_save' => $item->data_save,
                        'is_it_paid_user'=> $item->is_it_paid_user,
                        'payment_type_user'=> $item->payment_type_user,
                        'is_it_paid_renewal'=> $item->is_it_paid_renewal,
                        'payment_type_renewal'=> $item->payment_type_renewal,
                        'state' => $item->state,
                        'user_id' => $item->user_id,
                        'subscription_id' => $item->subscription_id,
                        'user_subscription_id' => $item->id,  
                        'transaction_user_id'=> $item->transaction_user_id,
                        'transaction_renewal_id'=> $item->transaction_renewal_id,
                    ]);   
                    
                    $fechaFinal = Carbon::parse($this->selectDateAndCash[$item->id][0])->subMonth();
                    $dateBeforeOneMonth = $fechaFinal->toDateString();

                    $is_it_paid_renewal = 0;
                    $payment_type_renewal= 0;
                    if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                        $is_it_paid_renewal = 1;                        
                        if (in_array($this->payType, ['Efectivo'])) {
                            $payment_type_renewal= 1;
                        }else{
                            $payment_type_renewal= 4;
                        }                        
                    }

                    $newSusbscriptionId = null;
                    if($item->subscription_id==1){ //demo personal
                        $newSusbscriptionId = 3; //personal
                    }else if($item->subscription_id==2){// demo profesional
                        $newSusbscriptionId = 4; //profesional
                    }else{
                        $newSusbscriptionId = $item->subscription_id;
                    }

                    UsersSubscriptionM::where('id',$item->id)
                    ->update([                    
                        'start_date' => Carbon::today()->format('Y-m-d'),
                        'expiration_notice' => $dateBeforeOneMonth,
                        'date_expiry' => $this->selectDateAndCash[$item->id][0],
                        'amount' => $this->selectAmount[$item->id],
                        'cost' => floatVal($this->selectDateAndCash[$item->id][1]),
                        'temporary_extension_start' => null,
                        'temporary_extension_end' => null,                       
                        'is_it_paid_renewal'=> $is_it_paid_renewal,
                        'payment_type_renewal'=> $payment_type_renewal,
                        'payment_link_renewal' => $this->linkDePago,
                        'transaction_renewal_id'=> $transactionId,
                        'subscription_id'=> $newSusbscriptionId,
                    ]);
                }
                DB::commit();
                $this->enablePayment = false;                
                $this->getData();
                if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                    $this->dispatch('successAlert',[ 'success' => 'Renovación exitosa.' ]);
                }
                $this->payType = -1;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->linkDePago=null;
                $this->dispatch('failedAlert',[ 'failed' => 'Hubo un problema al guardar los datos. Intenta nuevamente.' ]);                
            }
        }else if($this->selectedOption=="new_user"){
            $userOwner = User::find($this->userId);
            $fechaFinal = Carbon::parse($this->dateNewDateStation)->subMonth();
            $dateBeforeOneMonth = $fechaFinal->toDateString();
            $usersSubscriptions = UsersSubscriptionM::whereIn('id',$this->listIds)->get();
            try {
                DB::beginTransaction();
                foreach ($usersSubscriptions as $item) {
                    
                    $fechaFinal = Carbon::parse($this->selectDateAndCash[$item->id][0])->subMonth();
                    $dateBeforeOneMonth = $fechaFinal->toDateString();
                    
                    $userManagerId = UserDependencyM::join('users', 'users.id', '=', 'user_dependency.dependent_user_id')
                    ->where('user_dependency.user_subscription_id',$item->id)
                    ->where('users.user_type',2)
                    ->value('users.id');

                    $is_it_paid_user = 0;
                    $payment_type_user= 0;
                    $payment_state = 0; //for user
                    if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                        $payment_state = 1;
                        $is_it_paid_user = 1;                        
                        if (in_array($this->payType, ['Efectivo'])) {
                            $payment_type_user= 1;
                        }else{
                            $payment_type_user= 4;
                        }                        
                    }

                    UsersSubscriptionM::where('id',$item->id)
                    ->update([               
                        'added_user_count' => $item->added_user_count +  $this->selectAmount[$item->id],
                        'added_user_total_cost' => $item->added_user_total_cost + floatVal($this->selectDateAndCash[$item->id][1]),                                           
                        'is_it_paid_user'=> $is_it_paid_user,
                        'payment_type_user'=> $payment_type_user,
                        'payment_link_user' => $this->linkDePago,
                        'transaction_user_id'=> $transactionId,
                    ]);

                    for ($i=0; $i < intval($this->selectAmount[$item->id]); $i++) { 
                        $user = User::create([
                            'occupation' => $userOwner->occupation,          
                            'image'=>'user_perfil.jpg',
                            'access_type' => 1,
                            'user_type' => 3,
                            'user_dependency_id' => $userManagerId,
                            'own_state' => 1,
                            'payment_state'=> $payment_state, //pendiente
                            'state' => 1,
                        ]);
                        $user->assignRole('subscriber');
                        UserDependencyM::create([
                            'dependent_user_id' => $user->id,
                            'user_subscription_id' => $item->id,
                        ]);
                    }
                }
                DB::commit();
                $this->enablePayment = false;              
                
                $this->getData();
                if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                    $this->dispatch('successAlert',[ 'success' => 'Usuarios regitrados exitosamente.' ]);
                }
                $this->payType = -1;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->linkDePago=null;
                $this->dispatch('failedAlert', ['failed' => 'Hubo un problema al guardar los datos. Intenta nuevamente.' ]);                
            }

        }else if($this->selectedOption=="new_station"){
            date_default_timezone_set('America/La_Paz');
            try {

                DB::beginTransaction();            
                $userOwner = User::find($this->userId);
                $userOwnerSubs = UsersSubscriptionM::where('user_id',$this->userId)->first();
                $station = null;
                if($this->selectedStationId!=null && $this->selectedStationId!=-1){
                    $station = StationM::where('id',$this->selectedStationId)->first();
                }
                $fechaFinal = Carbon::parse($this->dateNewDateStation)->subMonth();
                $dateBeforeOneMonth = $fechaFinal->toDateString();

                $is_it_paid_renewal = 0;
                $payment_type_renewal= 0;
                $payment_state = 0; //for user
                if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                    $payment_state = 1;
                    $is_it_paid_renewal = 1;                        
                    if (in_array($this->payType, ['Efectivo'])) {
                        $payment_type_renewal= 1;
                    }else{
                        $payment_type_renewal= 4;
                    }                        
                }
                
                $user = User::create([
                    'username' => $this->name.$this->lastname,
                    'name' => $this->name,
                    'lastname' => $this->lastname,
                    'occupation' => $userOwner->occupation,
                    'email' => $this->email,
                    'number_phone' => $this->number_phone,
                    'password' => Hash::make($this->password),
                    'image'=>'user_perfil.jpg',
                    'access_type' => 1,
                    'user_type' => 2,
                    'user_dependency_id' => $userOwner->id,
                    'own_state' => 1,
                    'payment_state'=> $payment_state, 
                    'state' => 1,
                ]);
                $user->assignRole('subscriber'); 
                  
                
                $subsReg = UsersSubscriptionM::create([
                    'business_name'=>$userOwnerSubs->business_name,
                    'nit'=>$userOwnerSubs->nit,               
                    'reg_date' => Carbon::today()->format('Y-m-d') ,
                    'accumulation_date' => Carbon::today()->format('Y-m-d'),
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'expiration_notice' => $dateBeforeOneMonth,
                    'date_expiry' => $this->dateNewDateStation,
                    'is_discount'  => 0,
                    'amount'=> $this->valueDuration,
                    'cost' => $this->total_to_pay,
                    'latitude' => $station!==null?$station->latitude:'-17.782879',
                    'longitude' => $station!==null?$station->longitude:'-63.181668',
                    'invitation_reference_data' => null,
                    'temporary_extension_start' => null,
                    'temporary_extension_end' => null,
                    'incident_extension' => null,
                    'data_save' => null,
                    'is_it_paid_renewal'=> $is_it_paid_renewal,
                    'payment_type_renewal'=> $payment_type_renewal,                    
                    'station_id' => $station!==null?$station->id:null,
                    'state' => 1,
                    'user_id' => $userOwner->id,
                    'subscription_id' => SubscriptionM::where('tag', $this->subscription_type)->value('id'),                
                ]);

                UserDependencyM::create([
                    'dependent_user_id' => $user->id,
                    'user_subscription_id' => $subsReg->id,
                ]);

                UsersSubscriptionM::where('id',$subsReg->id)
                ->update([
                    'data_save' => $subsReg,
                ]);

                for ($i=0; $i < 10; $i++) {
                    $userDep = User::create([
                        'occupation' => $userOwner->occupation,
                        'image'=>'user_perfil.jpg',
                        'access_type' => 1,
                        'user_type' => 3,
                        'user_dependency_id' => $user->id,
                        'own_state' => 1,
                        'payment_state'=> $payment_state,
                        'state' => 1,
                    ]);
                    $userDep->assignRole('subscriber');

                    UserDependencyM::create([
                        'dependent_user_id' => $userDep->id,
                        'user_subscription_id' => $subsReg->id,
                    ]);
                }

                $this->newStationSuspcriptId = $subsReg->id;

                //Preparar identificador_transactionId
                $identificador = $this->newStationSuspcriptId;
                $identificador = $identificador.',renewal';
                if($this->externalUserId!=null){
                    $identificador = $identificador.','.$this->externalUserId;
                }
                $transactionId = $this->textEncryption($identificador,'encode');
                $this->identifierPay = $transactionId;

                UsersSubscriptionM::where('id',$this->newStationSuspcriptId)
                ->update([
                    'payment_link_renewal' => $this->linkDePago,           
                    'transaction_renewal_id'=> $transactionId,
                ]);
                DB::commit();                                
               
                $this->name = null;
                $this->lastname = null;                
                $this->email = null;
                $this->number_phone = null;
                $this->valueDuration = null;
                $this->password = null;
                $this->total_to_pay = null;
                $this->enablePayment = false;
                $this->valueDuration = -1;
                
                $this->getData();
                if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                    $this->dispatch('successAlert',[ 'success' => 'Registro de nueva suscripción exitosa.' ]);
                }     
                $this->payType = -1;
                
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->newStationSuspcriptId = null;
                dd($e);
                $this->dispatch('failedAlert',[ 'failed' => 'Hubo un problema al guardar los datos. Intenta nuevamente.' ]);         
            }

            

        }else if($this->selectedOption=="renewal_personal"){
            date_default_timezone_set('America/La_Paz');
            try {

                DB::beginTransaction();
                $userOwner = User::find($this->userId);
                $userOwnerSubs = UsersSubscriptionM::where('user_id',$this->userId)->first();
                $fechaFinal = Carbon::parse($this->dateNewDateStation)->subMonth();
                $dateBeforeOneMonth = $fechaFinal->toDateString();
                  
                
                HistoricalUsersSubscriptionM::create([
                    'business_name'=>$userOwnerSubs->business_name,
                    'nit'=>$userOwnerSubs->nit,               
                    'reg_date' => $userOwnerSubs->reg_date,
                    'accumulation_date' => $userOwnerSubs->accumulation_date,
                    'start_date' => $userOwnerSubs->start_date,
                    'expiration_notice' => $userOwnerSubs->expiration_notice,
                    'date_expiry' => $userOwnerSubs->date_expiry,
                    'is_discount'  => $userOwnerSubs->is_discount,
                    'added_user_count' => $userOwnerSubs->added_user_count,
                    'added_user_total_cost' => $userOwnerSubs->added_user_total_cost,
                    'amount'  => $userOwnerSubs->amount,
                    'cost' => $userOwnerSubs->cost,
                    'latitude' => $userOwnerSubs->latitude,
                    'longitude' => $userOwnerSubs->longitude,
                    'invitation_reference_data' => $userOwnerSubs->invitation_reference_data,
                    'temporary_extension_start' => $userOwnerSubs->temporary_extension_start,
                    'temporary_extension_end' => $userOwnerSubs->temporary_extension_end,
                    'incident_extension' => $userOwnerSubs->incident_extension,
                    'data_save' => $userOwnerSubs->data_save,
                    'is_it_paid'=> $userOwnerSubs->is_it_paid,
                    'payment_type'=> $userOwnerSubs->payment_type,
                    'state' => $userOwnerSubs->state,
                    'user_id' => $userOwnerSubs->user_id,
                    'subscription_id' => $userOwnerSubs->subscription_id,
                    'user_subscription_id' => $userOwnerSubs->id,            
                ]); 
                
                $is_it_paid_renewal = 0;
                $payment_type_renewal= 0;
                $payment_state = 0; //for user
                if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                    $payment_state = 1;
                    $is_it_paid_renewal = 1;                        
                    if (in_array($this->payType, ['Efectivo'])) {
                        $payment_type_renewal= 1;
                    }else{
                        $payment_type_renewal= 4;
                    }                        
                }

                $newSusbscriptionId = null;
                if($userOwnerSubs->subscription_id==1){ //demo personal
                    $newSusbscriptionId = 3; //personal
                }else if($userOwnerSubs->subscription_id==2){// demo profesional
                    $newSusbscriptionId = 4; //profesional
                }else{
                    $newSusbscriptionId = $userOwnerSubs->subscription_id;
                }

                $subsReg = UsersSubscriptionM::where('id',$userOwnerSubs->id)
                ->update([
                    'business_name'=>$this->business_name,
                    'nit'=>$this->nit,
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'expiration_notice' => $dateBeforeOneMonth,
                    'date_expiry' => $this->dateNewDateStation,
                    'amount'  => $this->valueDuration,
                    'cost' => $this->total_to_pay,
                    'temporary_extension_start' => null,
                    'temporary_extension_end' => null,               
                    'is_it_paid_renewal'=> $is_it_paid_renewal,
                    'payment_type_renewal'=> $payment_type_renewal,
                    'subscription_id'=> $newSusbscriptionId
                ]);

                UsersSubscriptionM::where('id',$userOwnerSubs->id)
                ->update([
                    'data_save' => json_encode(UsersSubscriptionM::where('id', $userOwnerSubs->id)->first()->toArray()),
                ]);    
                $this->renewalSuspcriptId = $userOwnerSubs->id;

                UsersSubscriptionM::where('id',$this->renewalSuspcriptId)
                ->update([ 
                    'payment_link_renewal' => $this->linkDePago,               
                    'transaction_renewal_id'=> $transactionId,
                ]);

                DB::commit();
                $this->enablePayment = false;
                $this->valueDuration = -1;
                
                $this->getData();
                if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                    $this->dispatch('successAlert',[ 'success' => 'Registro de Renovación exitosa.' ]);
                }     
                $this->payType = -1; 
            } catch (\Throwable $e) {
                $this->renewalSuspcriptId = null;
                DB::rollBack();                
                //dd($e);
                $this->dispatch('failedAlert',[ 'failed' => 'Hubo un problema al guardar los datos. Intenta nuevamente.' ]);                
            }            
        }

    }
    
    public function render(){
        return view('livewire.pay.pay');
    }
}

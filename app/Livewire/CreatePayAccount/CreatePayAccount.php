<?php

namespace App\Livewire\CreatePayAccount;


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
use App\Models\IpRegistration;
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

class CreatePayAccount extends Component
{
    use WithFileUploads;
    use WithPagination;
    use AuthValidationTrait;
    public $headers;
    protected $paginationTheme = 'tailwind';
   
    public $searchTitle = null;
    public $externalUserId = null;
    public $giveAccess = false;
    public $giveAccessError = null;
   
    public $user_type = null;
    public $subscription_type = null;
    public $total_to_pay = null;
    public $valueDuration = 1;
    public $dateRegister = null;
    public string $selectedOption = '';
    public $currencyOfExchange = 6.96;   
   
    public $selectDateAndCash = [];
    public $selectionType = null;
    public $userId = null;
    public $userSubscriptionId = null;


    // pay type -------
    public $payType = null;
    public $listPayType = [];
    public $typeSubsArray = [];
    public $typeSubsNameArray = [];
    public $occupations = ['Estudiante / Docente','Institución / Fundación','Empresa / Productor','Insumos Agrícolas'];
    //form -----
    public string $hp_field = '';
    public int $form_loaded_at;

    public $business_name = null;
    public $nit = null;   
    public $name = null;
    public $lastname = null;
    public $occupation = -1;
    public $email = null;
    public $number_phone = null;
    public $password = null;
    public $valueTypeSubs = -1;
    public $buttonSubscript = false;

    public $nameAdm = null;
    public $lastnameAdm = null;
    public $emailAdm = null;
    public $number_phoneAdm = null;
    public $passwordAdm = null;
   
    public $dateNewDateStation = null;
    public $failedDescription = null;

    public $subsCorporate = null;
    public $subsPersonal = null;
    public $subsProfessional = null;
    //DESCUENTO
    public $specialDiscounts = [];
    public $specialDiscountsId = -1;
    public $discountText = null;

    //PASARELA
    public $qrUrl = null;
    public $linkDePago = null;
    public $enablePayment = false;
    public $formCorporate = false;

    public $forIOS = null;

    protected function rules(){
        $rules = [
            //'business_name' => 'required|string|max:40',
            //'nit' => 'required|digits_between:6,15',
            'name' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|max:100|unique:users,email',
            //'number_phone' => 'digits_between:6,15|unique:users,number_phone',
            'password' => 'required|string|min:6|max:100',
        ];
        if($this->business_name!='' && $this->business_name!=null){
            $rules['business_name'] = 'string|max:40';
        } 
        if($this->nit!='' && $this->nit!=null){
            $rules['nit'] = 'digits_between:6,15';
        } 

        if($this->valueTypeSubs!="demo personal"){
            $rules['business_name'] = 'string|max:40';
            $rules['nit'] = 'digits_between:6,15';
            //$rules['number_phone'] = 'digits_between:6,15|unique:users,number_phone';
        }  
        if($this->number_phone!='' && $this->number_phone!=null){
            $rules['number_phone'] = 'digits_between:6,15|unique:users,number_phone';
        } 

        if ($this->valueTypeSubs!=null && $this->valueTypeSubs!=-1 && in_array($this->valueTypeSubs, [ 'corporate'])) {
            $rules['nameAdm'] = 'required|string|max:40';
            $rules['lastnameAdm'] = 'required|string|max:40';
            $rules['emailAdm'] = 'required|email|max:100|unique:users,email';
            if($this->number_phone!='' && $this->number_phone!=null){
                $rules['number_phoneAdm'] = 'digits_between:6,15|unique:users,number_phone';
            } 
            //$rules['number_phoneAdm'] = 'digits_between:6,15|unique:users,number_phone';
            $rules['passwordAdm'] = 'required|string|min:6|max:100';            
        }

        return $rules;
    }

    protected $messages = [
        'business_name.required' => 'La Razón social es obligatoria.',
        'business_name.string' => 'La Razón social debe ser una cadena de texto.',
        'business_name.max' => 'La Razón social no debe tener más de 40 caracteres.',
        
        'number_phone.required' => 'El nit es obligatorio.',
        'number_phone.numeric' => 'El nit debe contener solo números.',
        'number_phone.digits_between' => 'El nit debe tener entre 5 y 20 dígitos.',

        'lastname.required' => 'El nit es obligatorio.',
        'lastname.string' => 'El nit debe ser una cadena de texto.',
        'lastname.max' => 'El nit no debe tener más de 40 caracteres.', 
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
        //'number_phone.numeric' => 'El número de teléfono debe contener solo números.',
        'number_phone.digits_between' => 'El número de teléfono debe tener entre 6 y 15 dígitos.',
        'number_phone.unique' => 'Este número ya está registrado.',


        // Campos del admin corporativo
        'password.required' => 'La contraseña es obligatoria.',
        'password.string' => 'La contraseña debe ser una cadena de texto.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.max' => 'La contraseña no debe superar los 100 caracteres.',    

        'nameAdm.required' => 'El nombre es obligatorio.',
        'nameAdm.string' => 'El nombre debe ser una cadena de texto.',
        'nameAdm.max' => 'El nombre no debe tener más de 40 caracteres.',
    
        'lastnameAdm.required' => 'El apellido es obligatorio.',
        'lastnameAdm.string' => 'El apellido debe ser una cadena de texto.',
        'lastnameAdm.max' => 'El apellido no debe tener más de 40 caracteres.',
    
        'emailAdm.required' => 'El correo electrónico es obligatorio.',
        'emailAdm.email' => 'El correo electrónico no es válido.',
        'emailAdm.max' => 'El correo electrónico no debe superar los 100 caracteres.',
        'emailAdm.unique' => 'Este correo ya está registrado.',
    
        'number_phoneAdm.required' => 'El número de teléfono es obligatorio.',
        //'number_phoneAdm.numeric' => 'El número de teléfono debe contener solo números.',
        'number_phoneAdm.digits_between' => 'El número de teléfono debe tener entre 6 y 15 dígitos.',
        'number_phoneAdm.unique' => 'Este número ya está registrado.',
    
        'passwordAdm.required' => 'La contraseña es obligatoria.',
        'passwordAdm.string' => 'La contraseña debe ser una cadena de texto.',
        'passwordAdm.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'passwordAdm.max' => 'La contraseña no debe superar los 100 caracteres.',
       
    ];

    public function mount($transaction_id = null,$payment_method=null,$message = null, $for_IOS = null){
        $this->forIOS = $for_IOS;
                     
        if ($transaction_id !== null && strlen($transaction_id) > 20) {
            $data = $this->textEncryption($transaction_id,'decode');           
            $array = explode(',', $data);
            $typePayment = null;
            $typeRenewal = null;
            $isSuccessful = false;

            try {
                $userSubscriptionID = intval($array[0]);
                $userID = intval($array[1]);
                $typeRenewal = $array[2];
                
                if($payment_method!=null){
                    if($payment_method=="ATC_QR"){
                        $typePayment = 3;
                    }else if($payment_method=="ATC_Tarjeta"){ //Card
                        $typePayment = 2;
                    }else{
                        $typePayment = 2;//forzamos a Tarjeta
                        //$typePayment = 5;//otros
                    }
                }
                //is_it_paid_renewal
                try {
                    DB::beginTransaction();
                    UsersSubscriptionM::where("id",$userSubscriptionID)->where("transaction_".$typeRenewal."_id",$transaction_id)
                    ->update([
                        "is_it_paid_".$typeRenewal => 1,
                        "payment_type_".$typeRenewal => $typePayment,
                    ]);
                        
                    $data = UserDependencyM::join('users', 'users.id', '=', 'user_dependency.dependent_user_id')
                    ->where('user_dependency.user_subscription_id', $userSubscriptionID)
                    ->pluck('users.id')
                    ->toArray();

                    User::whereIn('id',$data)
                    ->update([
                        'payment_state' => 1,                            
                    ]);                
                    DB::commit();
                    $isSuccessful=true;
                } catch (\Throwable $e) {
                    DB::rollBack();
                    //dd($e);
                }
               
                if($isSuccessful){
                    $this->initialize();
                    if (Auth::check()){
                        $this->dispatch('successAlert',[ 'success' => 'Registro de suscripción exitosa. El usuario ya está habilitado para iniciar sesión.' ]);
                        $this->initialize();  
                    }else{
                        $this->initialize();  
                        $user = User::find($userID);
                        $userData = UserDependencyM::with(['user', 'subscription'])->where('dependent_user_id',$userID)->first();                        
                        if($userData->subscription->subscription->tag=="personal" || $userData->subscription->subscription->tag=="demo personal"){
                            $this->dispatch('successAlert',[ 'success' => '¡Registro completado con éxito!<br> Por favor, accede a la aplicación móvil SenvaTec 3.0. Puedes iniciar sesión utilizando tu correo electrónico o número de teléfono, junto con la contraseña que registraste al crear tu cuenta en este formulario.' ]);                            
                        }else{
                            Auth::login($user);
                            $namefull = $user->name.' '.$user->lastname;
                            if(strlen((trim($namefull)) > 0)){
                                $namefull = ", ".$namefull;
                            }else{
                                $namefull = "";
                            }
                            return redirect()->route('dashboard')
                            ->with([
                                'nameUser' => $namefull,
                                'bienvenida' => '¡Bienvenido, tu cuenta ha sido creada exitosamente!'
                            ]);
                        }
                    }
                    
                }else{
                    $this->initialize();

                    $this->dispatch('failedAlert',[ 'failed' => 'Error al actualizar el estado de pago. Por favor, inténtelo más tarde o contactese con soporte.' ]); 
                }
            } catch (\Throwable $e) {
                $this->initialize();  
            }
        }else{            
            $this->initialize();            
        }
    }

    private function initialize(){
        $this->form_loaded_at = time();
        $this->business_name = null;
        $this->nit = null;
        $this->name = null;
        $this->lastname = null;
        $this->occupation = -1;
        $this->email = null;
        $this->number_phone = null;
        $this->password = null;
        $this->valueTypeSubs = -1;
        $this->buttonSubscript = false;

        $this->nameAdm = null;
        $this->lastnameAdm = null;
        $this->emailAdm = null;
        $this->number_phoneAdm = null;
        $this->passwordAdm = null;

        $this->subsCorporate = SubscriptionM::where('state',1)->where('tag','corporate')->first()->toArray();
        $this->subsPersonal = SubscriptionM::where('state',1)->where('tag','personal')->first()->toArray();
        $this->subsProfessional = SubscriptionM::where('state',1)->where('tag','professional')->first()->toArray();
        $this->specialDiscounts = [];
        
        /*if(count($this->specialDiscounts) > 0){
           //dd($this->specialDiscounts);
           $this->specialDiscountsId = $this->specialDiscounts[0]['id'];           
        }*/
        
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasAnyRole(['super_manager', 'manager', 'meteorology','meteorologist']) && $user->access_type==0) {
                $this->typeSubsArray =['demo personal','personal','demo professional','professional','corporate'];
                $this->typeSubsNameArray = ['Demo','Personal','Demo profesional','Profesional','Corporativo'];
                $this->listPayType = ['QR/Tarjeta','Efectivo','Cortesía'];
                $this->payType = -1;
            }else{
                $this->typeSubsArray =['demo personal','personal','professional','corporate'];
                $this->typeSubsNameArray = ['Demo','Personal','Profesional','Corporativo'];
                $this->listPayType = ['QR/Tarjeta'];
                $this->payType = "QR/Tarjeta";
            }
        }else{
            if($this->forIOS){
                $this->typeSubsNameArray = ['Demo'];
                $this->typeSubsArray =['demo personal'];
            }else{
                $this->typeSubsNameArray = ['Demo','Personal','Profesional','Corporativo'];
                $this->typeSubsArray =['demo personal','personal','professional','corporate'];
            }
            $this->listPayType = ['QR/Tarjeta'];
            $this->payType = "QR/Tarjeta";
        }
    }

    public function applySpecialDiscount(){
        $this->specialDiscountsId = -1;
        $this->discountText = null;
    }

    public function applyType(){
        
        date_default_timezone_set('America/La_Paz');
        if ($this->valueTypeSubs==null || $this->valueTypeSubs==-1 || !in_array($this->valueTypeSubs, ['demo personal', 'personal', 'demo professional', 'professional', 'corporate'])) {
            $this->valueTypeSubs=-1; 
            $this->buttonSubscript = false;
            $this->formCorporate = false; 
            $this->dateRegister = null; 
            $this->total_to_pay = null;
        }else{
            
            if($this->valueTypeSubs=="corporate"){
                $this->formCorporate = true;
                $this->total_to_pay = $this->currencyOfExchange * ($this->subsCorporate != null ? (isset($this->subsCorporate['price']) ? $this->subsCorporate['price'] : null): null);
                if($this->total_to_pay!=null){
                    $fechaEnUnAnio = Carbon::now()->addYear();
                    $this->dateRegister = $fechaEnUnAnio->toDateString(); 
                    $this->buttonSubscript = true;
                }else{
                   $this->dateRegister = null; 
                    $this->buttonSubscript = false;
                }
            }else if($this->valueTypeSubs=="professional"){  
                           
                $this->total_to_pay = $this->currencyOfExchange * ($this->subsProfessional != null ? (isset($this->subsProfessional['price']) ? $this->subsProfessional['price'] : null): null);
                $this->formCorporate = false;
                $this->nameAdm = null;
                $this->lastnameAdm = null;
                $this->emailAdm = null;
                $this->number_phoneAdm = null;
                $this->passwordAdm = null;
                if($this->total_to_pay!=null){
                    $fechaEnUnAnio = Carbon::now()->addYear();
                    $this->dateRegister = $fechaEnUnAnio->toDateString(); 
                    $this->buttonSubscript = true;
                }else{
                   $this->dateRegister = null; 
                    $this->buttonSubscript = false;
                }                

            }else if($this->valueTypeSubs=="demo professional"){
                if(count($this->typeSubsArray) == 5){                    
                    $fechaEn7Dias = Carbon::now()->addDays(7);
                    $this->dateRegister = $fechaEn7Dias->toDateString();
                    $this->buttonSubscript = true;                
                }else{
                    $this->dateRegister = null;
                    $this->buttonSubscript = false;
                }                
                $this->total_to_pay = null;
                $this->formCorporate = false;
                $this->nameAdm = null;
                $this->lastnameAdm = null;
                $this->emailAdm = null;
                $this->number_phoneAdm = null;
                $this->passwordAdm = null;
                
                //dd($this->valueTypeSubs,$this->typeSubsArray,$this->enablePayment,$this->buttonSubscript);
            }else if($this->valueTypeSubs=="personal"){
                
                $this->total_to_pay = $this->currencyOfExchange * ($this->subsPersonal != null ? (isset($this->subsPersonal['price']) ? $this->subsPersonal['price'] : null): null);
                $this->formCorporate = false;
                $this->nameAdm = null;
                $this->lastnameAdm = null;
                $this->emailAdm = null;
                $this->number_phoneAdm = null;
                $this->passwordAdm = null;
                
                if($this->total_to_pay!=null){
                    $fechaEnUnAnio = Carbon::now()->addYear();
                    $this->dateRegister = $fechaEnUnAnio->toDateString();
                    $this->buttonSubscript = true;
                }else{
                   $this->dateRegister = null; 
                    $this->buttonSubscript = false;
                } 
            }else if($this->valueTypeSubs=="demo personal"){
                $fechaEn7Dias = Carbon::now()->addDays(7);
                $this->dateRegister = $fechaEn7Dias->toDateString();
                $this->total_to_pay = null;
                $this->formCorporate = false;
                $this->nameAdm = null;
                $this->lastnameAdm = null;
                $this->emailAdm = null;
                $this->number_phoneAdm = null;
                $this->passwordAdm = null;
                $this->buttonSubscript = true;
                
            }else{
                $this->dateRegister = null;
                $this->total_to_pay = null;
                $this->formCorporate = false;
                $this->nameAdm = null;
                $this->lastnameAdm = null;
                $this->emailAdm = null;
                $this->number_phoneAdm = null;
                $this->passwordAdm = null;
                $this->valueTypeSubs=-1;
                $this->buttonSubscript = false;
                
            }
            $this->specialDiscountsId = -1;
            $this->specialDiscounts = [];
            $this->discountText = null;
        }
    }

    public function addPayNewAccount(){
        $this->enablePayment = false;
        //try {
       
            $this->validate();
            $flag = $this->permitted();     
                 
            if($flag){
                if($this->valueTypeSubs=="corporate"){
                    $flag = $this->permittedPersonalAm();
                    if($flag){
                        if (Auth::check()) {
                            $user = Auth::user();
                            if ($user->hasAnyRole(['super_manager', 'manager', 'meteorology','meteorologist']) && $user->access_type==0) {
                                $this->enablePayment = true;
                            }else{

                                $ip = request()->ip();
                                $today = now()->toDateString();
                                $record = IpRegistration::firstOrCreate(
                                    ['ip' => $ip, 'date' => $today],
                                    ['attempts' => 0]
                                );
                                if ($record->attempts < 3) {
                                    $record->increment('attempts');
                                    $this->payWith();
                                }else{
                                    $this->dispatch('failedAlert',[ 'failed' => 'Demasiados intentos de registro. Por favor, inténtelo más tarde.' ]);
                                }
                                
                                
                            }
                        }else{

                            $ip = request()->ip();
                            $today = now()->toDateString();
                            $record = IpRegistration::firstOrCreate(
                                ['ip' => $ip, 'date' => $today],
                                ['attempts' => 0]
                            );
                            if ($record->attempts < 3) {
                                $record->increment('attempts');
                                $this->payType = 'QR/Tarjeta';
                                $this->payWith();
                            }else{
                                $this->dispatch('failedAlert',[ 'failed' => 'Demasiados intentos de registro. Por favor, inténtelo más tarde.' ]);
                            }
                        }                        
                    }else{
                        $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);
                    }
                }else{
                    
                    if (Auth::check()) {
                        $user = Auth::user();
                        if ($user->hasAnyRole(['super_manager', 'manager', 'meteorology','meteorologist']) && $user->access_type==0) {
                            
                            $this->enablePayment = true;
                        }else{
                             $ip = request()->ip();
                                $today = now()->toDateString();
                                $record = IpRegistration::firstOrCreate(
                                    ['ip' => $ip, 'date' => $today],
                                    ['attempts' => 0]
                                );
                                if ($record->attempts < 3) {
                                    $record->increment('attempts');
                                    $this->payWith();
                                }else{
                                    $this->dispatch('failedAlert',[ 'failed' => 'Demasiados intentos de registro. Por favor, inténtelo más tarde.' ]);
                                }
                        }
                    }else{
                       
                        $ip = request()->ip();
                        $today = now()->toDateString();
                        $record = IpRegistration::firstOrCreate(
                            ['ip' => $ip, 'date' => $today],
                            ['attempts' => 0]
                        );
                        if ($record->attempts < 3) {
                            $record->increment('attempts');
                            $this->payType = 'QR/Tarjeta';
                            
                            $this->payWith();
                        }else{
                            $this->dispatch('failedAlert',[ 'failed' => 'Demasiados intentos de registro. Por favor, inténtelo más tarde.' ]);
                        }
                    } 
                }            
            }else{
                $this->dispatch('failedAlert',[ 'failed' => $this->failedDescription ]);
            }
        //} catch (\Illuminate\Validation\ValidationException $e) {
            //dd('VALIDACIÓN FALLÓ', $e->errors());
        //}
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
        }else if($this->number_phone!='' && $this->number_phone!=null){
            if(User::where('number_phone', $this->number_phone)->count() > 0){
                $this->failedDescription = 'El número de teléfono: '.$this->number_phone.' no puede ser registrado porque ya está asignado a otro usuario';
                $break = false;
            }
            
        }else if (!empty($this->password) && strlen($this->password) < 6) {
            $this->failedDescription = 'La nueva contraseña debe tener por lo menos 6 o más caracteres';
            $break = false;
        }else if ($this->valueTypeSubs==null || $this->valueTypeSubs==-1 || !in_array($this->valueTypeSubs, ['demo personal', 'personal', 'demo professional', 'professional', 'corporate'])) {
            $this->valueTypeSubs=-1;
            $this->failedDescription = 'Debe seleccionar un tipo de suscripción valido';
            $break = false;
        }else if(!in_array($this->occupation,['Estudiante / Docente','Institución / Fundación','Empresa / Productor','Insumos Agrícolas'])){
            $this->failedDescription = 'Debe seleccionar una ocupación valida.';
            $break = false;
            $this->occupation=-1;

        }else if (!empty($this->hp_field)) {
            $this->failedDescription = 'Bot detectado.';
            $break = false;
            $this->hp_field = '';

        }else if ((time() - $this->form_loaded_at) < 7) {
           $this->failedDescription = 'Formulario enviado demasiado rápido.';
            $break = false;
            $this->form_loaded_at = time();
           
        }

        

        return $break;
    }   

    private function permittedPersonalAm(){
        $break = true;
        if(User::where('email',$this->emailAdm)->count() > 0){
            $this->failedDescription='El correo: '.$this->email.'del administrador no puede ser registrado porque ya existe, y esta asignado a otro usuario';
            $break=false;
        }else if($this->number_phoneAdm!='' && $this->number_phoneAdm!=null){
            if(User::where('number_phone', $this->number_phoneAdm)->count() > 0){
                $this->failedDescription = 'El número de teléfono: '.$this->number_phoneAdm.'del administrador no puede ser registrado porque ya está asignado a otro usuario';
                $break = false;
            }
            
        }else if (!empty($this->passwordAdm) && strlen($this->passwordAdm) < 6) {
            $this->failedDescription = 'La nueva contraseña para el aministrador debe tener por lo menos 6 o más caracteres';
            $break = false;
        }
        return $break;
    }

    public function updatedSelectedOption($value){        
        $this->getData();
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
                return null;
            }
        
            $iv = base64_decode($decoded['iv']);
            $code = $decoded['value'];
        
            $res = openssl_decrypt($code, $strategy, $cypher, 0, $iv);
        }
        return $res;
    }
    
    public function payWith(){
        date_default_timezone_set('America/La_Paz');
        $this->giveAccess = false;
        //Evitando alteracion desde el navegador
        
        if(in_array($this->payType, ['QR/Tarjeta','Efectivo','Cortesía'])) {
            if(in_array($this->valueTypeSubs,['demo personal','personal','demo professional','professional','corporate'])){
                if(in_array($this->occupation,['Estudiante / Docente','Institución / Fundación','Empresa / Productor','Insumos Agrícolas'])){
                    if($this->valueTypeSubs=="corporate"){
                        $fechaEnUnAnio = Carbon::now()->addYear();
                        $this->dateRegister = $fechaEnUnAnio->toDateString();
                    }else if($this->valueTypeSubs=="professional"){
                        $fechaEnUnAnio = Carbon::now()->addYear();
                        $this->dateRegister = $fechaEnUnAnio->toDateString();
                    }else if($this->valueTypeSubs=="demo professional"){
                        $fechaEn7Dias = Carbon::now()->addDays(7);
                            $this->dateRegister = $fechaEn7Dias->toDateString();
                    }else if($this->valueTypeSubs=="personal"){
                        $fechaEnUnAnio = Carbon::now()->addYear();
                        $this->dateRegister = $fechaEnUnAnio->toDateString();
                    }else if($this->valueTypeSubs=="demo personal"){
                        
                        $fechaEn7Dias = Carbon::now()->addDays(7);
                        $this->dateRegister = $fechaEn7Dias->toDateString();
                    }else{
                        $this->dateRegister = null;
                    }

                    $this->registerData();
                }else{
                    $this->occupation = -1;
                   
                }
            }else{                
                $this->valueTypeSubs =-1;                
            } 
            
            //dd($this->giveAccess,$this->userId,$this->userSubscriptionId);           
            
            if($this->giveAccess==true && $this->userId!=null && $this->userSubscriptionId!=null){
                if($this->valueTypeSubs=="demo personal"){
                    $this->dispatch('successAlert',[ 'success' => '¡Registro completado con éxito!<br> Por favor, accede a la aplicación móvil SenvaTec 3.0. Puedes iniciar sesión utilizando tu correo electrónico o número de teléfono, junto con la contraseña que registraste al crear tu cuenta en este formulario.' ]);
                }else{
                    if (in_array($this->payType, ['QR/Tarjeta'])) {
                        
                        $fechaActual = new DateTime();
                        $fechaActual->modify('+1 year');
                        $fecha_vencimiento = $fechaActual->format('Y-m-d');                        
                        $metaData = [];
                        $items = [];
                        $identificador = null;
                        $email_cliente = $this->email;
                        $descripcion = 'Pago Online Senvatec';
                        $nombre_cliente = trim($this->name);
                        $apellido_cliente = trim($this->lastname);        
                        
                        $razon_social = $this->business_name;
                        $numero_documento = $this->nit;
                        $identificador = (string)$this->userSubscriptionId.','.(string)$this->userId;
                        $identificador = $identificador.',renewal';
                        //dd($identificador,$this->textEncryption($identificador,'encode'));
                        $identificador = $this->textEncryption($identificador,'encode');             
                        $costo = floatval($this->total_to_pay);
                        
                        $metaData[] = [
                            'nombre' => trim($nombre_cliente.' '.$apellido_cliente),
                            'dato' => "Tiempo de vigencia: ".(string)$this->valueDuration.",  Expiración:".$this->dateRegister.",  Costo: ".(string) $this->total_to_pay. " Bs",
                        ];

                        $items[] =[
                            'concepto' => 'Registro de Suscripción con codigo '.(string)'',
                            'cantidad' => intval($this->valueDuration),
                            'costo_unitario' =>$costo,
                            'codigo_producto' => (string)$this->userSubscriptionId.'-RSU',
                            'descuento_unitario' => 0,
                            'ignora_factura' => false,
                        ];

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
                            'callback_url' => env('APP_URL').env('PAYMENT_CREATE_ROUTE_CALLBACK'),
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
                            //dd($data,$this->giveAccess, $this->userId, $this->userSubscriptionId);
                            $this->linkDePago = $data['url_pasarela_pagos'] ?? null;
                            
                            UsersSubscriptionM::where('id',$this->userSubscriptionId)
                            ->update([ 
                                'payment_link_renewal' => $this->linkDePago,               
                                'transaction_renewal_id'=> $identificador,
                            ]);
                            //dd($this->linkDePago);
                        } else {
                            $this->deleteRegisterGenerate();
                        } 
                    }else{

                        if (in_array($this->payType, ['Efectivo','Cortesía'])) {
                            if (Auth::check()) {                            
                                $this->dispatch('successAlert',[ 'success' => 'Registro de suscripción exitosa. El usuario ya está habilitado para iniciar sesión.' ]);
                            }else{
                                $user = User::find($this->userId);
                                Auth::login($user);
                                return redirect()->route('dashboard')->with('bienvenida', '¡Bienvenido, tu cuenta ha sido creada exitosamente!');
                            }                        
                        }
                    }
                }
                $this->enablePayment = false;
                $this->payType = -1;
                //$this->linkDePago=null;
                $this->valueTypeSubs = -1;
                $this->dateRegister = null;
                $this->total_to_pay = null;
                $this->formCorporate = false;

                $this->form_loaded_at = time();
                $this->business_name = null;
                $this->nit = null;
                $this->name = null;
                $this->lastname = null;
                $this->occupation = -1;
                $this->email = null;
                $this->number_phone = null;
                $this->password = null;
                $this->valueTypeSubs = -1;
                $this->buttonSubscript = false;

                $this->nameAdm = null;
                $this->lastnameAdm = null;
                $this->emailAdm = null;
                $this->number_phoneAdm = null;
                $this->passwordAdm = null;

            }else{
                if( $this->valueTypeSubs !=-1){
                    $this->dispatch('failedAlert',[ 'failed' => $this->giveAccessError ]); 
                }
                
            }       
        }else{
            $this->payType="QR/Tarjeta";
        }
    }

    private function registerData(){
        
        date_default_timezone_set('America/La_Paz');
        $this->userId =null;
        $this->userSubscriptionId = null;
        $payment_state = null;
        $is_it_paid_renewal = null;
        $payment_type_renewal = null;
        if($this->payType == "QR/Tarjeta"){
            $payment_state = 0;
            $is_it_paid_renewal = 0;
            $payment_type_renewal = 0;
           
        }else if($this->payType == "Efectivo"){
            $payment_state = 1;
            $is_it_paid_renewal = 1;
            $payment_type_renewal = 1;
            //dd('HERE 1',$payment_state,$this->payType );
        }else if($this->payType == "Cortesía"){
            $payment_state = 1;
            $is_it_paid_renewal = 1;
            $payment_type_renewal = 4;
        }

        
        
        if($this->valueTypeSubs=="corporate"){
            try {

                DB::beginTransaction();                
                
                $fechaFinal = Carbon::parse($this->dateRegister)->subMonth();
                $dateBeforeOneMonth = $fechaFinal->toDateString();

                $user = User::create([
                    'username' => $this->name.$this->lastname,
                    'name' => $this->name,
                    'lastname' => $this->lastname,
                    'occupation' => $this->occupation,
                    'email' => $this->email,
                    'number_phone' => $this->number_phone,
                    'password' => Hash::make($this->password),
                    'image'=>'user_perfil.jpg',
                    'access_type' => 1,
                    'user_type' => 1,
                    'user_dependency_id' => null,
                    'own_state' => 1,
                    'payment_state'=> $payment_state,
                    'state' => 1,
                ]);
                $user->assignRole('subscriber');
                $this->userId = $user->id;
                

                $userAdm = User::create([
                    'username' => $this->nameAdm.$this->lastnameAdm,
                    'name' => $this->nameAdm,
                    'lastname' => $this->lastnameAdm,
                    'occupation' => $this->occupation,
                    'email' => $this->emailAdm,
                    'number_phone' => $this->number_phoneAdm,
                    'password' => Hash::make($this->passwordAdm),
                    'image'=>'user_perfil.jpg',
                    'access_type' => 1,
                    'user_type' => 2,
                    'user_dependency_id' => $user->id,
                    'own_state' => 1,
                    'payment_state'=> $payment_state,
                    'state' => 1,
                ]);
                $userAdm->assignRole('subscriber');
                
                
                $subsReg = UsersSubscriptionM::create([
                    'business_name'=>$this->business_name,
                    'nit'=>$this->nit,               
                    'reg_date' => Carbon::today()->format('Y-m-d') ,
                    'accumulation_date' => Carbon::today()->format('Y-m-d'),
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'expiration_notice' => $dateBeforeOneMonth,
                    'date_expiry' => $this->dateRegister,
                    'is_discount'  => 0,
                    'amount'=> $this->valueDuration,
                    'cost' => $this->total_to_pay,
                    'latitude' => '-17.782879',
                    'longitude' => '-63.181668',
                    'invitation_reference_data' => null,
                    'temporary_extension_start' => null,
                    'temporary_extension_end' => null,
                    'incident_extension' => null,
                    'data_save' => null,
                    'is_it_paid_renewal'=> $is_it_paid_renewal,
                    'payment_type_renewal'=> $payment_type_renewal,                    
                    'station_id' => null,
                    'state' => 1,
                    'user_id' => $user->id,
                    'subscription_id' => SubscriptionM::where('tag', $this->valueTypeSubs)->value('id'),                
                ]);
                $this->userSubscriptionId = $subsReg->id;

                UsersSubscriptionM::where('id',$subsReg->id)
                ->update([
                    'data_save' => $subsReg,
                ]);

                UserDependencyM::create([
                    'dependent_user_id' => $user->id,
                    'user_subscription_id' => $subsReg->id,
                ]);
                UserDependencyM::create([
                    'dependent_user_id' => $userAdm->id,
                    'user_subscription_id' => $subsReg->id,
                ]);

                

                for ($i=0; $i < 10; $i++) {
                    $userDep = User::create([
                        'occupation' => $this->occupation,
                        'image'=>'user_perfil.jpg',
                        'access_type' => 1,
                        'user_type' => 3,
                        'user_dependency_id' => $userAdm->id,
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
                DB::commit();                    
                //$this->reset();
                $this->giveAccess = true;
                $this->giveAccess = true;
                $this->giveAccessError = null;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->userId =null;
                $this->userSubscriptionId = null;
                $this->giveAccess = false; 
                //dd($e,'1');
                $this->giveAccessError = 'No se ha podido registrar la información. Intente nuevamente.';
            }           

        }else if($this->valueTypeSubs=="professional" || $this->valueTypeSubs=="demo professional"){            
            try {
                if($this->valueTypeSubs=="professional"){
                    $fechaFinal = Carbon::parse($this->dateRegister)->subMonth();
                    $dateBeforeOneMonth = $fechaFinal->toDateString();
                }else{
                    $dateBeforeOneMonth = $this->dateRegister;
                }

                DB::beginTransaction();                
                $user = User::create([
                    'username' => $this->name.$this->lastname,
                    'name' => $this->name,
                    'lastname' => $this->lastname,
                    'occupation' => $this->occupation,
                    'email' => $this->email,
                    'number_phone' => $this->number_phone,
                    'password' => Hash::make($this->password),
                    'image'=>'user_perfil.jpg',
                    'access_type' => 1,
                    'user_type' => 1,
                    'user_dependency_id' => null,
                    'own_state' => 1,
                    'payment_state'=> $payment_state,
                    'state' => 1,
                ]);
                $user->assignRole('subscriber');
                $this->userId = $user->id;      
                
                $subsReg = UsersSubscriptionM::create([
                    'business_name'=>$this->business_name,
                    'nit'=>$this->nit,               
                    'reg_date' => Carbon::today()->format('Y-m-d') ,
                    'accumulation_date' => Carbon::today()->format('Y-m-d'),
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'expiration_notice' => $dateBeforeOneMonth,
                    'date_expiry' => $this->dateRegister,
                    'is_discount'  => 0,
                    'amount'=> $this->valueDuration,
                    'cost' => $this->total_to_pay,
                    'latitude' => '-17.782879',
                    'longitude' => '-63.181668',
                    'invitation_reference_data' => null,
                    'temporary_extension_start' => null,
                    'temporary_extension_end' => null,
                    'incident_extension' => null,
                    'data_save' => null,
                    'is_it_paid_renewal'=> $is_it_paid_renewal,
                    'payment_type_renewal'=> $payment_type_renewal,                    
                    'station_id' => null,
                    'state' => 1,
                    'user_id' => $user->id,
                    'subscription_id' => SubscriptionM::where('tag', $this->valueTypeSubs)->value('id'),                
                ]);
                $this->userSubscriptionId = $subsReg->id;

                UserDependencyM::create([
                    'dependent_user_id' => $user->id,
                    'user_subscription_id' => $subsReg->id,
                ]);
                
                UsersSubscriptionM::where('id',$subsReg->id)
                ->update([
                    'data_save' => $subsReg,
                ]);
                DB::commit();                    
                //$this->reset();
                $this->giveAccess = true;
                $this->giveAccessError = null;  
            } catch (\Throwable $e) {
                DB::rollBack();
                
                $this->userId =null;
                $this->userSubscriptionId = null;
                $this->giveAccess= false; 
                //dd($e,'2');
                $this->giveAccessError = 'No se ha podido registrar la información. Intente nuevamente.';              
            }            
        }else if($this->valueTypeSubs=="personal" || $this->valueTypeSubs=="demo personal"){
            
            try {
                if($this->valueTypeSubs=="personal"){
                    $fechaFinal = Carbon::parse($this->dateRegister)->subMonth();
                    $dateBeforeOneMonth = $fechaFinal->toDateString();
                }else{
                    $dateBeforeOneMonth = $this->dateRegister;
                    $payment_state = 1;
                    $is_it_paid_renewal = 1;
                    $payment_type_renewal = 4;
                }                
                
                DB::beginTransaction();                
                $user = User::create([
                    'username' => $this->name.$this->lastname,
                    'name' => $this->name,
                    'lastname' => $this->lastname,
                    'occupation' => $this->occupation,
                    'email' => $this->email,
                    'number_phone' => $this->number_phone,
                    'password' => Hash::make($this->password),
                    'image'=>'user_perfil.jpg',
                    'access_type' => 1,
                    'user_type' => 1,
                    'user_dependency_id' => null,
                    'own_state' => 1,
                    'payment_state'=> $payment_state,
                    'state' => 1,
                ]);
                $user->assignRole('subscriber');
                $this->userId = $user->id;      
                
                $subsReg = UsersSubscriptionM::create([
                    'business_name'=>$this->business_name,
                    'nit'=>$this->nit,               
                    'reg_date' => Carbon::today()->format('Y-m-d') ,
                    'accumulation_date' => Carbon::today()->format('Y-m-d'),
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'expiration_notice' => $dateBeforeOneMonth,
                    'date_expiry' => $this->dateRegister,
                    'is_discount'  => 0,
                    'amount'=> $this->valueDuration,
                    'cost' => $this->total_to_pay,
                    'latitude' => '-17.782879',
                    'longitude' => '-63.181668',
                    'invitation_reference_data' => null,
                    'temporary_extension_start' => null,
                    'temporary_extension_end' => null,
                    'incident_extension' => null,
                    'data_save' => null,
                    'is_it_paid_renewal'=> $is_it_paid_renewal,
                    'payment_type_renewal'=> $payment_type_renewal,                    
                    'station_id' => null,
                    'state' => 1,
                    'user_id' => $user->id,
                    'subscription_id' => SubscriptionM::where('tag', $this->valueTypeSubs)->value('id'),                
                ]);
                $this->userSubscriptionId = $subsReg->id;

                UserDependencyM::create([
                    'dependent_user_id' => $user->id,
                    'user_subscription_id' => $subsReg->id,
                ]);
                
                UsersSubscriptionM::where('id',$subsReg->id)
                ->update([
                    'data_save' => $subsReg,
                ]);                  
                DB::commit();
                //$this->reset();
                $this->giveAccess= true; 
                $this->giveAccessError = null;  
            } catch (\Throwable $e) {
                DB::rollBack(); 
                $this->userId =null;
                $this->userSubscriptionId = null;
                $this->giveAccess= false; 
                //dd($e,'3');
                $this->giveAccess = 'No se ha podido registrar la información. Intente nuevamente.';                               
            }            
        
        }       

    }


    private function deleteRegisterGenerate(){
        try {
            DB::beginTransaction();
            
            UserDependencyM::where('user_subscription_id',$this->userSubscriptionId)->delete();
            UsersSubscriptionM::where('id',$this->userSubscriptionId)->delete();
            if($this->valueTypeSubs=="corporate"){                        
                
                $idsAdm = User::where('user_dependency_id',$this->userId)->pluck('id')->toArray();
                $usersToDelete = User::whereIn('user_dependency_id', $idsAdm)->get();
                foreach ($usersToDelete as $user) {
                    $user->syncRoles([]);
                    $user->syncPermissions([]);
                    $user->delete();
                }

                $users = User::where('user_dependency_id', $this->userId)->get();
                foreach ($users as $user) {
                    $user->syncRoles([]);
                    $user->syncPermissions([]);
                    $user->delete();
                }

                $user = User::find($this->userId);
                if ($user) {
                    $user->syncRoles([]);
                    $user->syncPermissions([]); 
                    $user->delete();
                }
            
            }else{
                $user = User::find($this->userId);
                if ($user) {
                    $user->syncRoles([]);
                    $user->syncPermissions([]); 
                    $user->delete();
                }
            }
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            
            $this->giveAccess= false; 
            $this->giveAccessError = null;
            $this->dispatch('failedAlert',[ 'failed' => 'No se ha podido limpiar los registros generados' ]);
        }
    }
    
    public function render(){
        return view('livewire.create-pay-account.create-pay-account');
    }    
}

<?php

namespace App\Livewire\Subscriptions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubscriptionM;
use Livewire\WithFileUploads;
use App\Models\DetailsSubscriptionM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\ErrorMessage;

class Subscriptions extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $headers;
    protected $paginationTheme = 'tailwind';

    public $stateChange = 'all';
    public $perPage = 10;

    public $sortColumn = 'id';
    public $sortDirection = 'desc';
    public $formAdd = false;
    public $subscriptions = [];
    public $subscriptionsIds = [];
    public $subscriptionDetails = [];
    public $subscriptionId = null;
    public $idU = null;
    public $nameU = null;
    public $tagU = null;
    public $priceU = null;
    public $durationU = null;
    public $amountU = null;
    public $durationDiscountU = null;
    public $durationValueDiscountU = null;
    public $discount_value_stationsU = null;
    public $number_stations_discountU = null;
    public $typeU = null;
    public $stateDEU = null;
    public $stateDDU = null;
    public $stateU = null;
    public $formUpdate = false;
    public $state=1;
    public $name=null;
    protected $listeners = ['addSubscription','updateSubscription'];

    public function closeFormButton(){
        $this->formAdd = false;
        $this->formUpdate = false;
    }
 

    public function addSubscription($data){
        try {
            DB::beginTransaction();
            try {
                if(SubscriptionM::where('name',$data['name'])->doesntExist()){
                    if(SubscriptionM::where('tag',$data['tag'])->doesntExist()){
                    
                        $subscription=SubscriptionM::create([
                            'name' => $data['name'],
                            'tag' => $data['tag'],
                            'price' => $data['price'],
                            'amount' => $data['amount'],
                            'duration' => $data['duration'],
                            'duration_discount' => $data['duration_discount'],
                            'duration_value_discount' => $data['duration_value_discount'],
                            'discount_value_stations' => $data['discount_value_stations'],                        
                            'number_stations_discount' => $data['number_stations_discount'],                    
                            'state_de' => $data['stateDE'],
                            'state_dd' => $data['stateDD'],
                            'state' => $data['state'],                      
                        ]);
                        
                        DB::commit();
                        $this->formAdd = false;
                        $this->formUpdate = false;
                        session()->flash('message', 'Registro existoso!');
                    }else{
                        session()->flash('failed', 'Ya existe otra Suscripción con esta etiqueta');
                    }
                }else{
                    session()->flash('failed', 'Ya existe otra Suscripción con este título');
                }

            } catch (\Throwable $th) {
                DB::rollBack();                
                session()->flash('error', 'No se pudo realizar la actualización. Intente mas tarde.');
                //session()->flash('error', $th->getMessage());
                

            }
        } catch (\Throwable $th) {
            //throw $th;
        }  
    }

    public function updateSubscription($data){
        //dd($data);
        try {
            DB::beginTransaction();
            try {
                if(SubscriptionM::where('name',$data['nameU'])->where('id','!=',$this->idU)->doesntExist()){
                    if(SubscriptionM::where('tag',$data['tagU'])->where('id','!=',$this->idU)->doesntExist()){

                        $subscription=SubscriptionM::where('id',$this->idU)
                        ->update([
                            'name' => $data['nameU'],
                            'tag' => $data['tagU'],
                            'price' => $data['priceU'],
                            'amount' => $data['amountU'],
                            'duration' => $data['durationU'],
                            'duration_discount' => $data['duration_discountU'],
                            'duration_value_discount' => $data['duration_value_discountU'],                        
                            'discount_value_stations' => $data['discount_value_stationsU'],                        
                            'number_stations_discount' => $data['number_stations_discountU'],                    
                            'state_de' => $data['stateDEU'],
                            'state_dd' => $data['stateDDU'],
                            'state' => $data['stateU'],
                        ]);

                        
                        DB::commit();
                        $this->formAdd = false;
                        $this->formUpdate = false;
                        session()->flash('message', 'Actualización existosa!');
                    }else{
                        session()->flash('failed', 'Ya existe otra Suscripción con esta etiqueta');
                    }
                }else{
                    session()->flash('failed', 'Ya existe otra Suscripción con este título');
                }

            } catch (\Throwable $th) {
                DB::rollBack();
                session()->flash('error', ErrorMessage::forUser($th, 'No se pudo realizar la actualización. Intente más tarde.'));
                

            }
        } catch (\Throwable $th) {
            //throw $th;
        }  
    }

    public function visibleAdd(){
        $this->closedUpdate();
        $this->formAdd = !$this->formAdd;        
         
    }

    public function visibleUpdate($id){
        $this->subscriptionsIds=[];
        $this->subscriptionDetails=[];
        

        $this->idU = $id;
        $this->formAdd = false;
        $this->formUpdate = true; 
        $data = SubscriptionM::find($id);
        $this->idU = $id;
        $this->nameU = $data->name;
        $this->tagU = $data->tag;
        $this->priceU = $data->price;
        $this->durationU = $data->duration;
        $this->durationDiscountU = $data->duration_discount;
        $this->durationValueDiscountU = $data->duration_value_discount;
        $this->amountU = $data->amount;        
        $this->discount_value_stationsU = $data->discount_value_stations;
        $this->number_stations_discountU = $data->number_stations_discount;
        $this->typeU = $data->type;
        $this->stateDEU = $data->state_de;
        $this->stateDDU = $data->state_dd;
        $this->stateU = $data->state;
        $dataDetails = DetailsSubscriptionM::where('subscription_id',$id)->get();
        foreach($dataDetails as $item){
            $this->subscriptionsIds[]=$item->id;
            $this->subscriptionDetails[]=[$item->id,$item->description];
        }
        //dd($this->subscriptionDetails);
    }

    public function closedUpdate(){
        $this->idU = null;        
        $this->formUpdate = false;         
        $this->idU = null; 
        $this->nameU = null;
        $this->priceU = null;
        $this->amountU = null;
        $this->durationU = null;
        $this->durationDiscountU = null;
        $this->durationValueDiscountU = null;
        $this->discount_value_stationsU = null;
        $this->number_stations_discountU = null;
        $this->typeU = null;
        $this->stateDEU = null; 
        $this->stateDDU = null; 
        $this->stateU = null; 
    }

    public function deleteSubsEvent($id=null){        
        DB::beginTransaction();
        try {
            if(UsersSubscriptionM::where('subscription_id',$id)->doesntExist()){
                DetailsSubscriptionM::where('subscription_id',$id)->delete();
                SubscriptionM::where('id',$id)->delete();
                DB::commit();
                session()->flash('message', 'Eliminación exitosa!');
            }else{
                session()->flash('failed', 'No se pude Eliminar, esta suscripción ya esta siendo usada por los suscriptores');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            session()->flash('failed', 'No se pudo Eliminar. Intente mas tarde');
        }
    }

    public function changeStatus($id,$estadoActual){
        DB::beginTransaction();
        try {
            SubscriptionM::where('id','=',$id)
                ->update([
                    'state'=> $estadoActual==1?0:1,
                ]);
            DB::commit();
            //session()->flash('message', 'Se actualizo el estado!');
            //$this->dispatch('ocultar-alert');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('failed', 'No se pudo cambiar el estado. Intente mas tarde.');            
        }                    
    }


    private function resultData()
    {
        if(strtolower($this->stateChange)=='all'){
            $data = SubscriptionM::with('detailsSubscription')                
            ->orderBy($this->sortColumn,$this->sortDirection)
            ->paginate($this->perPage);           
        } else{
            $data = SubscriptionM::with('detailsSubscription')
            ->where('subscriptions.state','=',$this->stateChange)            
            ->orderBy($this->sortColumn,$this->sortDirection)
            ->paginate($this->perPage);             
        }
        return $data;
    }
    
    public function render()
    {
        return view('livewire.subscriptions.subscriptions',['data' => $this->resultData()]);
    }
}

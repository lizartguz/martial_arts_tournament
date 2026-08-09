<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SubscriptionM;
use App\Models\UserDependencyM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

//
//class InsertUsersDataImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithStartRow
class InsertUsersDataImport implements /*ToModel*/ ToCollection{
    /**
    * @param Collection $collection
    */
    use Importable;
    
    public $message = '';

    public function collection(Collection $rows) {
        //dd($rows);
        $this->setData($rows);
    }
    
    public function setData(Collection $rows){
        date_default_timezone_set('America/La_Paz');
        try{
            for ($i=1; $i <count($rows) ; $i++) {
                try{   
                    DB::beginTransaction();
                    $name = $rows[$i][1];
                    $lastname = $rows[$i][2];
                    $username = trim($name).trim($lastname);
                    $email = $rows[$i][2];
                    $emailOwner = $rows[$i][2];
                    $number_phone = $rows[$i][2];
                    $password = $rows[$i][2];
                    $user_type = $rows[$i][2];
                    $expire = $rows[$i][3];
                    $occupation = $rows[$i][4];
                    $access_type = 1;
                    $user_dependency_id = null;
                    $payment_state = 1;
                    $own_state = 1;
                    $state = 1;
                    $typeSubscriptionTag = 'corporate';          
                    try {
                        $date_expiry = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($expire)->format('Y-m-d');
                        $fechaFinal = Carbon::parse($date_expiry)->subMonth();
                        $dateBeforeOneMonth = $fechaFinal->toDateString();

                        $user = User::create([
                            'username' => $username,
                            'name' => $name,
                            'lastname' => $lastname,
                            'occupation' => $occupation,
                            'email' => $email,
                            'number_phone' => $number_phone,
                            'password' => Hash::make($password),
                            'image'=>'user_perfil.jpg',
                            'access_type' => $user_type,
                            'user_type' => $user_type,
                            'user_dependency_id' =>$user_dependency_id,
                            'own_state' => $own_state,
                            'payment_state'=> $payment_state,
                            'state' => $state,
                        ]);
                        $user->assignRole('subscriber');
                        /*
                        $subsReg = UsersSubscriptionM::create([
                            'business_name'=> null,
                            'nit'=> null,               
                            'reg_date' => Carbon::today()->format('Y-m-d') ,
                            'accumulation_date' => Carbon::today()->format('Y-m-d'),
                            'start_date' => Carbon::today()->format('Y-m-d'),
                            'expiration_notice' => $dateBeforeOneMonth,
                            'date_expiry' => $date_expiry,
                            'is_discount'  => 0,
                            'amount'=> 1,
                            'cost' => 1324.3,
                            'latitude' => '-17.782879',
                            'longitude' => '-63.181668',
                            'invitation_reference_data' => null,
                            'temporary_extension_start' => null,
                            'temporary_extension_end' => null,
                            'incident_extension' => null,
                            'data_save' => null,
                            'is_it_paid_renewal'=> 1,
                            'payment_type_renewal'=> 1,                    
                            'station_id' => null,
                            'state' => 1,
                            'user_id' => $user->id,
                            'subscription_id' => SubscriptionM::where('tag', $typeSubscriptionTag)->value('id'),                
                        ]);                        

                        UsersSubscriptionM::where('id',$subsReg->id)
                        ->update([
                            'data_save' => $subsReg,
                        ]);
                        */

                        UserDependencyM::create([
                            'dependent_user_id' => $user->id,
                            'user_subscription_id' => $subsReg->id,
                        ]);
                        DB::commit();                    
                        //$this->reset();
                       
                    } catch (\Throwable $e) {
                        DB::rollBack();                      
                        dd($e);                        
                    }  
                        
                    
                }catch(\Exception $e){
                    DB::rollback();
                    //return back()->with('error', $e->getMessage());
                    return back()->with('error', $i.'-'.$e->getMessage());
                    //return back()->with('error', $i."-".$this->nombres." ".$this->apellido_paterno);
                }
            }
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    
    public function chunkSize(): int{
        // TODO: Implement chunkSize() method.
        return 100;
    }

    public function startRow(): int{
        // TODO: Implement startRow() method.
        return 100;
    }

    
}

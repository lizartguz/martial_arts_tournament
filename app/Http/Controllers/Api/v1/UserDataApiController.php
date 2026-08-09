<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\StationM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DateTime;

class UserDataApiController extends Controller
{
    public function getDataUser(Request $request){
        $userItem = null;
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){  
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();

                $userItem = User::Where('id',$request->userId)
                ->select('users.id','users.name','users.lastname','users.email','users.image','users.number_phone')->first(); 
                
                return response()->json([
                    "response" => true, 
                    "data" => $userItem ,
                    "failed" => 'no', 
                    "data_user" => $userPermission[3],                 
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false, 
                    "data" => null,  
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],  
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                 
                ]); 
            }                     
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false, 
                "data" => null, 
                "failed"=> $th->getMessage(),
                "data_user" => null, 
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }
    } 

    public function updateDataUser(Request $request){
        $userItem = null;
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $user = User::where('id',$request->userId)->first();

                if(!$user){
                    throw new \Exception('Usuario no encontrado');
                }

                $updateData = [
                    'name' => $request->nameNew ?? $user->name,
                    'lastname' => $request->lastnameNew ?? $user->lastname,
                    'email' => $request->emailNew ?? $user->email,
                    'number_phone' => $request->phoneNew ?? $user->number_phone,
                ];

                if($request->filled('imageNew')){
                    $newImagePath = $this->storeUserImage($request->imageNew, $user->id);
                    if($newImagePath !== null){
                        if(!empty($user->image) && $user->image !== 'user_perfil.jpg' && Storage::disk('public')->exists($user->image)){
                            Storage::disk('public')->delete($user->image);
                        }
                        $updateData['image'] = $newImagePath;
                    }
                }

                $user->update($updateData);
                $this->syncMobileFcmToken($request, (int) $request->userId);

                $userItem = User::Where('id',$request->userId)
                ->select('users.id','users.name','users.lastname','users.email','users.image','users.number_phone')->first();

                DB::commit();
                 
                return response()->json([
                    "response" => true, 
                    "data" => $userItem ,
                    "failed" => 'no',    
                    "data_user" => $userPermission[3],              
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false, 
                    "data" => null,  
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],  
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],             
                ]); 
            }                     
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false, 
                "data" => null, 
                "failed"=> $th->getMessage(),
                "data_user" => null, 
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
            ]);
        }
    } 

    private function storeUserImage(string $rawImage, int $userId): ?string
    {
        try {
            $imageData = $rawImage;
            $extension = 'jpg';

            if(Str::contains($rawImage, 'base64,')){
                [$meta, $content] = explode('base64,', $rawImage, 2);
                $imageData = $content;
                if(Str::contains($meta, 'image/')){
                    $metaParts = explode('image/', $meta);
                    if(isset($metaParts[1])){
                        $extensionCandidate = explode(';', $metaParts[1])[0];
                        if(!empty($extensionCandidate)){
                            $extension = $extensionCandidate;
                        }
                    }
                }
            }

            $binary = base64_decode($imageData);
            if($binary === false){
                throw new \Exception('No se pudo decodificar la imagen subida.');
            }

            $extension = strtolower($extension);
            if(!in_array($extension, ['jpg','jpeg','png','webp'])){
                $extension = 'jpg';
            }

            $fileName = 'user_'.$userId.'_'.time().'.'.$extension;
            $storagePath = 'users/'.$fileName;

            Storage::disk('public')->put($storagePath, $binary);

            return $storagePath;
        } catch (\Throwable $th) {
            throw new \Exception('No fue posible guardar la imagen: '.$th->getMessage());
        }
    }
}

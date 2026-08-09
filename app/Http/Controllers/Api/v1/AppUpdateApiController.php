<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\AppUpdateM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class AppUpdateApiController extends Controller
{
    public function verifAppUpdate(Request $request){
        try {
            DB::beginTransaction();

            $appUpdate = AppUpdateM::first();
            DB::commit();

            return response()->json([
                'update_message_android'=> $appUpdate->update_message_android,
                'update_message_ios'=> $appUpdate->update_message_ios,
                'version_android'=> $appUpdate->version_android,
                'version_ios'=> $appUpdate->version_ios,
                'block_android'=> $appUpdate->block_android,
                'block_ios'=> $appUpdate->block_ios,
                'error'=> 'no',
                'details_error'=> '',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([                
                'update_message_android'=> '',
                'update_message_ios'=> '',
                'version_android'=> null,
                'version_ios'=> null,
                'block_android'=> '',
                'block_ios'=> '',
                'error'=> 'yes',
                'details_error'=> $th->getMessage(),
            ]);
        }
    }
}

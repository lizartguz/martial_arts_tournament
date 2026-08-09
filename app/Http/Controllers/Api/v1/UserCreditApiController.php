<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserCreditApiController extends Controller
{
    public function getUserCredits(Request $request)
    {
        try {
            DB::beginTransaction();

            $userPermission = UserPermissionVerif::verif(
                $request->userId,
                $request->email,
                $request->phone,
                $request->identifier
            );

            if ($userPermission[0] === 'yes') {
                $user = User::where('id', $request->userId)->first();
                if ($user && $request->filled('fcmToken') && $request->fcmToken !== '') {
                    $this->syncMobileFcmToken($request, (int) $request->userId);
                }

                UserCredit::firstOrCreate(
                    [
                        'user_id' => $request->userId,
                        'type' => 'forecast',
                    ],
                    [
                        'total_credit' => 5,
                        'used_credit' => 0,
                        'state' => 1,
                    ]
                );

                $query = UserCredit::where('user_id', $request->userId);

                if ($request->filled('type')) {
                    $query->where('type', $request->type);
                } else {
                    $query->where('type', 'forecast');
                }

                if ($request->filled('state')) {
                    $query->where('state', (int) $request->state);
                }

                $credit = $query
                    ->orderBy('id', 'desc')
                    ->first();

                DB::commit();

                return response()->json([
                    'response' => true,
                    'data' => $credit,
                    'failed' => 'no',
                    'data_user' => $userPermission[3],
                    'user_permission' => $userPermission[0],
                    'permit_detail' => $userPermission[1],
                    'type_subscriptions' => $userPermission[2],
                ]);
            }

            DB::commit();

            return response()->json([
                'response' => false,
                'data' => null,
                'failed' => 'no',
                'data_user' => $userPermission[3],
                'user_permission' => $userPermission[0],
                'permit_detail' => $userPermission[1],
                'type_subscriptions' => $userPermission[2],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'response' => false,
                'data' => null,
                'failed' => $th->getMessage(),
                'data_user' => null,
                'user_permission' => 'no',
                'permit_detail' => '',
                'type_subscriptions' => null,
            ]);
        }
    }

    public function consumeUserCredits(Request $request)
    {
        try {
            DB::beginTransaction();

            $userPermission = UserPermissionVerif::verif(
                $request->userId,
                $request->email,
                $request->phone,
                $request->identifier
            );

            if ($userPermission[0] === 'yes') {
                $user = User::where('id', $request->userId)->first();
                if ($user && $request->filled('fcmToken') && $request->fcmToken !== '') {
                    $this->syncMobileFcmToken($request, (int) $request->userId);
                }

                $type = $request->input('type', 'forecast');

                $credit = UserCredit::firstOrCreate(
                    [
                        'user_id' => $request->userId,
                        'type' => $type,
                    ],
                    [
                        'total_credit' => 5,
                        'used_credit' => 0,
                        'state' => 1,
                    ]
                );

                $creditsToConsume = 1;

                $newUsedCredit = $credit->used_credit + $creditsToConsume;

                if ($newUsedCredit > $credit->total_credit) {
                    DB::rollBack();

                    return response()->json([
                        'response' => false,
                        'data' => [
                            'available_credit' => $credit->total_credit - $credit->used_credit,
                            'total_credit' => $credit->total_credit,
                            'used_credit' => $credit->used_credit,
                        ],
                        'failed' => 'no',
                        'message' => 'No hay créditos suficientes disponibles.',
                        'data_user' => $userPermission[3],
                        'user_permission' => $userPermission[0],
                        'permit_detail' => $userPermission[1],
                        'type_subscriptions' => $userPermission[2],
                    ]);
                }

                $credit->used_credit = $newUsedCredit;
                $credit->save();

                DB::commit();

                return response()->json([
                    'response' => true,
                    'data' => $credit,
                    'failed' => 'no',
                    'message' => 'Crédito consumido correctamente.',
                    'data_user' => $userPermission[3],
                    'user_permission' => $userPermission[0],
                    'permit_detail' => $userPermission[1],
                    'type_subscriptions' => $userPermission[2],
                ]);
            }

            DB::commit();

            return response()->json([
                'response' => false,
                'data' => null,
                'failed' => 'no',
                'data_user' => $userPermission[3],
                'user_permission' => $userPermission[0],
                'permit_detail' => $userPermission[1],
                'type_subscriptions' => $userPermission[2],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'response' => false,
                'data' => null,
                'failed' => $th->getMessage(),
                'data_user' => null,
                'user_permission' => 'no',
                'permit_detail' => '',
                'type_subscriptions' => null,
            ]);
        }
    }

}



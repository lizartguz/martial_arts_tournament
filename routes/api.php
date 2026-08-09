<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthApiController;
use App\Http\Controllers\Api\v1\StationApiController;
use App\Http\Controllers\Api\v1\ChangePasswordApiController;
use App\Http\Controllers\Api\v1\AppUpdateApiController;
use App\Http\Controllers\Api\v1\ForecastApiController;
use App\Http\Controllers\Api\v1\AlertAlarmApiController;
use App\Http\Controllers\Api\v1\ReportApiController;
use App\Http\Controllers\Api\v1\CropAlertApiController;
use App\Http\Controllers\Api\v1\NotificationApiPushC;
use App\Http\Controllers\Api\v1\NotificationApiController;
use App\Http\Controllers\Api\v1\UserDataApiController;
use App\Http\Controllers\Api\v1\ApplicationTechnologyController;
use App\Http\Controllers\Api\v1\AlertApiController;
use App\Http\Controllers\Api\v1\AppVersionApiController;
use App\Http\Controllers\Api\v1\PasswordResetApiController;
use App\Http\Controllers\Api\v1\AccountDeletionApiController;
use App\Http\Controllers\Api\v1\NetworkCredentialApiController;
use App\Http\Controllers\Api\v1\WifiApiController;
use App\Http\Controllers\Api\v1\UserCreditApiController;
use App\Http\Controllers\Api\v1\StationNodeApiController;
use App\Http\Controllers\Api\v1\HistoricalDataApiController;
use App\Http\Controllers\Api\v1\AutomationApiController;
use App\Http\Controllers\DataCaptureC;
use App\Http\Controllers\NetworkCredentialsC;
use App\Http\Controllers\NotificationImageController;
/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/login', [AuthApiController::class, 'loginApi']);
Route::post('/v1/getStation', [StationApiController::class, 'getStation']);
Route::post('/v1/getStationMap', [StationApiController::class, 'getStationMap']);
Route::post('/v1/getHomeStation', [StationApiController::class, 'getHomeStation']);
Route::post('/v1/getFavoriteStation', [StationApiController::class, 'getFavoriteStation']);
Route::post('/v1/getOnlyFavoriteStation', [StationApiController::class, 'getOnlyFavoriteStation']);
Route::post('/v1/addFavoriteStation', [StationApiController::class, 'addFavoriteStation']);
Route::post('/v1/getConcatenationName', [StationApiController::class, 'getConcatenationName']);
Route::post('/v1/removeStation', [StationApiController::class, 'removeStation']);
Route::post('/v1/getStationHistoricalData', [HistoricalDataApiController::class, 'getStationHistoricalData']);
Route::post('/v1/changePassword', [ChangePasswordApiController::class, 'changePassword']);
Route::post('/v1/verifAppUpdate', [AppUpdateApiController::class, 'verifAppUpdate']);
Route::post('/v1/getForecast', [ForecastApiController::class, 'getForecast']);
Route::post('/v1/getForecastApi', [ForecastApiController::class, 'getForecastApi']);
Route::post('/v1/getReport', [ReportApiController::class, 'getReport']);
Route::post('/v1/getReportFilter', [ReportApiController::class, 'getReportWithFilter']);
Route::post('/v1/getAlertAlarm', [AlertAlarmApiController::class, 'getAlertAlarm']);
Route::post('/v1/saveAlertAlarm', [AlertAlarmApiController::class, 'saveAlertAlarm']);
Route::post('/v1/getCropAlertMap', [CropAlertApiController::class, 'getCropAlertMap']);
Route::post('/v1/getCropAlertGraphic', [CropAlertApiController::class, 'getCropAlertGraphic']);
//Route::post('/v1/sendnotifapialarmpush', [NotificationApiPushC::class,'sendnotifapialarmpush'])->name('sendnotifapialarmpush');
//Route::get('/v1/sendPush', [NotificationApiPushC::class,'sendPush'])->name('sendPush');
Route::post('/v1/getDataUser', [UserDataApiController::class,'getDataUser'])->name('getDataUser');
Route::post('/v1/updateDataUser', [UserDataApiController::class,'updateDataUser'])->name('updateDataUser');
Route::post('/v1/getVersionApp', [AppVersionApiController::class,'getAppVersion'])->name('getAppVersion');
Route::post('/v1/getTAgraphic', [ApplicationTechnologyController::class,'getTAgraphic'])->name('getTAgraphic');
Route::post('/v1/getAlert', [AlertApiController::class,'getAlert'])->name('getAlert');
Route::post('/v1/forgot-password', [PasswordResetApiController::class, 'sendResetLinkEmail']);
Route::post('/v1/account-deletion', [AccountDeletionApiController::class, 'setAccountDeletion']);
Route::post('/v1/getStationsForWifi', [WifiApiController::class, 'getStationsForWifi']);
Route::post('/v1/saveNetworkCredential', [NetworkCredentialApiController::class, 'saveNetworkCredential']);
Route::post('/v1/getNetworkCredential', [NetworkCredentialApiController::class, 'getNetworkCredential']);
Route::post('/v1/getNetworkCredentialHistory', [NetworkCredentialApiController::class, 'getNetworkCredentialHistory']);
Route::post('/v1/getUserCredits', [UserCreditApiController::class, 'getUserCredits']);
Route::post('/v1/consumeUserCredits', [UserCreditApiController::class, 'consumeUserCredits']);
Route::post('/v1/getNotifications', [NotificationApiController::class, 'getNotifications']);
Route::post('/v1/markNotificationAsRead', [NotificationApiController::class, 'markNotificationAsRead']);
Route::get('/v1/notifications/{notification}/images/{slot}/{user}', [NotificationImageController::class, 'showApi'])
    ->whereIn('slot', ['image', 'image_1', 'image_2'])
    ->middleware(['signed', 'throttle:60,1'])
    ->name('api.notifications.images.show');
Route::post('/v1/automations/execute', [AutomationApiController::class, 'execute']);

// ============================================================
// Rutas exclusivas para firmware de nodos (vía API v1)
// Centralizado en StationNodeApiController
// ============================================================
Route::get('/v1/node/get-network',   [StationNodeApiController::class, 'getNetwork']);
Route::get('/v1/node/station-data', [StationNodeApiController::class, 'receiveStationData']);

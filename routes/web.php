<?php

use App\Http\Controllers\TryC;
use App\Http\Controllers\StationC;
use App\Http\Controllers\ForecastC;
use App\Http\Controllers\PublicStationFeedC;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DailyTotalC;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataManagerC;
use App\Http\Controllers\DownloadAppC;
use App\Http\Controllers\PayAndRenewC;
use App\Http\Controllers\OrganizeUserC;
use App\Http\Controllers\CreateAccountC;
use App\Http\Controllers\ForecastsEditC;
use App\Http\Controllers\SubscriptionsC;
use App\Http\Controllers\ChangeMeasuresC;
use App\Http\Controllers\UserManagementC;
use App\Http\Controllers\GraphDataStationC;
use App\Http\Controllers\ImportDataGlobalC;
use App\Http\Controllers\NotificationPushC;
use App\Http\Controllers\NotificationImageController;
use App\Http\Controllers\NotificationViewController;
use App\Http\Controllers\FirebaseWebPushController;
use App\Http\Controllers\UsersSubscriptionsC;
use App\Http\Controllers\DownloadDataStationC;
use App\Http\Controllers\NotificationAlertAlarmC;
use App\Http\Controllers\OutOfServiceStationNotifierC;
use App\Http\Controllers\FundecorDashboardC;
use App\Http\Controllers\AdminDashboardC;
use App\Http\Controllers\AuthorizationC;
use App\Http\Controllers\CatalogC;
use App\Http\Controllers\LogManagerC;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


Route::get('/alertn', [NotificationAlertAlarmC::class,'runAlert'])->name('alertn');
Route::get('/alarmn', [NotificationAlertAlarmC::class,'runAlarm'])->name('alarmn');

Route::get('/verifIconoFecha', [ForecastC::class,'verifIconoFecha'])->name('verifIconoFecha');
Route::get('/setDailyTotal', [DailyTotalC::class,'setDailyTotal'])->name('setDailyTotal');

Route::get('/forecastpc', [ForecastC::class,'getForecastCurl'])->name('forecastpc.getForecastCurl');
Route::get('/newGetForecastCurl', [ForecastC::class,'newGetForecastCurl'])->name('newGetForecastCurl');
Route::get('/firebase-messaging-sw.js', [FirebaseWebPushController::class, 'serviceWorker'])->name('firebase.web-push.sw');


//-------------------------------------------- DASHBOARD ---------------------------------------------

Route::get('/683f1d18-2cdc-30b8-8c47-e96904d87fe6_s64r40J1l5-6I5k3-fd433fg6x', [FundecorDashboardC::class, 'viewDashboard'])->name('viewDashboard');
Route::get('/tv{codigo}', [FundecorDashboardC::class, 'begin']);
//----------------------------------------------------------------------------------------------------


Route::get('/', function () {
    Log::channel('stack')->info('Page visited: landing.home', [
        'route' => '/',
        'method' => request()->method(),
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'referer' => request()->headers->get('referer'),
    ]);

    return view('landing.home');
})->name('landing.home');

// Cambio de idioma: setea la cookie que lee LocaleMiddleware y vuelve a la página anterior.
// Sirve para páginas sin Livewire (landing) y persiste la elección por 1 año.
Route::get('/idioma/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en', 'pt', 'fr', 'de', 'ru'], true)) {
        session(['locale' => $locale]);
        \Illuminate\Support\Facades\Cookie::queue('locale', $locale, 525600);
    }
    return redirect()->back(fallback: route('landing.home'));
})->name('locale.switch');
Route::get('/conoce', function () { return view('landing.knows'); })->name('landing.knows');
Route::get('/consultoria', function () { return view('landing.consulting'); })->name('landing.consulting');
Route::get('/contacto', function () { return view('landing.contact'); })->name('landing.contact');
Route::get('/quienessomos', function () { return view('landing.about-us'); })->name('landing.about-us');
Route::get('/register', function () {
    Log::channel('stack')->info('Auth page visited: register', [
        'route' => '/register',
        'method' => request()->method(),
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'referer' => request()->headers->get('referer'),
    ]);

    return response()->view('auth.register', status: 403);
})->name('register');
Route::post('/register', function () {
    Log::channel('stack')->warning('Blocked register attempt', [
        'route' => '/register',
        'method' => request()->method(),
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'referer' => request()->headers->get('referer'),
    ]);

    abort(404);
});
Route::get('/servicios', function () { return view('landing.services'); })->name('landing.services');
Route::get('/academia', function () { return view('landing.academy'); })->name('landing.academy');
Route::get('/create-pay-account', [CreateAccountC::class,'index'])->name('createpayaccount');


Route::get('/create-pay-account-os', [CreateAccountC::class,'indexIOS'])->name('createpayaccountos');
Route::match(['get', 'post'], '/outofservice/webhook', [OutOfServiceStationNotifierC::class, 'webhook'])->name('outofservice.webhook');
Route::get('/8d4f7b6c-2026-public-station-feed-uid01', [PublicStationFeedC::class, 'index'])->name('public.station.feed');
Route::post('/8d4f7b6c-2026-public-station-feed-uid01/delete-records', [PublicStationFeedC::class, 'destroy'])
    ->middleware('throttle:3,1')
    ->name('public.station.feed.destroy');



Route::middleware(['auth:sanctum',config('jetstream.auth_session'),
    'verified',
])->group(function () {
    /* No sctructure */
    Route::get('/showLoadDataQuiar', [DataManagerC::class, 'showLoadDataQuiar'])->name('showLoadDataQuiar');
    Route::post('/setDataQuair', [DataManagerC::class, 'setDataQuair'])->name('setDataQuair');    
    Route::get('/importdataglobal', [ImportDataGlobalC::class,'index'])->middleware('can:data.import_global.view')->name('importDataGlobalC');
    Route::post('/importdataglobalform', [ImportDataGlobalC::class,'importUserData'])->middleware('can:data.import_global.view')->name('import.user.data');
    Route::post('/orderdataglobalform', [ImportDataGlobalC::class,'orderUserData'])->middleware('can:data.import_global.view')->name('import.user.order.data');
    Route::get('/organizeuser', [OrganizeUserC::class,'index'])->middleware('can:data.organize_users.view')->name('organizeuserC');
    
    //Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/dashboard', [AdminDashboardC::class,'index'])->name('admin-dashboard');
    Route::get('/forecasts', [ForecastC::class,'index'])->name('forecasts');
    Route::get('/getForecastCurl', [ForecastC::class,'getForecastCurl'])->name('getForecastCurl');
    Route::get('/preview/password-reset-senvatec', function () {
        $user = Auth::user();
        if ($user->hasRole('subscriber')) {
            abort(403, 'No autorizado.');
        }

        return view('emails.password-reset-senvatec', [
            'resetUrl' => url('/reset-password/demo-token?email=' . urlencode($user->email ?? 'usuario@senvatec.com')),
            'userName' => trim(($user->name ?? '') . ' ' . ($user->lastname ?? '')) ?: 'Usuario Senvatec',
            'supportEmail' => config('mail.from.address'),
        ]);
    })->name('preview.password-reset-senvatec');
    Route::get('/notificationsp', [NotificationPushC::class,'index'])->middleware('can:notifications.view')->name('notificationsp');
    Route::get('/notifications/preview', [NotificationViewController::class, 'preview'])
        ->middleware('can:notifications.view')
        ->name('notifications.preview');
    Route::get('/notifications/{notification}', [NotificationViewController::class, 'show'])
        ->name('notifications.show');
    Route::get('/notifications/{notification}/images/{slot}', [NotificationImageController::class, 'show'])
        ->whereIn('slot', ['image', 'image_1', 'image_2'])
        ->name('notifications.images.show');
    Route::get('/sendNotification', [NotificationPushC::class,'sendNotification'])->middleware('can:notifications.view')->name('sendNotification');
    Route::post('/firebase/web-push/token/status', [FirebaseWebPushController::class, 'tokenStatus'])
        ->name('firebase.web-push.token.status');
    Route::post('/firebase/web-push/token', [FirebaseWebPushController::class, 'storeToken'])->name('firebase.web-push.token.store');
    Route::delete('/firebase/web-push/token', [FirebaseWebPushController::class, 'destroyToken'])->name('firebase.web-push.token.destroy');
    Route::get('/subscription', [SubscriptionsC::class,'index'])->middleware('can:subscriptions.view')->name('subscription');
    Route::get('/userSubscription', [UsersSubscriptionsC::class,'index'])->middleware('can:sales_subscribers.view')->name('userSubscription');
    Route::get('/userManagement', [userManagementC::class,'index'])->middleware('can:user_management.view')->name('userManagement');
    Route::get('/reportItemUSC', [UsersSubscriptionsC::class,'reportItem'])->middleware('can:sales_subscribers.view')->name('reportItem');
    Route::get('/station', [StationC::class,'index'])->middleware('can:stations.table.view')->name('station');
    Route::get('/station-map', [StationC::class,'indexMapOnly'])->middleware('can:stations.map.view')->name('stationMapOnly');
    Route::get('/reportMaintenanceIndividual', [StationC::class,'reportMaintenanceIndividual'])->middleware('can:stations.table.view')->name('reportMaintenanceIndividual');
    Route::get('/reportMaintenanceFilter', [StationC::class,'reportMaintenanceFilter'])->middleware('can:stations.table.view')->name('reportMaintenanceFilter');
    Route::get('/reportStation', [StationC::class,'reportStation'])->middleware('can:stations.table.view')->name('reportStation');
    Route::get('/resetdata', [DataManagerC::class,'resetdata'])->name('resetdata');
    Route::get('/setmetric', [ChangeMeasuresC::class, 'setMetric'])->name('measures.metric');
    Route::get('/setimperial', [ChangeMeasuresC::class, 'setImperial'])->name('measures.imperial');

    Route::get('/downloads', [DownloadDataStationC::class,'index'])->middleware('can:stations.downloads.view')->name('downloadDataStation');
    Route::get('/graphs', [GraphDataStationC::class,'index'])->middleware('can:stations.charts.view')->name('graphDataStation');
    Route::get('/early-warning', function () {return view('earlyWarning.index');})->middleware('can:agro.early_warning.view')->name('earlyWarning');
    Route::get('/gdd', function () {return view('gdd.index');})->middleware('can:agro.gdd.view')->name('gdd');
    Route::get('/chill-hours', function () {return view('chillHours.index');})->middleware('can:agro.chill_hours.view')->name('chillHours');
    Route::get('/logs', [LogManagerC::class, 'index'])->middleware('can:administration.logs.view')->name('logs.index');
    Route::get('/logs/download', [LogManagerC::class, 'download'])->middleware('can:administration.logs.view')->name('logs.download');
    Route::get('/authorization', [AuthorizationC::class, 'index'])->middleware('permission:authorization.access')->name('authorization.index');
    Route::get('/catalog', [CatalogC::class, 'index'])->middleware('can:administration.catalog.view')->name('catalog.index');
    Route::get('/forecasts-edit', [ForecastsEditC::class, 'index'])->middleware('can:administration.forecasts_edit.view')->name('forecastsEdit');

    Route::get('/pay-renew-c', [PayAndRenewC::class, 'index'])->middleware('can:pay_renewals.view')->name('payAndRenewC');
});

Route::get('/downloadps', [DownloadAppC::class,'downloadAndroid'])->name('downloadAndroid');
Route::get('/downloadas', [DownloadAppC::class,'downloadIOS'])->name('downloadIOS');

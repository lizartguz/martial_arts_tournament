<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventMediaController;
use App\Http\Controllers\Admin\FighterController;
use App\Http\Controllers\Admin\FighterMediaController;
use App\Http\Controllers\Admin\FighterTeamController;
use App\Http\Controllers\Admin\FightController;
use App\Http\Controllers\Admin\FightResultController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PurchaseRequestController;
use App\Http\Controllers\Admin\RankingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\SubscriptionPaymentController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TicketLinkController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserSubscriptionController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\WeightClassController;
use App\Http\Controllers\AuthorizationC;
use App\Http\Controllers\FirebaseWebPushController;
use App\Http\Controllers\NotificationImageController;
use App\Http\Controllers\NotificationPushC;
use App\Http\Controllers\NotificationViewController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Subscriber\PortalController as SubscriberPortalController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing.home');

/**
 * Resuelve la respuesta de esta ruta GET.
 */
Route::get('/idioma/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en', 'pt', 'fr', 'de', 'ru'], true)) {
        session(['locale' => $locale]);
        Cookie::queue('locale', $locale, 525600);
    }

    return redirect()->back(fallback: route('landing.home'));
})->name('locale.switch');

Route::get('/eventos/{event:slug}', [LandingController::class, 'event'])->name('landing.events.show');
Route::get('/peleadores', [LandingController::class, 'fighters'])->name('landing.fighters.index');
Route::get('/peleadores/{fighter:slug}', [LandingController::class, 'fighter'])->name('landing.fighters.show');
Route::get('/noticias', [LandingController::class, 'news'])->name('landing.news.index');
Route::get('/noticias/{newsPost:slug}', [LandingController::class, 'newsShow'])->name('landing.news.show');
Route::get('/suscripcion', [LandingController::class, 'subscription'])->name('landing.subscription');
Route::get('/contacto', [LandingController::class, 'contact'])->name('landing.contact');
Route::post('/contacto', [LandingController::class, 'submitContact'])
    ->middleware('throttle:10,1')
    ->name('landing.contact.submit');
Route::get('/firebase-messaging-sw.js', [FirebaseWebPushController::class, 'serviceWorker'])->name('firebase.web-push.sw');

Route::get('/register', fn () => response()->view('auth.register', status: 403))->name('register');
Route::post('/register', fn () => abort(404));

/**
 * Agrupa rutas que comparten middleware de acceso.
 */
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    /**
     * Resuelve la respuesta de esta ruta GET.
     */
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user?->can('dashboard.view')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->can('subscriber.dashboard.view')) {
            return redirect()->route('subscriber.dashboard');
        }

        abort(403);
    })->name('dashboard');

    Route::get('/notifications/{notification}', [NotificationViewController::class, 'show'])
        ->name('notifications.show');

    Route::get('/notifications/{notification}/images/{slot}', [NotificationImageController::class, 'show'])
        ->name('notifications.images.show');

    /**
     * Agrupa rutas bajo un prefijo comun.
     */
    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/dashboard', [SubscriberPortalController::class, 'dashboard'])
            ->middleware('can:subscriber.dashboard.view')
            ->name('dashboard');

        Route::get('/purchases', [SubscriberPortalController::class, 'purchases'])
            ->middleware('can:subscriber.purchases.view')
            ->name('purchases');

        Route::get('/purchases/payments/{payment}', [SubscriberPortalController::class, 'paymentDetail'])
            ->middleware('can:subscriber.purchases.view')
            ->name('purchases.payments.show');

        Route::post('/purchases/payments/{payment}/proof', [SubscriberPortalController::class, 'uploadPaymentProof'])
            ->middleware('can:subscriber.purchases.view')
            ->name('purchases.payments.proof');

        Route::get('/purchases/requests/{purchaseRequest}', [SubscriberPortalController::class, 'requestDetail'])
            ->middleware('can:subscriber.purchases.view')
            ->name('purchases.requests.show');

        Route::post('/purchases/requests/{purchaseRequest}/proof', [SubscriberPortalController::class, 'uploadRequestProof'])
            ->middleware('can:subscriber.purchases.view')
            ->name('purchases.requests.proof');

        Route::get('/events', [SubscriberPortalController::class, 'events'])
            ->middleware('can:subscriber.events.view')
            ->name('events');

        Route::get('/subscription', [SubscriberPortalController::class, 'subscription'])
            ->middleware('can:subscriber.subscription.view')
            ->name('subscription');

        Route::get('/profile', [SubscriberPortalController::class, 'profile'])
            ->middleware('can:subscriber.profile.view')
            ->name('profile');

        Route::patch('/profile', [SubscriberPortalController::class, 'updateProfile'])
            ->middleware('can:subscriber.profile.update')
            ->name('profile.update');
    });

    /**
     * Agrupa rutas bajo un prefijo comun.
     */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('can:dashboard.view')
            ->name('dashboard');

        Route::get('/events', [EventController::class, 'index'])
            ->middleware('can:events.view')
            ->name('events.index');

        Route::get('/venues', [VenueController::class, 'index'])
            ->middleware('can:venues.view')
            ->name('venues.index');

        Route::get('/fights', [FightController::class, 'index'])
            ->middleware('can:fights.view')
            ->name('fights.index');

        Route::get('/fight-results', [FightResultController::class, 'index'])
            ->middleware('can:fight_results.view')
            ->name('fight-results.index');

        Route::get('/event-media', [EventMediaController::class, 'index'])
            ->middleware('can:event_media.view')
            ->name('event-media.index');

        Route::get('/fighters', [FighterController::class, 'index'])
            ->middleware('can:fighters.view')
            ->name('fighters.index');

        Route::get('/fighter-media', [FighterMediaController::class, 'index'])
            ->middleware('can:fighter_media.view')
            ->name('fighter-media.index');

        Route::get('/fighter-teams', [FighterTeamController::class, 'index'])
            ->middleware('can:fighter_teams.view')
            ->name('fighter-teams.index');

        Route::get('/weight-classes', [WeightClassController::class, 'index'])
            ->middleware('can:weight_classes.view')
            ->name('weight-classes.index');

        Route::get('/rankings', [RankingController::class, 'index'])
            ->middleware('can:rankings.view')
            ->name('rankings.index');

        Route::get('/news', [NewsController::class, 'index'])
            ->middleware('can:news.view')
            ->name('news.index');

        Route::get('/landing', [LandingPageController::class, 'index'])
            ->middleware('can:landing.update')
            ->name('landing.index');

        Route::get('/sponsors', [SponsorController::class, 'index'])
            ->middleware('can:sponsors.view')
            ->name('sponsors.index');

        Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])
            ->middleware('can:subscription_plans.view')
            ->name('subscription-plans.index');

        Route::get('/subscribers', [SubscriberController::class, 'index'])
            ->middleware('can:subscribers.view')
            ->name('subscribers.index');

        Route::get('/user-subscriptions', [UserSubscriptionController::class, 'index'])
            ->middleware('can:user_subscriptions.view')
            ->name('user-subscriptions.index');

        Route::get('/subscription-payments', [SubscriptionPaymentController::class, 'index'])
            ->middleware('can:subscription_payments.view')
            ->name('subscription-payments.index');

        Route::get('/subscription-payments/{subscriptionPayment}/proof', [SubscriptionPaymentController::class, 'proof'])
            ->middleware('can:subscription_payments.view')
            ->name('subscription-payments.proof');

        Route::get('/purchase-requests', [PurchaseRequestController::class, 'index'])
            ->middleware('can:purchase_requests.view')
            ->name('purchase-requests.index');

        Route::get('/purchase-requests/{purchaseRequest}/proof', [PurchaseRequestController::class, 'proof'])
            ->middleware('can:purchase_requests.view')
            ->name('purchase-requests.proof');

        Route::get('/ticket-links', [TicketLinkController::class, 'index'])
            ->middleware('can:ticket_links.view')
            ->name('ticket-links.index');

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/settings', [SystemSettingController::class, 'index'])
            ->middleware('can:system_settings.view')
            ->name('settings.index');

        Route::get('/logs', [LogController::class, 'index'])
            ->middleware('can:logs.view')
            ->name('logs.index');

        Route::get('/logs/download', [LogController::class, 'download'])
            ->middleware('can:logs.download')
            ->name('logs.download');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('can:users.view')
            ->name('users.index');

        Route::get('/authorization', [AuthorizationC::class, 'index'])
            ->middleware('can:authorization.access')
            ->name('authorization.index');
    });

    Route::post('/firebase/web-push/token/status', [FirebaseWebPushController::class, 'tokenStatus'])
        ->name('firebase.web-push.token.status');
    Route::post('/firebase/web-push/token', [FirebaseWebPushController::class, 'storeToken'])
        ->name('firebase.web-push.token.store');
    Route::delete('/firebase/web-push/token', [FirebaseWebPushController::class, 'destroyToken'])
        ->name('firebase.web-push.token.destroy');

    Route::get('/admin/notifications', [NotificationPushC::class, 'index'])
        ->middleware('can:notifications.view')
        ->name('notificationsp');
});

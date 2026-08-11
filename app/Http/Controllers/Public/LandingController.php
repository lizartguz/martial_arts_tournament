<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Fight;
use App\Models\Fighter;
use App\Models\NewsPost;
use App\Models\PurchaseRequest;
use App\Models\SubscriptionPlan;
use App\Services\FileUploadService;
use App\Services\SystemSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LandingController extends Controller
{
    /**
     * Muestra una landing publica inicial con eventos publicados y peleadores destacados.
     */
    public function index(SystemSettingsService $settings)
    {
        $events = Event::query()
            ->with('venue:id,name,city_id')
            ->published()
            ->orderByDesc('is_featured')
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, starts_at, NOW()))')
            ->limit(12)
            ->get(['id', 'venue_id', 'name', 'slug', 'starts_at', 'subtitle', 'description', 'poster_image', 'is_featured']);

        $featuredFighters = Fighter::query()
            ->with(['weightClass:id,name,gender', 'country:id,name'])
            ->where('status', 1)
            ->orderByDesc('wins')
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'nickname', 'slug', 'profile_image', 'weight_class_id', 'country_id', 'wins', 'losses', 'draws']);

        $latestNews = NewsPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at']);

        return view('landing.mma-home', compact('events', 'featuredFighters', 'latestNews', 'settings'));
    }

    /**
     * Muestra el detalle publico de un evento publicado, con navegacion y tickets.
     */
    public function event(Event $event, SystemSettingsService $settings)
    {
        abort_unless($event->status === 1, 404);

        $event->load([
            'venue:id,name,city_id,address',
            'ticketLinks' => fn ($query) => $query->where('status', 1)->orderBy('display_order'),
            'fights' => fn ($query) => $query
                ->with([
                    'cornerRed:id,nickname,first_name,last_name,slug',
                    'cornerBlue:id,nickname,first_name,last_name,slug',
                    'weightClass:id,name,gender',
                ])
                ->orderBy('display_order'),
        ]);

        $previousEvent = Event::query()
            ->published()
            ->whereNotNull('starts_at')
            ->where(function ($query) use ($event) {
                $query->where('starts_at', '<', $event->starts_at)
                    ->orWhere(fn ($sub) => $sub->where('starts_at', $event->starts_at)->where('id', '<', $event->id));
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->first(['name', 'slug']);

        $nextEvent = Event::query()
            ->published()
            ->whereNotNull('starts_at')
            ->where(function ($query) use ($event) {
                $query->where('starts_at', '>', $event->starts_at)
                    ->orWhere(fn ($sub) => $sub->where('starts_at', $event->starts_at)->where('id', '>', $event->id));
            })
            ->orderBy('starts_at')
            ->orderBy('id')
            ->first(['name', 'slug']);

        return view('landing.event-detail', compact('event', 'settings', 'previousEvent', 'nextEvent'));
    }

    /**
     * Lista peleadores activos para la landing publica.
     */
    public function fighters(SystemSettingsService $settings)
    {
        $fighters = Fighter::query()
            ->with(['weightClass:id,name,gender', 'country:id,name'])
            ->where('status', 1)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(24, ['id', 'first_name', 'last_name', 'nickname', 'slug', 'profile_image', 'weight_class_id', 'country_id', 'wins', 'losses', 'draws']);

        return view('landing.fighters-index', compact('fighters', 'settings'));
    }

    /**
     * Muestra el perfil publico de un peleador activo.
     */
    public function fighter(Fighter $fighter, SystemSettingsService $settings)
    {
        abort_unless((int) $fighter->status === 1, 404);

        $fighter->load(['weightClass:id,name,gender', 'country:id,name', 'city:id,name', 'team:id,name,slug']);

        $fights = Fight::query()
            ->where(fn ($query) => $query
                ->where('corner_red_fighter_id', $fighter->id)
                ->orWhere('corner_blue_fighter_id', $fighter->id))
            ->with([
                'event:id,name,slug,starts_at,status',
                'cornerRed:id,nickname,first_name,last_name,slug',
                'cornerBlue:id,nickname,first_name,last_name,slug',
                'result:id,fight_id,winner_fighter_id,result_type,method,round,time',
            ])
            ->whereHas('event', fn ($query) => $query->where('status', 1))
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('landing.fighter-detail', compact('fighter', 'fights', 'settings'));
    }

    /**
     * Lista noticias publicadas para la landing publica.
     */
    public function news(SystemSettingsService $settings)
    {
        $newsPosts = NewsPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(9, ['id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at']);

        return view('landing.news-index', compact('newsPosts', 'settings'));
    }

    /**
     * Muestra el detalle publico de una noticia publicada.
     */
    public function newsShow(NewsPost $newsPost, SystemSettingsService $settings)
    {
        abort_unless($newsPost->isPublished(), 404);

        return view('landing.news-detail', ['post' => $newsPost, 'settings' => $settings]);
    }

    /**
     * Muestra los planes de suscripcion activos disponibles al publico.
     */
    public function subscription(SystemSettingsService $settings)
    {
        $plans = SubscriptionPlan::query()
            ->with(['planFeatures' => fn ($query) => $query->where('status', 1)])
            ->where('status', 1)
            ->orderBy('display_order')
            ->orderBy('price')
            ->get();

        return view('landing.subscription', compact('plans', 'settings'));
    }

    /**
     * Muestra el formulario publico de contacto/compra, opcionalmente preseleccionado.
     */
    public function contact(Request $request, SystemSettingsService $settings)
    {
        $event = null;
        $plan = null;

        if ($request->filled('event')) {
            $event = Event::query()->published()->where('slug', (string) $request->input('event'))->first(['id', 'name', 'slug']);
        }

        if ($request->filled('plan')) {
            $plan = SubscriptionPlan::query()->where('status', 1)->where('slug', (string) $request->input('plan'))->first(['id', 'name', 'slug']);
        }

        $requestType = (string) $request->input('type', $event ? 'event_ticket' : ($plan ? 'subscription' : 'general_contact'));

        return view('landing.contact', [
            'settings' => $settings,
            'event' => $event,
            'plan' => $plan,
            'requestType' => $requestType,
        ]);
    }

    /**
     * Registra una solicitud publica de contacto, compra o validacion manual.
     */
    public function submitContact(Request $request, FileUploadService $files): RedirectResponse
    {
        $validated = $request->validate([
            'event_slug' => ['nullable', 'string', 'max:220'],
            'subscription_plan_id' => ['nullable', 'integer', Rule::exists('subscription_plans', 'id')->where('status', 1)],
            'contact_name' => ['required', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email', 'max:150', 'required_without_all:contact_phone,contact_whatsapp'],
            'contact_phone' => ['nullable', 'string', 'max:30', 'required_without_all:contact_email,contact_whatsapp'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30', 'required_without_all:contact_email,contact_phone'],
            'preferred_channel' => ['required', Rule::in(['whatsapp', 'phone', 'email'])],
            'request_type' => ['required', Rule::in(['general_contact', 'event_ticket', 'subscription', 'payment_proof'])],
            'message' => ['nullable', 'string', 'max:2000'],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:'.((int) config('uploads.payment_proofs.max_mb', 5) * 1024)],
        ], attributes: [
            'contact_name' => __('mma.landing.contact_page.form.name'),
            'contact_email' => __('mma.landing.contact_page.form.email'),
            'contact_phone' => __('mma.landing.contact_page.form.phone'),
            'contact_whatsapp' => __('mma.landing.contact_page.form.whatsapp'),
            'preferred_channel' => __('mma.landing.contact_page.form.channel'),
            'request_type' => __('mma.landing.contact_page.form.type'),
            'message' => __('mma.landing.contact_page.form.message'),
            'payment_proof' => __('mma.landing.contact_page.form.proof'),
        ]);

        $eventId = null;
        if (filled($validated['event_slug'] ?? null)) {
            $eventId = Event::query()->published()->where('slug', $validated['event_slug'])->value('id');
        }

        if (($validated['request_type'] ?? null) === 'event_ticket' && ! $eventId) {
            throw ValidationException::withMessages([
                'event_slug' => [__('mma.api.purchase_requests.event_required')],
            ]);
        }

        $proof = [];
        if ($request->hasFile('payment_proof')) {
            try {
                $proof = $files->storePaymentProof($request->file('payment_proof'), 'landing-contact');
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'payment_proof' => [$exception->getMessage()],
                ]);
            }
        }

        PurchaseRequest::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $request->user()?->id,
            'event_id' => $eventId,
            'subscription_plan_id' => $validated['subscription_plan_id'] ?? null,
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_whatsapp' => $validated['contact_whatsapp'] ?? null,
            'preferred_channel' => $validated['preferred_channel'],
            'request_type' => $validated['request_type'],
            'message' => $validated['message'] ?? null,
            'payment_proof_path' => $proof['path'] ?? null,
            'payment_proof_mime' => $proof['mime'] ?? null,
            'payment_proof_size' => $proof['size'] ?? null,
            'status' => 0,
            'metadata' => [
                'source' => 'landing',
                'event_slug' => $validated['event_slug'] ?? null,
                'proof_original_name' => $proof['original_name'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            ],
        ]);

        return redirect()
            ->route('landing.contact')
            ->with('success', __('mma.landing.contact_page.success'));
    }
}

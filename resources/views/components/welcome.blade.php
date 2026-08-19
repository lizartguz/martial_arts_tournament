<div class="bg-white rounded-2xl shadow p-6 max-w-4xl mx-auto mt-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-4 flex items-center gap-4">
        <img src="{{ \App\Support\PublicMedia::url('images/mma/brand/combate-real-logo.svg') }}"
             alt="Combate Real"
             class="w-24 h-24 object-contain" />
        {{ \App\Support\AdminPanel::brandName() }}
    </h2>

    <p class="text-gray-700 mb-4">
        Bienvenido al panel administrativo de <strong>{{ \App\Support\AdminPanel::brandName() }}</strong>,
        la plataforma de gestión de la promotora de MMA: eventos, peleadores, carteleras,
        suscripciones y contenido de la landing pública.
    </p>
</div>

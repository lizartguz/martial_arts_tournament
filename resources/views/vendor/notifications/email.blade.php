<x-mail::message>
{{-- Saludo personalizado --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
# @lang('¡Hola!')
@endif

{{-- Introducción personalizada --}}
Por favor, sigue las instrucciones a continuación para restablecer tu contraseña.

{{-- Botón de acción --}}
@isset($actionText)
<x-mail::button :url="$actionUrl" :color="'primary'">
Restablecer Contraseña
</x-mail::button>
@endisset

{{-- Despedida personalizada --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Atentamente,<br>
El equipo de {{ config('app.name') }}
@endif

{{-- Subcopy personalizado --}}
@isset($actionText)
<x-slot:subcopy>
Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega el siguiente enlace en tu navegador: 
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>

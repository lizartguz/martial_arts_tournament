@extends('layouts.admin')

@section('title', __('messages.catalog.page_title'))

@section('content')
    <div>
        @livewire('catalog.brand-catalog')
    </div>
@stop

@section('js')
<script>
    Livewire.on('successAlert', (event) => {
        const msg = Array.isArray(event) ? event[0]?.success : event?.success;
        if (!msg) return;
        Swal.fire({
            icon: 'success',
            title: msg,
            timer: 2200,
            showConfirmButton: false,
            customClass: { popup: 'small-swal', icon: 'small-icon' },
        });
    });

    Livewire.on('failedAlert', (event) => {
        const msg = Array.isArray(event) ? event[0]?.failed : event?.failed;
        if (!msg) return;
        Swal.fire({
            icon: 'error',
            title: msg,
            customClass: { popup: 'small-swal', icon: 'small-icon' },
        });
    });
</script>
@stop

@section('footer')
    <div class="text-center">
        SenvaTec {{ date('Y') }} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a>
    </div>
@stop

@extends('layouts.admin')

@section('title', __('messages.notices.page.title'))

@section('content_header')
    <h5>{{ __('messages.notices.page.header') }}</h5>
@stop

@section('content')
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-green" role="alert">
            <span class="flex-1">{{ session('success') }}</span>
            <button type="button" @click="show=false" class="ml-auto hover:opacity-70" aria-label="{{ __('messages.notices.gallery.close') }}"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-red" role="alert">
            <span class="flex-1">{{ session('error') }}</span>
            <button type="button" @click="show=false" class="ml-auto hover:opacity-70" aria-label="{{ __('messages.notices.gallery.close') }}"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div>
        @livewire('notifications-push.notifications-push')
    </div>
@stop

@section('css')
<style>
    .content-wrapper .alert {
        border: 0;
        border-radius: 0.9rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }
</style>
@stop

@section('footer')
<div class="text-center">
    SenvaTec {{ date('Y') }} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a>
</div>
@stop

@section('js')
@php
    $notificationI18n = [
        'close' => __('messages.notices.js.close'),
        'confirm_title' => __('messages.notices.js.confirm_title'),
        'confirm_button' => __('messages.notices.js.confirm_button'),
        'cancel' => __('messages.notices.js.cancel'),
        'gallery_title' => __('messages.notices.gallery.title'),
        'one_image' => __('messages.notices.gallery.one_image'),
        'many_images' => __('messages.notices.gallery.many_images'),
        'attachment_alt' => __('messages.notices.gallery.attachment_alt'),
    ];
@endphp
<script>
    window.notificationI18n = @json($notificationI18n);

    /**
     * Gestiona el envio del formulario en la vista.
     */
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('.notification-action-form');
        if (!form || form.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();

        Swal.fire({
            title: '<h5>' + (form.dataset.confirmTitle || window.notificationI18n.confirm_title) + '</h5>',
            text: form.dataset.confirmText || '',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: form.dataset.confirmButton || window.notificationI18n.confirm_button,
            cancelButtonText: window.notificationI18n.cancel,
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });

    /**
     * Gestiona los clics delegados de la vista.
     */
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.notification-gallery-trigger');
        if (!trigger) {
            return;
        }

        const rawImages = trigger.getAttribute('data-gallery-images');
        let images = [];

        try {
            images = JSON.parse(rawImages || '[]');
        } catch (error) {
            images = [];
        }

        if (!Array.isArray(images) || images.length === 0) {
            return;
        }

        const modal = document.getElementById('notificationGalleryModal');
        const title = document.getElementById('notificationGalleryModalLabel');
        const description = document.getElementById('notificationGalleryModalDescription');
        const feature = document.getElementById('notificationGalleryFeature');
        const stack = document.getElementById('notificationGalleryStack');

        if (!modal || !title || !description || !feature || !stack) {
            return;
        }

        title.textContent = trigger.getAttribute('data-gallery-title') || window.notificationI18n.gallery_title;
        description.textContent = images.length === 1
            ? window.notificationI18n.one_image
            : window.notificationI18n.many_images.replace(':count', images.length);

        feature.src = images[0];
        stack.innerHTML = '';

        images.forEach((image, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'notification-gallery-item' + (index === 0 ? ' is-active' : '');
            button.innerHTML = '<img src="' + image + '" alt="' + window.notificationI18n.attachment_alt.replace(':number', index + 1) + '">';
            /**
             * Gestiona el clic del elemento interactivo.
             */
            button.addEventListener('click', function () {
                feature.src = image;
                stack.querySelectorAll('.notification-gallery-item').forEach((item) => item.classList.remove('is-active'));
                button.classList.add('is-active');
            });
            stack.appendChild(button);
        });

        window.dispatchEvent(new CustomEvent('open-notification-gallery'));
    });

    if (!window.__notificationImagePreviewBooted) {
        window.__notificationImagePreviewBooted = true;
        /**
         * Gestiona los cambios de campos en la vista.
         */
        document.addEventListener('change', function (event) {
            const input = event.target.closest('[data-notification-image-input]');
            if (!input || !input.files || !input.files[0]) {
                return;
            }

            const model = input.getAttribute('data-notification-image-input');
            const file = input.files[0];
            const preview = document.querySelector('[data-notification-image-preview="' + model + '"]');
            const selected = document.querySelector('[data-notification-image-selected="' + model + '"]');
            const server = document.querySelector('[data-notification-image-server="' + model + '"]');

            if (server) {
                server.classList.add('d-none');
            }

            if (file.type && file.type.startsWith('image/') && preview) {
                const img = preview.querySelector('img');
                if (img) {
                    img.src = URL.createObjectURL(file);
                }
                preview.classList.remove('d-none');
                if (selected) {
                    selected.classList.add('d-none');
                }
                return;
            }

            if (selected) {
                selected.classList.remove('d-none');
            }
        });
    }
</script>
@stop

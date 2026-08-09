<x-landing-layout>
    <div class="flex flex-col items-center w-full max-w-6xl gap-4 p-4 mx-auto font-light text-center">
        <div class="w-full py-4">
            <img class="text-center" src="{{ asset('frontend/images/contacto01.jpg') }}" alt="" width="100%">
        </div>
        <div class="w-full">
            <h3 class="text-2xl text-[#263381] uppercase">
                {{ __('messages.landing.content.contact.knows_title')}}
            </h3>
        </div>
        <div class="w-full border-[#263381] opacity-20 border"></div>
        @livewire('components.contactForm')
    </div>
</x-landing-layout>

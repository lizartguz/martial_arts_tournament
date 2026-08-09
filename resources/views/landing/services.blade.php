<x-landing-layout>
    <div class="flex flex-col items-center w-full max-w-6xl gap-2 p-8 mx-auto font-light text-center">
        <div class="w-full">
            <h3 class="text-2xl text-[#263381] uppercase font-normal">
                {{ __('messages.landing.content.services.services_title')}}
            </h3>
        </div>
        <div class="flex flex-col items-center gap-8 md:flex-row">
            <a href="{{ url('/civil')}}">
                <img class="w-full md:w-96" src="{{ asset('frontend/images/agro-civil-01.jpg') }}" alt="" />
            </a>
            <a href="{{ url('/agro')}}">
                <img class="w-full md:w-96" src="{{ asset('frontend/images/agro-civil-02.jpg') }}" alt="" />
            </a>
        </div>
    </div>
</x-landing-layout>


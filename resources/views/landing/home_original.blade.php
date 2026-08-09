<x-landing-layout>
    <livewire:components.slider
        :images="[
            [
                'src' => asset('frontend/images/2-01.jpg'),
                'title' => __('messages.landing.content.welcome.slide_1_title'),
                'description' => __('messages.landing.content.welcome.slide_1_paragraph'),
            ],
            [
                'src' => asset('frontend/images/2-02.jpg'),
                'title' => __('messages.landing.content.welcome.slide_2_title'),
                'description' => __('messages.landing.content.welcome.slide_2_paragraph'),
            ],
            [
                'src' => asset('frontend/images/3-03.jpg'),
                'title' => __('messages.landing.content.welcome.slide_2_title'),
                'description' => __('messages.landing.content.welcome.slide_3_paragraph'),
            ]
        ]"
    />
    <div class="flex flex-col items-center w-full max-w-6xl gap-8 p-4 mx-auto font-light text-center">
        <span class="max-w-3xl">
            {{ __('messages.landing.content.welcome.first_platform')}}
        </span>
        <div class="flex flex-col items-center w-full">
            <h3 class="text-xl font-bold uppercase">
            {{ __('messages.landing.content.welcome.download_app')}}
            </h3>
            <div class="flex items-center gap-10">
                <a class="w-36" href="https://play.google.com/store/apps/details?id=net.artguz.artguz" target="_blank">
                    <img class="center-block" src="{{ asset('frontend/images/logoplaystore.png') }}" alt="" />
                </a>
                <a class="w-36" href="https://apps.apple.com/bo/app/artguz-clima-2-0/id1556257302" target="_blank">
                    <img class="center-block" src="{{ asset('frontend/images/logoappstore.png') }}" alt="" />
                </a>
            </div>
        </div>
        <div class="flex flex-col items-center w-full gap-2">
            <h3 class="text-2xl uppercase text-[#263381]">{{ __('messages.landing.content.welcome.monitoring_title')}}</h3>
            <span>{{ __('messages.landing.content.welcome.monitoring_paragraph_1')}}</span>
            <span>{{ __('messages.landing.content.welcome.monitoring_paragraph_2')}}</span>
        </div>
        <div class="flex flex-col items-center justify-center md:flex-row">
            <div class="w-96">
				<img src="{{ asset('frontend/images/' . __('messages.landing.content.welcome.img_certainty') . '.jpg') }}" alt=""/>
				<p class="text-[#263381] font-semi text-xl">{{ __('messages.landing.content.welcome.certainty')}}</p>
			</div>
			<div class="w-96">
				<img src="{{ asset('frontend/images/redsensores.png')}}" alt=""/>
				<p class="text-[#263381] font-semi text-xl">{{ __('messages.landing.content.welcome.sensor_network')}}</p>
			</div>
        </div>
        <div class="w-full border-[#263381] opacity-20 border-2"></div>
        <div class="w-full">
            <h3 class="text-2xl text-[#263381] uppercase">
                {{ __('messages.landing.content.welcome.agriculture_title')}}
            </h3>
        </div>
        <div class="flex flex-col items-start w-full gap-8 px-4 md:flex-row">
            <div class="flex flex-col items-center w-full px-8 md:w-1/3">
                <div class="h-16">
                    <img src="{{ asset('frontend/images/blurb1.png') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.welcome.sowing_title')}}
                </h3>
                <p class="pt-4">
                    {{ __('messages.landing.content.welcome.sowing_paragraph_1')}}
                </p>
            </div>
            <div class="flex flex-col items-center w-full px-8 md:w-1/3">
                <div class="h-16">
                    <img src="{{ asset('frontend/images/blurb4.png') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.welcome.fumigation_title')}}
                </h3>
                <p class="pt-4">
                    {{ __('messages.landing.content.welcome.fumigation_paragraph_1')}}
                </p>
            </div>
            <div class="flex flex-col items-center w-full px-8 md:w-1/3">
                <div class="h-16">
                    <img src="{{ asset('frontend/images/blurb2.png') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.welcome.control_title')}}
                </h3>
                <p class="pt-4">
                    {{ __('messages.landing.content.welcome.control_paragraph_1')}}
                </p>
            </div>
        </div>
        <div class="flex flex-col items-start justify-center w-full gap-8 px-8 md:flex-row">
            <div class="flex flex-col items-center w-full px-8 md:w-1/3">
                <div class="h-16">
                    <img src="{{ asset('frontend/images/blurb3.png') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.welcome.irrigation_title')}}
                </h3>
                <p class="pt-4">
                    {{ __('messages.landing.content.welcome.irrigation_paragraph_1')}}
                </p>
            </div>
            <div class="flex flex-col items-center w-full px-8 md:w-1/3">
                <div class="h-16">
                    <img src="{{ asset('frontend/images/blurb5.png') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.welcome.harvest_title')}}
                </h3>
                <p class="pt-4">
                    {{ __('messages.landing.content.welcome.harvest_paragraph_1')}}
                </p>
            </div>
        </div>
    </div>
    <div class="w-full py-8 mx-auto md:max-w-6xl">
        <img class="w-full" src="{{ asset('frontend/images/aci-01.jpg') }}" alt="" />
    </div>
    <div class="flex flex-col items-center w-full max-w-6xl gap-8 p-4 mx-auto font-light text-center">
        <div class="w-full border-[#263381] opacity-20 border-2"></div>
        <div class="w-full">
            <h3 class="text-2xl text-[#263381] uppercase font-normal">
                {{ __('messages.landing.content.welcome.benefits_title')}}
            </h3>
        </div>
        <div class="flex flex-col md:gap-8 md:flex-row">
            <div class="w-full bg-[#F5F5F5] md:w-1/4 text-start p-4 flex gap-2 flex-col">
                <h3 class="text-xl font-medium text-[#263381]">
                    {{ __('messages.landing.content.welcome.accurate_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.welcome.accurate_paragraph_1')}}
                </p>
            </div>
            <div class="w-full bg-[#F5F5F5] md:w-1/4 text-start p-4 flex gap-2 flex-col">
                <h3 class="text-xl font-medium text-[#263381]">
                    {{ __('messages.landing.content.welcome.real_time_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.welcome.real_time_paragraph_1')}}
                </p>
            </div>
            <div class="w-full bg-[#F5F5F5] md:w-1/4 text-start p-4 flex gap-2 flex-col">
                <h3 class="text-xl font-medium text-[#263381]">
                    {{ __('messages.landing.content.welcome.forecasts_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.welcome.forecasts_paragraph_1')}}
                </p>
            </div>
            <div class="w-full bg-[#F5F5F5] md:w-1/4 text-start p-4 flex gap-2 flex-col">
                <h3 class="text-xl font-medium text-[#263381]">
                    {{ __('messages.landing.content.welcome.climate_alerts_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.welcome.climate_alerts_paragraph_1')}}
                </p>
            </div>
        </div>
    </div>
    <div class="w-full py-8 mx-auto md:max-w-6xl">
        <img class="w-full" src="{{ asset('frontend/images/4-04.jpg') }}" alt="" />
    </div>
    <div class="bg-[#263381] flex items-center justify-center p-8">
        <a class="px-8 py-2 font-bold text-white uppercase border rounded hover:bg-white hover:text-[#263381]" href="{{ route('landing.contact') }}">
            {{ __('messages.landing.content.welcome.more_info')}}
        </a>
    </div>
    <div class="flex flex-col items-center w-full max-w-6xl gap-2 p-8 mx-auto font-light text-center">
        <div class="w-full">
            <h3 class="text-2xl text-[#263381] uppercase font-normal">
                {{ __('messages.landing.content.welcome.meteorology_title')}}
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

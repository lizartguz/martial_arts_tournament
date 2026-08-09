<x-landing-layout>
    <div class="flex flex-col items-center w-full max-w-6xl gap-8 p-4 mx-auto font-light text-center">
        <ul class="flex items-center justify-start w-full text-xs">
			<li><a href="{{route('landing.home')}}">{{ __('messages.landing.content.welcome.page')}}</a></li>
            <span class="mx-2 text-gray-400">/</span>
			<li class="text-[#263381] font-bold">{{ __('messages.landing.content.about_us.page')}}</li>
		</ul>
        <div class="flex flex-col items-center max-w-5xl md:flex-row">
            <div class="w-full md:w-2/3">
                <img class="mx-auto" src="{{ asset('./frontend/images/introduccion01.jpg') }}" alt=""/>
            </div>
            <div class="w-full md:w-1/3">
                <img class="mx-auto" src="{{ asset('./frontend/images/artguz-02.png') }}" alt=""/>
            </div>
        </div>
        <div class="w-full space-y-3 text-left">
            <h3 class="text-2xl text-[#263381] uppercase">
                {{ __('messages.landing.content.about_us.why_trust_title')}}
            </h3>
            <p>{{ __('messages.landing.content.about_us.report_paragraph_1')}}</p>
            <div class="w-full border-[#263381] opacity-20 border-2"></div>
        </div>
        <div>
            <img class="col-md-8 center-block" src="./frontend/images/porque artguz-01.jpg" alt="" width="100%"/>
        </div>
        <div class="w-full space-y-3">
            <h3 class="text-2xl text-[#263381] uppercase">
                {{ __('messages.landing.content.about_us.characteristics_title')}}
            </h3>
            <div class="w-full border-[#263381] opacity-20 border-2"></div>
        </div>
        <div class="flex flex-col items-start w-full md:flex-row">
            <div class="flex flex-col items-center w-full gap-4 px-8 md:w-1/3">
                <div class="h-28">
                    <img src="{{ asset('./frontend/images/iconos-02.jpg') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.about_us.analysis_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.about_us.analysis_paragraph_1')}}
                </p>
                <p>
                    {{ __('messages.landing.content.about_us.analysis_paragraph_2')}}
                </p>
            </div>
            <div class="flex flex-col items-center w-full gap-4 px-8 md:w-1/3">
                <div class="h-28">
                    <img src="{{ asset('./frontend/images/iconos-03.jpg') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.about_us.monitoring_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.about_us.monitoring_paragraph_1')}}
                </p>
                <p>
                    {{ __('messages.landing.content.about_us.monitoring_paragraph_2')}}
                </p>
            </div>
            <div class="flex flex-col items-center w-full gap-4 px-8 md:w-1/3">
                <div class="h-28">
                    <img src="{{ asset('./frontend/images/iconos-01.jpg') }}"/>
                </div>
                <div class="w-full border-[#263381] opacity-20 border-t"></div>
                <h3 class="uppercase text-[#263381] text-xl font-bold">
                    {{ __('messages.landing.content.about_us.report_title')}}
                </h3>
                <p>
                    {{ __('messages.landing.content.about_us.report_paragraph_1')}}
                </p>
            </div>
        </div>
        <div class="w-full space-y-3">
            <h3 class="text-2xl text-[#263381] uppercase">
                {{ __('messages.landing.content.about_us.web_app_title')}}
            </h3>
        </div>
        <div class="flex flex-col items-center gap-8 md:flex-row">
            <img class="w-full md:w-96" src="{{ asset('frontend/images/u11640-15.png') }}" alt="" />
            <img class="w-full md:w-96" src="{{ asset('frontend/images/mesoescala-01.jpg') }}" alt="" />
        </div>
        <div class="flex flex-col items-center gap-8 md:flex-row">
            <img class="w-full md:w-96 md:order-2" src="{{ asset('frontend/images/u11642-16.png') }}" alt="" />
            <img class="w-full md:w-96 md:order-1" src="{{ asset('frontend/images/gran escala-01.jpg') }}" alt="" />
        </div>
        <div class="flex flex-col items-center gap-8 md:flex-row">
            <img src="{{ asset('frontend/images/u11641-12.png') }}" alt="" />
            <img src="{{ asset('frontend/images/escala planetaria-01.jpg') }}" alt="" />
        </div>
    </div>
</x-landing-layout>

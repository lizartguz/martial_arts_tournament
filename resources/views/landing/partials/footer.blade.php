<div class="flex flex-col w-full">
    <div class="w-full bg-[#d7d7d7] flex items-center justify-center p-2 text-[#263381] gap-2 uppercase text-sm">
        <a>
            {{ __('messages.landing.footer.about_us') }}
        </a>
        <span>|</span>
        <a>
            {{ __('messages.landing.footer.services') }}
        </a>
    </div>
    <div class="w-full bg-[#efeeee] text-[#263381] flex items-center justify-center">
        <div class="flex flex-col items-start w-full max-w-6xl gap-6 mx-auto md:flex-row text-xs text-[#8F8F8F] p-4">
            <div class="flex justify-center w-full">
                <a href="http://amigo.artguz.net" target="_blank" title="amigoPROFEL">
                    <img src="{{ asset('frontend/images/amigoartguz.png')}}" alt="">
                </a>
            </div>
            <div class="flex items-start w-full">
                <div class="flex items-start w-full gap-2">
                    <div class="flex items-start justify-center w-full">
                        <div class="flex items-start gap-2">
                            <div>
                                <img src="{{ asset('frontend/images/icono-01.png')}}" alt="">
                            </div>
                            <div>
                                <div class="font-bold">PROFEL NORTE</div>
                                <div class="">6680 Cristo Redentor, 7°</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center w-full">
                        <div class="flex flex-col items-start gap-2">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('frontend/images/icono-02.png')}}" alt="">
                                <span>3 348 0707</span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('frontend/images/icono-03.png')}}" alt="">
                                    <span>7109 4415</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('frontend/images/icono-03.png')}}" alt="">
                                    <span>7109 4432</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center w-full gap-2">
                <div class="font-semibold uppercase">
                    {{ __('messages.landing.footer.follow_us') }}
                </div>
                <ul class="flex items-center gap-2">
                    <li class="w-8"><a target="_blank" href="https://twitter.com/ProfelBolivia" title="twitter"><img src="{{ asset('frontend/images/twitter.png')}}" alt=""></a></li>
                    <li class="w-8"><a target="_blank" href="https://www.facebook.com/artguz.bolivia" title="facebook"><img src="{{ asset('frontend/images/facebook.png')}}" alt=""></a></li>
                    <li class="w-8"><a target="_blank" href="https://www.youtube.com/channel/UCMiGdmZGPqfnjgnXeVbQFKQ" title="youtube"><img src="{{ asset('frontend/images/youtube.png')}}" alt=""></a></li>
                    <li class="w-8"><a target="_blank" href="https://www.linkedin.com/in/artguz-bolivia-14200862" title="linkedin"><img src="{{ asset('frontend/images/linkedin.png')}}" alt=""></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

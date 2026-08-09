<nav x-data="{ open: false }"  class="h-20 w-full bg-[#28327d] sticky top-0 z-50 text-white px-4">
    <div class="flex items-center w-full h-full max-w-6xl gap-4 mx-auto">
        <div class="flex-none w-32">
            <a href="{{ route('landing.home')}}">
                <img src="{{ asset('frontend/images/logo.png')}}" alt="">
            </a>
        </div>
        <div class="flex items-center justify-between w-full gap-4 px-4">
            <div class="w-full text-sm">
                <div class="hidden md:block">
                    <div class="flex items-center gap-6">
                        <a class="uppercase cursor-pointer hover:text-slate-500" href="{{ route('landing.about-us') }}">
                            {{ __('messages.landing.navigation.about_us') }}
                        </a>
                        <div class="hidden text-sm sm:flex sm:items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center uppercase cursor-pointer hover:text-slate-500">
                                        {{ __('messages.landing.navigation.services') }}
                                        <div class="ms-1">
                                            <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link class="cursor-pointer hover:!bg-[#28327d] hover:text-white" href="{{ route('landing.agro') }}">
                                        {{ __('messages.landing.navigation.agro') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class="cursor-pointer hover:!bg-[#28327d] hover:text-white" href="{{ route('landing.civil') }}">
                                        {{ __('messages.landing.navigation.civil') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class="cursor-pointer hover:!bg-[#28327d] hover:text-white" href="{{ route('landing.consulting') }}">
                                        {{ __('messages.landing.navigation.consulting') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class="cursor-pointer hover:!bg-[#28327d] hover:text-white" href="{{ route('landing.academy') }}">
                                        {{ __('messages.landing.navigation.academy') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <livewire:components.language-select triggerClass="bg-[#28327d] hover:bg-white hover:text-[#28327d]"/>
                
                <!-- Toggle de modo oscuro -->
                <button class="dark-mode-toggle p-2 rounded hover:bg-white hover:text-[#28327d]" onclick="toggleDarkMode()" title="Modo Oscuro/Claro">
                    <i class="fas fa-moon"></i>
                </button>
                
                <div class="hidden md:block">
                    <div class="flex items-center gap-4">
                        <a class="px-2 py-1 border rounded hover:bg-white hover:text-[#28327d]" href="{{ route('login') }}">
                            {{ __('messages.landing.navigation.login') }}
                        </a>
                        <div class="w-16">
                            <a href="https://artguz.com" target="_blank">
                                <img src="{{ asset('frontend/images/logoartguz.png')}}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Hamburger -->
                <div class="flex items-center -me-2 md:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md focus:outline-none hover:text-white">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden bg-[#28327d] md:hidden text-sm">
        <a href="{{ route('landing.about-us') }}">
            <div class="px-4 py-2 text-white uppercase cursor-pointer hover:text-slate-300 hover:bg-slate-800">
                {{ __('messages.landing.navigation.about_us') }}
            </div>
        </a>

        <div class="border-gray-200 border-y">
            <div class="">
                <div class="px-4 py-2 font-semibold uppercase">
                    {{ __('messages.landing.navigation.services') }}
                </div>
            </div>

            <div>
                <a href="{{ route('landing.agro') }}">
                    <div class="px-8 py-2 text-white uppercase cursor-pointer hover:text-slate-300 hover:bg-slate-800">
                        {{ __('messages.landing.navigation.agro') }}
                    </div>
                </a>
                <a href="{{ route('landing.civil') }}">
                    <div class="px-8 py-2 text-white uppercase cursor-pointer hover:text-slate-300 hover:bg-slate-800">
                        {{ __('messages.landing.navigation.civil') }}
                    </div>
                </a>
                <a href="{{ route('landing.consulting') }}">
                    <div class="px-8 py-2 text-white uppercase cursor-pointer hover:text-slate-300 hover:bg-slate-800">
                        {{ __('messages.landing.navigation.consulting') }}
                    </div>
                </a>
                <a href="{{ route('landing.academy') }}">
                    <div class="px-8 py-2 text-white uppercase cursor-pointer hover:text-slate-300 hover:bg-slate-800">
                        {{ __('messages.landing.navigation.academy') }}
                    </div>
                </a>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 p-4">
            <!-- Toggle de modo oscuro para móviles -->
            <button class="dark-mode-toggle p-2 rounded hover:bg-white hover:text-[#28327d]" onclick="toggleDarkMode()" title="Modo Oscuro/Claro">
                <i class="fas fa-moon"></i>
            </button>
            <a class="px-2 py-1 border rounded hover:bg-white hover:text-[#28327d]" href="{{ route('login') }}">
                {{ __('messages.landing.navigation.login') }}
            </a>
        </div>
    </div>

</nav>

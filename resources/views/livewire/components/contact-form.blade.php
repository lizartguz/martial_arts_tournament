<form wire:submit="send" class="w-full max-w-xl">
    <div class="flex flex-col w-full gap-4">
        <div class="text-xl text-[#263381] font-medium">
            {{ __('messages.landing.content.contact.form.title')}}
        </div>
        <div>
            {{ __('messages.landing.content.contact.form.paragraph_1')}}
        </div>
        <div class="flex flex-col w-full gap-4 md:flex-row">
            <div class="flex flex-col items-start w-full gap-2 md:w-1/2">
                <div class="font-bold">{{ __('messages.landing.content.contact.form.name_field') }}</div>
                <div class="w-full text-left">
                    <input maxlength="100" class="w-full px-3 py-1 rounded appearance-none border-slate-300" type="text" wire:model="name" wire:loading.attr="disabled">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex flex-col items-start w-full gap-2 md:w-1/2">
                <div class="font-bold">{{ __('messages.landing.content.contact.form.lastname_field') }}</div>
                <div class="w-full text-left">
                    <input maxlength="100" class="w-full px-3 py-1 rounded border-slate-300" type="text" wire:model="lastname" wire:loading.attr="disabled">
                    @error('lastname') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="flex flex-col items-start w-full gap-2">
            <div class="font-bold">{{ __('messages.landing.content.contact.form.email_field') }}</div>
            <div class="w-full text-left">
                <input maxlength="100" class="w-full px-3 py-1 rounded border-slate-300" type="email" wire:model="email" wire:loading.attr="disabled">
                @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col items-start w-full gap-2">
            <div class="font-bold">{{ __('messages.landing.content.contact.form.company_field') }}</div>
            <div class="w-full text-left">
                <input maxlength="100" class="w-full px-3 py-1 rounded border-slate-300" type="text" wire:model="company" wire:loading.attr="disabled">
                @error('company') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col items-start w-full gap-2">
            <div class="font-bold">{{ __('messages.landing.content.contact.form.phone_field') }}</div>
            <div class="w-full text-left">
                <input maxlength="15" class="w-full px-3 py-1 rounded border-slate-300" type="text" wire:model="phone" wire:loading.attr="disabled">
                @error('phone') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col items-start w-full gap-2">
            <div class="font-bold">{{ __('messages.landing.content.contact.form.country_field') }}</div>
            <div class="w-full text-left">
                <input maxlength="50" class="w-full px-3 py-1 rounded border-slate-300" type="text" wire:model="country" wire:loading.attr="disabled">
                @error('country') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col items-start w-full gap-2">
            <div class="font-bold">{{ __('messages.landing.content.contact.form.city_field') }}</div>
            <div class="w-full text-left">
                <input maxlength="100" class="w-full px-3 py-1 rounded border-slate-300" type="text" wire:model="city" wire:loading.attr="disabled">
                @error('city') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col items-start w-full gap-2">
            <div class="font-bold">{{ __('messages.landing.content.contact.form.message_field') }}</div>
            <div class="w-full text-left">
                <textarea maxlength="700" class="w-full px-3 py-1 rounded border-slate-300" rows="3" wire:model="message_field" wire:loading.attr="disabled"></textarea>
                @error('message_field') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-end">
            <button class="bg-[#263381] text-white py-2 px-8 rounded flex items-center" wire:loading.class="opacity-50 cursor-not-allowed">
                <div wire:loading.remove>
                    {{ __('messages.landing.content.contact.form.send_button') }}
                </div>
                <div wire:loading>
                    {{ __('messages.landing.content.contact.form.send_process_button') }}
                </div>
            </button>
            <div wire:loading>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 animate-[spin_0.8s_linear_infinite] fill-[#263381] block mx-auto"
                    viewBox="0 0 24 24">
                    <path
                        d="M12 22c5.421 0 10-4.579 10-10h-2c0 4.337-3.663 8-8 8s-8-3.663-8-8c0-4.336 3.663-8 8-8V2C6.579 2 2 6.58 2 12c0 5.421 4.579 10 10 10z"
                        data-original="#000000" />
                </svg>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        Livewire.on('success', data => {
            Swal.fire({
                    text: data.message,
                    icon: 'success',
                });
        });
        Livewire.on('error', data => {
            Swal.fire({
                    text: data.message,
                    icon: 'error',
                });
        });
    </script>
@endscript

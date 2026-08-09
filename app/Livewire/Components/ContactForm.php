<?php

namespace App\Livewire\Components;

use App\Mail\ContactFormMail;
use App\Models\CustomerServiceM;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class ContactForm extends Component
{
    #[Validate('required|min:2|max:100', as: 'messages.landing.content.contact.form.name_field')]
    public $name = '';

    #[Validate('required|min:2|max:100', as: 'messages.landing.content.contact.form.lastname_field')]
    public $lastname = '';

    #[Validate('required|email|max:100', as: 'messages.landing.content.contact.form.email_field')]
    public $email = '';

    #[Validate('required|min:2|max:100', as: 'messages.landing.content.contact.form.company_field')]
    public $company = '';

    #[Validate('required|numeric|digits_between:7,15', as: 'messages.landing.content.contact.form.phone_field')]
    public $phone = '';

    #[Validate('required|regex:/^[\pL\s]+$/u|min:2|max:50', as: 'messages.landing.content.contact.form.country_field')]
    public $country = '';

    #[Validate('required|max:100', as: 'messages.landing.content.contact.form.city_field')]
    public $city = '';

    #[Validate('required|max:700', as: 'messages.landing.content.contact.form.message_field')]
    public $message_field = '';

    public function send()
    {
        $this->validate();
        try {
            DB::beginTransaction();
            $customerService = CustomerServiceM::create([
                'name' => $this->name,
                'lastname' => $this->lastname,
                'email' => $this->email,
                'company' => $this->company,
                'number_phone' => $this->phone,
                'country' => $this->country,
                'city' => $this->city,
                'description' => $this->message_field,
                'type' => 2,
                'state' => 1,
            ]);
            DB::commit();
            $address = env('MAIL_NOTIFICATION');
            Mail::to($address)
                ->send(new ContactFormMail(
                    $this->name,
                    $this->lastname,
                    $this->email,
                    $this->company,
                    $this->phone,
                    $this->country,
                    $this->city,
                    $this->message_field,
                )
            );
            $this->reset();
            $this->dispatch('success', ['message' => __('messages.landing.content.contact.form.success_message')]);
        } catch (QueryException $e) {
            DB::rollback();
            $this->dispatch('error', ['message' => __('messages.landing.content.contact.form.error_db_message')]);
        } catch (Throwable $e) {
            DB::rollback();
            $this->dispatch('error', ['message' => __('messages.landing.content.contact.form.error_message')]);
        }
    }

    public function render()
    {
        return view('livewire.components.contact-form');
    }
}

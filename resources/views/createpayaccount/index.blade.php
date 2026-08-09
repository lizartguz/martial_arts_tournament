@extends('layouts.app2')

@section('title', 'Formulario de Suscripción')

@section('content')   
<div>
    @livewire('create-pay-account.create-pay-account', ['transaction_id' => $transaction_id, 'message' => $message, 'payment_method' => $payment_method, 'for_IOS' => $for_IOS])        
</div>    
@stop

@extends('layouts.admin')

@section('title', 'Pagos y renovaciones')

@section('content_header')    
    <h5>Pagos y renovaciones</h5>
@stop

@section('content')
    <div>
        @livewire('pay.pay', ['transaction_id' => $transaction_id, 'message' => $message, 'payment_method' => $payment_method, 'userId' => $userId, 'forPay' => $forPay])        
    </div>    
@stop

@section('css')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #FFFFFF;
    }
    th, td {
        border: 1px solid black;
        padding: 3px;
        text-align: center;
        font-size: 10pt;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
@stop

@section('js')
<script> 

    

    
    Livewire.on('openLink',(event) =>{
        console.log(event[0].link);
        window.open(event[0].link, '_blank');
       
    }); 
    </script> 
@stop

@section('footer')
<div class="text-center">
  SenvaTec {{date('Y')}} | Desarrollado por <a href="https://www.artguz.com" style="color: #869099;">Artguz SRL</a> 
</div>  
     
   
    
     
@stop

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateAccountC extends Controller{
   
    public function index(Request $request){
        $params = $request->query();
        $transaction_id=null;
        $message=null;
        $payment_method=null;
        $for_IOS = false;
        
        if (!empty($params)) {
            $transaction_id = $params['transaction_id'] ?? null;
            $message = $params['message'] ?? null;
            $payment_method = $params['payment_method'] ?? null;            
            
        }
        return view('createpayaccount.index',compact('transaction_id','message','payment_method','for_IOS'));
    }

    public function indexIOS(Request $request){
        $params = $request->query();
        $transaction_id=null;
        $message=null;
        $payment_method=null;
        $for_IOS = true;
        
        if (!empty($params)) {
            $transaction_id = $params['transaction_id'] ?? null;
            $message = $params['message'] ?? null;
            $payment_method = $params['payment_method'] ?? null;            
            
        }
        return view('createpayaccount.index',compact('transaction_id','message','payment_method','for_IOS'));
    }
}

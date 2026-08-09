<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserDependencyM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayAndRenewC extends Controller
{
   public function index(Request $request){
      $params = $request->query();
      $transaction_id=null;
      $message=null;
      $payment_method=null;
      $userId=null;  
      $forPay=null;   
      
      if (!empty($params)) {
        $transaction_id = $params['transaction_id'] ?? null;
        $message = $params['message'] ?? null;
        $payment_method = $params['payment_method'] ?? null;
        $transaction_id = $params['transaction_id'] ?? null;
        $userId = $params['userId'] ?? null;
        $forPay = $params['forPay'] ?? null;
        
         /*
         array:7 [▼ 
         "transaction_id" => "1061-2025-04-14 03:14:35"
         "error" => "0"
         "message" => "OK"
         "cancel_order" => "0"
         "payment_method" => "ATC_QR"
         "payment_method_id" => "30"
         "numeroReferencia" => "10220102"
         ]*/
      }


      return view('payandrenew.index',compact('transaction_id','message','payment_method','userId','forPay'));
   }

   private function textEncryption($text, $type){        
      $cypher = "Pr0F31c11m4";
      $strategy = "AES-256-CBC";
      if($type=='encode'){
          $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($strategy));        
          $encryption = openssl_encrypt($text, $strategy, $cypher, 0, $iv);        
          $res = base64_encode($iv . $encryption);
      }else{
          $data = base64_decode($text);
          $iv_longitud = openssl_cipher_iv_length($strategy);
          $iv = substr($data, 0, $iv_longitud);
          $code = substr($data, $iv_longitud);
          
          $res = openssl_decrypt($code, $strategy, $cypher, 0, $iv);
      }
      return $res;
  }
}

<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Google\Client;
use App\Http\Controllers\Controller;

class NotificationApiPushC extends Controller
{
    function sendnotifapialarmpush(Request $request){
        /*
        try {   
             
            $data = $request->json()->all();
            $tokens = $data['tokens'];
            $bodyList = $data['bodyList'];
            $title = $data['title'];

            $serviceAccountFile = storage_path('app/firebase/artguz-89301-firebase-adminsdk-nfeh8-26d41dd9ed.json');
            if (file_exists($serviceAccountFile) && is_readable($serviceAccountFile)) {
                //echo "El archivo existe y es legible.";
            
            
                $client = new Client();
                $client->setAuthConfig($serviceAccountFile);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');   
                $token = $client->fetchAccessTokenWithAssertion();
                if (array_key_exists('access_token', $token)) {
                    $accessToken = $token['access_token'];
                } else {
                    return response()->json(['error' => 'No se pudo obtener el token de acceso'], 500);
                }

                $url = 'https://fcm.googleapis.com/v1/projects/artguz-89301/messages:send';

                $headers = [
                    'Authorization: Bearer '.$accessToken,
                    'Content-Type: application/json'
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                for ($i=0; $i < count($tokens); $i++) { 
                    $notification = [
                        'message' => [
                            'token' => $tokens[$i],
                            'notification' => [
                                'title' => $title,
                                'body' => $bodyList[$i]
                            ]
                        ]
                    ];       
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
                    $result = curl_exec($ch);    
                    if ($result === FALSE) {
                        die('Error enviando notificación: ' . curl_error($ch));
                    }
                }
                curl_close($ch);
                // Imprimir la respuesta de Firebase
                //echo $result;
            } else {
                echo "El archivo no existe o no es legible.";
            }
        } catch (\Throwable $th) {
            //echo $th->getMessage();
        } 
        */       
    }

    function sendPush(){
        /*
        $tokens=['fmqJQkDlQMaCQXs6j80ZJ_:APA91bEyTWoHLNlSyaOUTwjJPVNlSogmD0daXiOisVaH-DbMwQ_dmSQ855ylZO27tDsHsV3XGZ9p5z8d74FKJepSNhIQgRzIFJiOOx9NfiiOSJhuladIo0PLT0rQlViuRs2fBvF3baJR'];
        $bodyList=['Provando que el CURL este funcionando'];
        $title='Welcome Santiago to Senvatec';
        
        try {
            if(count($tokens)>0){   
                $data = [
                    "tokens" => $tokens,
                    "bodyList" => $bodyList,
                    "title" => 'ALARMA | '.$title,
                ];
                $data_json = json_encode($data);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://208.109.232.246/api/v1/sendnotifapialarmpush"); 
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_json)
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error en cURL: ' . curl_error($ch);
                } else {
                    echo "Respuesta de la API: " . $response;
                }
        
                curl_close($ch);
                echo $response;
            }
        } catch (\Throwable $th) {
            //throw $th;
            echo $th->getMessage();
        }
        */
    }
}
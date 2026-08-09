<?php

namespace App\Http\Controllers;


use Twilio\Rest\Client;
use App\Models\StationM;
use Illuminate\Http\Request;

class OutOfServiceStationNotifierC extends Controller{

    public function webhook(Request $request){
        date_default_timezone_set('America/La_Paz');
        /*$stations = StationM::where('stations.state', 1)
            ->join('current_data as d', 'd.station_id', '=', 'stations.id')            
            ->whereRaw("TIMESTAMPDIFF(MINUTE, d.receipt_date,CONVERT_TZ(NOW(), '+00:00', '-04:00')) > 30")
            ->orderby('d.receipt_date', 'desc')
            ->limit(30) 
            ->get(['stations.name','d.receipt_date']); */      
        
        $from = $request->input('From');
        $from = str_replace('whatsapp: ', 'whatsapp:+', $from);
        $body = strtolower($request->input('Body'));  
        $keywords = ['sagpf', 'lmcpf'];

       
        if (in_array($body, $keywords)) {           
            $stations = StationM::where('stations.state', 1)
            ->join('current_data as d', 'd.station_id', '=', 'stations.id')            
            ->whereRaw("TIMESTAMPDIFF(MINUTE, d.receipt_date,CONVERT_TZ(NOW(), '+00:00', '-04:00')) > 30")
            ->orderby('d.receipt_date', 'desc')
            //->limit(10)
            ->get(['stations.name','d.receipt_date']);
            //dd($stations);

            // Preparar la respuesta con los resultados
            $responseMessage = $this->formatStationList($stations);
        } else {
            // Si no se encuentra ninguna palabra clave, no hacer nada
            $responseMessage = "No se encontró ninguna estación para la palabra clave '$body'.";
        }

        // Enviar la respuesta al usuario
        $this->sendWhatsAppMessage($from, $responseMessage);

        return response()->json(['status' => 'Mensaje procesado']);
    }

    // Función para dar formato a la lista de estaciones
    private function formatStationList($stations)
    {
        if ($stations->isEmpty()) {
            return "No se encontraron estaciones que coincidan con tu búsqueda.";
        }

        $stationReceiptDates = $stations->pluck('receipt_date')->toArray();
        $stationNames = $stations->pluck('name')->toArray();
        $formattedList = '';
        $i=1;
        foreach ($stationReceiptDates as $index => $date) {
            $formattedList .= $i.'.-'. $stationNames[$index]. ' - ' . $date  . "\n\n";
            $i++;
        }

        return "Últimas estaciones con retrazos mayor e igual a 30 minutos:\n\n" . $formattedList;
    }


    // Función para enviar mensajes de WhatsApp
    private function sendWhatsAppMessage($to, $message){
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_WHATSAPP_NUMBER');
        $client = new Client($sid, $token);

        try {
            
            $messageParts = $this->splitMessage($message);
            $listPhone = ['whatsapp:+59160992075', 'whatsapp:+59168808957'];
            for($i=0; $i < count($listPhone); $i++){
                foreach ($messageParts as $part) {
                    $client->messages->create(
                        //$to,
                        $listPhone[$i],
                        [
                            'from' => $twilioNumber,
                            'body' => $part,
                            'mediaUrl' => 'https://i0.wp.com/univalletelevision.com/wp-content/uploads/2024/04/JLBENEZ72VHU3PR4W6HHWMBHOI-LvzjIJ.jpeg',
                        ]                       
                    );
                    //sleep(1);
                }
            }                      

            return response()->json(['status' => 'success', 'messageSid' => $message->sid]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }  
    
    
    private function splitMessage($message, $maxLength = 1500) {
    $parts = [];

    while (strlen($message) > $maxLength) {
        $breakpoint = strrpos(substr($message, 0, $maxLength), "\n\n");

        if ($breakpoint === false) {
            $breakpoint = strrpos(substr($message, 0, $maxLength), "\n");
        }

        if ($breakpoint === false) {
            $breakpoint = strrpos(substr($message, 0, $maxLength), ' ');
        }

        if ($breakpoint === false) {
            $breakpoint = $maxLength;
        }

        $parts[] = substr($message, 0, $breakpoint);
        $message = substr($message, $breakpoint + 1);
    }
    
    $parts[] = $message;

    return $parts;
}
   
}

<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;

class QairImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    use Importable;
    
    public $message = '';

    public function collection(Collection $rows)
    {
        $this->setData($rows);
    }

    public function setData(Collection $rows){
        //dd(count($rows));
        date_default_timezone_set('America/La_Paz');
        try{
            $failed = $this->verifFailed($rows);
            if($failed){ 
                dd($this->message);                  
                return back()->with($this->message);

            }else{
                $fechaLimite = '2025-02-05 18:20:02';
                
                for ($i=1; $i <count($rows) ; $i++) {
                    try{   
                        $fechaDato = trim($rows[$i][0]);
                        if ($fechaDato > $fechaLimite) {
                            DB::beginTransaction();                
                            DB::connection('artguz2currentyear')
                            ->table('datos_ma')->insert([
                                'DISPOSITIVO_ID' => 483,
                                'FECHA' => trim($rows[$i][0]),
                                'FECHA_REG' => date('Y-m-d H:i:s'),
                                'TEMP_OUT' => trim($rows[$i][1]),
                                'HUM_OUT' => trim($rows[$i][2]),
                                'PM10' => trim($rows[$i][3]),
                                'PM1' => trim($rows[$i][4]),
                                'PM2_5' => trim($rows[$i][5]),
                            ]);     

                            DB::commit(); 
                        }                    
                    }catch(\Exception $e){
                        DB::rollback();
                        dd($e->getMessage(),$rows[$i],$i);
                        //return back()->with('error', $e->getMessage());
                        return back()->with('error', $i.'-'.$e->getMessage());
                        break;
                        //return back()->with('error', $i."-".$this->nombres." ".$this->apellido_paterno);
                    }
                }
            }
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifFailed($rows){
        $failed = false;
        $this->message = '';
        for ($i=1; $i <count($rows) ; $i++) {
            try{
                if ($rows[$i][1] !== null && $rows[$i][1] !== '' && is_numeric($rows[$i][1])) { //Temp
                    if ($rows[$i][2] !== null && $rows[$i][2] !== '' && is_numeric($rows[$i][2])) { //Hum
                        if ($rows[$i][3] !== null && $rows[$i][3] !== '' && is_numeric($rows[$i][3])) { //PM10
                            if ($rows[$i][4] !== null && $rows[$i][4] !== '' && is_numeric($rows[$i][4])) { //PM1
                                if ($rows[$i][5] !== null && $rows[$i][5] !== '' && is_numeric($rows[$i][5])) {////PM2.5
                                    
                                }else{
                                    $failed = true;
                                    $this->message = 'Dato: '.$rows[$i][3].' no valido en la Fila: '.($i+1).', de la columna: pm2.5'; 
                                    break;
                                }
                            }else{
                                $failed = true; 
                                $this->message = 'Dato: '.$rows[$i][4].' no valido en la Fila: '.($i+1).', de la columna: pm1';  
                                break;
                            }
                        }else{
                            $failed = true; 
                            $this->message = 'Dato: '.$rows[$i][3].' no valido en la Fila: '.($i+1).', de la columna: pm10';
                            break;
                        }
                    }else{
                        $failed = true; 
                        $this->message = 'Dato: '.$rows[$i][2].' no valido en la Fila: '.($i+1).', de la columna: Hum';
                        break;
                    }
                    
                }else{
                    $failed = true;
                    $this->message = 'Dato: '.$rows[$i][1].' no valido en la Fila: '.($i+1).', de la columna: Temp';
                    break;
                } 
                        
                             
            }catch(\Exception $e){
                $failed = true;
                $this->message = $e->getMessage();
                break;
            }
        }  
       
        return $failed;
    }

    public function chunkSize(): int{
        // TODO: Implement chunkSize() method.
        return 100;
    }

    public function startRow(): int{
        // TODO: Implement startRow() method.
        return 100;
    }
}

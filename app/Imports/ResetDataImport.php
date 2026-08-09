<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

//
//class ResetDataImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithStartRow
class ResetDataImport implements /*ToModel*/ ToCollection{
    /**
    * @param Collection $collection
    */
    use Importable;
    
    public $message = '';

    public function collection(Collection $rows) {
        //dd($rows);
        $this->setData($rows);
    }
    
    public function setData(Collection $rows){
        date_default_timezone_set('America/La_Paz');
        try{
            for ($i=1; $i <count($rows) ; $i++) {
                try{   
                    DB::beginTransaction();     
                    $failed = $this->verifFailed($rows);
                    if($failed){                   
                        return back()->with($this->message);
                    }else{
                        $dateCol = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[2][0])->format('Y-m-d');  
                        $dataDate = explode("-", $dateCol);  
                        dd($dataDate);    
                            DB::connection('mysql_pro')
                            ->connection('total_diario')
                            ->create([
                                'DISPOSITIVO_ID' => trim($rows[$i][30]),
                                'F_YEAR' => trim($dataDate[0]),
                                'F_MONTH' => trim($dataDate[1]),
                                'F_DAY' => trim($dataDate[2]),
                                        //0 =>  "Date"
                                        //1 =>    "Time"
                                'TEMP_OUT' => trim($rows[$i][2]),//2 =>    "Temp_Out"
                                'TEMP_OUT_MAX' => trim($rows[$i][3]),//3 =>    "Hi_Temp"
                                'TEMP_OUT_MIN' => trim($rows[$i][4]),//4 =>    "Low_Temp"
                                'HUM_OUT' => trim($rows[$i][5]),//5 =>    "Out_Hum"
                                'DEW_PT_OUT' => trim($rows[$i][6]),//6 =>    "Dew_Pt."
                                'WIND_SPEED' => trim($rows[$i][7]),//7 =>    "Wind_Speed"
                                'WIND_DIR' => trim($rows[$i][8]),//8 =>    "Wind_Dir"  (Text)
                                        //9 =>    "Wind_Run"
                                'WIND_SPEED_HI' => trim($rows[$i][10]),//10 =>   "Hi_Speed"
                                        //11 =>   "Hi_Dir"
                                        //12 =>   "Wind_Chill"
                                'HEAT_INDEX_OUT' => trim($rows[$i][13]),//13 =>   "Heat_Index"
                                        //14 =>   "THW_Index"
                                'PRESS' => trim($rows[$i][15]),//15 =>   "Bar  "
                                'RAIN_TOTAL' => trim($rows[$i][16]),//16 =>   "Rain"
                                'TOTAL_RAIN_RATE' => trim($rows[$i][17]),//17 =>   "Rain_Rate"
                                        //18 =>   "Heat_D-D"
                                        //19 =>   "Cool_D-D"
                                        //20 =>   "In_Temp"
                                        //21 =>   "In_Hum"
                                        //22 =>   "In_Dew"
                                        //23 =>   "In_Heat"
                                        //24 =>   "In_EMC"
                                        //25 =>   "In_Air_Density"
                                        //26 =>   "Wind_Samp"
                                        //27 =>   "Wind_Tx "
                                        //28 =>   "ISS_Recept"
                                        //29 =>   "Arc._Int."
                            ]);                   
                            DB::commit();
                    }
                }catch(\Exception $e){
                    DB::rollback();
                    //return back()->with('error', $e->getMessage());
                    return back()->with('error', $i.'-'.$e->getMessage());
                    //return back()->with('error', $i."-".$this->nombres." ".$this->apellido_paterno);
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
                if($rows[$i][0]!= null && $rows[$i][0]!=''){
                    if (is_float($rows[$i][0])) {
                        if ($rows[$i][2]!= null && $rows[$i][2]!='' && (is_float($rows[$i][2]) || is_int($rows[$i][2]))) { //Temp_Out
                            if ($rows[$i][3]!= null && $rows[$i][3]!='' && (is_float($rows[$i][3]) || is_int($rows[$i][3]))) { //Hi_Temp
                                if ($rows[$i][4]!= null && $rows[$i][4]!='' && (is_float($rows[$i][4]) || is_int($rows[$i][4]))) { //Low_Temp
                                    if ($rows[$i][5]!= null && $rows[$i][5]!='' && (is_float($rows[$i][5]) || is_int($rows[$i][5]))) { //Out_Hum
                                        if ($rows[$i][6]!= null && $rows[$i][6]!='' && (is_float($rows[$i][6]) || is_int($rows[$i][6]))) { //Dew_Pt.
                                            if ($rows[$i][7]!= null && $rows[$i][7]!='' && (is_float($rows[$i][7]) || is_int($rows[$i][7]))) { //Wind_Speed
                                                if (!is_float($rows[$i][8]) && !is_int($rows[$i][8])) { //Wind_Dir
                                                    if ($rows[$i][10]!= null && $rows[$i][10]!='' && (is_float($rows[$i][10]) || is_int($rows[$i][10]))) { //Hi_Speed
                                                        if ($rows[$i][13]!= null && $rows[$i][13]!='' && (is_float($rows[$i][13]) || is_int($rows[$i][13]))) { //Heat_Index
                                                            if ($rows[$i][15]!= null && $rows[$i][15]!='' && (is_float($rows[$i][15]) || is_int($rows[$i][15]))) { //Bar
                                                                if ($rows[$i][16]!= null && $rows[$i][16]!='' && (is_float($rows[$i][16]) || is_int($rows[$i][16]))) { //Rain
                                                                    if ($rows[$i][17]!= null && $rows[$i][17]!='' && (is_float($rows[$i][17]) || is_int($rows[$i][17]))) { //Rain_Rate
                                                                
                                                                    }else{
                                                                        $failed = true; 
                                                                        $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Rain_Rate'; 
                                                                    } 
                                                                }else{
                                                                    $failed = true; 
                                                                    $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Rain';
                                                                } 
                                                            }else{
                                                                $failed = true; 
                                                                $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Bar';
                                                            } 
                                                        }else{
                                                            $failed = true;
                                                            $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Heat_Index'; 
                                                        }      
                                                    }else{
                                                        $failed = true; 
                                                        $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Hi_Speed';
                                                    }
                                                }else{
                                                    $failed = true;
                                                    $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Wind_Dir';
                                                }
                                            }else{
                                                $failed = true; 
                                                $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Wind_Speed';
                                            }
                                        }else{
                                            $failed = true;
                                            $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Dew_Pt'; 
                                        }
                                    }else{
                                        $failed = true; 
                                        $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Out_Hum';  
                                    }
                                }else{
                                    $failed = true; 
                                    $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Low_Temp';
                                }
                            }else{
                                $failed = true; 
                                $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Hi_Temp';
                            }
                            
                        }else{
                            $failed = true;
                            $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Temp_Out';
                        }                
                    }else{
                        $failed = true; 
                        $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Date';
                    }
                }else{
                    $failed = true;
                    $this->message = 'Dato no valido en la Fila: '.($i+1).', de la columna: Date';
                }          
            }catch(\Exception $e){
                $failed = true;
                $this->message = $e->getMessage();
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifPayC extends Controller
{
    public function verif(){        
        date_default_timezone_set('America/La_Paz'); 
        $currentDate = date('Y-m-d');        
    }
}

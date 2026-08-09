<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GraphDataStationC extends Controller
{
    public function index() {
        $typeS =null;
        return view('graphDataStation.index',compact('typeS'));
    }
}

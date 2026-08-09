<?php

namespace App\Http\Controllers;

class DownloadDataStationC extends Controller
{
    public function index() {
        $typeS = null;
        return view('downloadDataStation.index',compact('typeS'));
    }
}

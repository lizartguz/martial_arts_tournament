<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadAppC extends Controller
{
    public function downloadAndroid(){
        return view('downloadapp.downloadandroid');
    }
    public function downloadIOS(){
        return view('downloadapp.downloadios');
    }
}

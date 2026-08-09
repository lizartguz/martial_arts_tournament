<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Google\Client;

class OrganizeUserC extends Controller
{
    public $listTokens = [];
    public function index(){
        return view('organizeusers.index');
    }  

}

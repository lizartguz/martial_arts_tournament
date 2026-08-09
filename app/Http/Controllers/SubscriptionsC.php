<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionsC extends Controller
{
    public function index(){
        return view('subscriptions.index');
    }
}

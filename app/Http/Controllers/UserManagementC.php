<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserManagementC extends Controller
{
    public function index(){
        return view('user_management.index');
    }
}

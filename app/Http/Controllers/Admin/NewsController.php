<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class NewsController extends Controller
{
    public function index()
    {
        Gate::authorize('news.view');

        return view('admin.news.index');
    }
}

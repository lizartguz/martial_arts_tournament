<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->canAny([
            'reports.events.view',
            'reports.subscriptions.view',
            'reports.sales.view',
        ]), 403);

        return view('admin.reports.index');
    }
}

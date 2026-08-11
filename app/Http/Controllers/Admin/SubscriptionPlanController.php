<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        Gate::authorize('subscription_plans.view');

        return view('admin.subscription-plans.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BoardM;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FundecorDashboardC extends Controller
{
    public function begin($codigo){
        
        if ($codigo != 'central') {
            return response('No tiene permiso', 403);
        }
        session([
            'dashboard_autorizado' => true,
            'dashboard_expira_en' => now()->setTime(23, 59, 59),
        ]);

        return redirect('/683f1d18-2cdc-30b8-8c47-e96904d87fe6_s64r40J1l5-6I5k3-fd433fg6x');        
    }


    public function viewDashboard(){    
        if (!session('dashboard_autorizado') || now()->greaterThan(session('dashboard_expira_en'))) {
            abort(403, 'Sesión no autorizada');
        }
        return view('fundecordashboard.index');       
    }

}

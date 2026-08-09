<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderUsersDataImport;
use App\Imports\InsertUsersDataImport;

class ImportDataGlobalC extends Controller
{
    public function index() {
        return view('list_import.index');
    }

    public function importUserData(Request $request){
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480' // 20MB máx
        ]);

        $file = $request->file('excel_file');

        // Aquí puedes procesarlo con Laravel Excel (si lo deseas)
        Excel::import(new InsertUsersDataImport, $file);

        // Para testear: solo guardar temporalmente y retornar info
        $path = $file->store('temp');
        
        return back()->with('success', 'Archivo cargado: ' . $file->getClientOriginalName());
    }

    public function orderUserData(Request $request){
        
        $request->validate([
            'excel_file_order' => 'required|file|mimes:xlsx,xls|max:20480' // 20MB máx
        ]);

        $file = $request->file('excel_file');

        // Aquí puedes procesarlo con Laravel Excel (si lo deseas)
        Excel::import(new OrderUsersDataImport, $file);

        // Para testear: solo guardar temporalmente y retornar info
        $path = $file->store('temp');
        
        return back()->with('success', 'Archivo cargado: ' . $file->getClientOriginalName());
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DailyTotalC;
use Illuminate\Http\Request;
class DailyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-data-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecución de daily_data';

    /**
     * Execute the console command.
     */

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        $c = new DailyTotalC();
        $request = new Request();
        $c->setDailyTotal($request);
        return 0;
    }
}

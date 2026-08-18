<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;

trait CountsQueries
{
    protected function countQueriesDuring(callable $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        $count = count(DB::getQueryLog());

        DB::flushQueryLog();

        return $count;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

class HashDbTables extends Command
{
    protected $signature = 'db:hash';

    protected $description = 'Database Bootstrap Integrity Hash';

    public function handle()
    {
        $tables = DB::table('information_schema.columns')
            ->where('TABLE_SCHEMA', '=', DB::connection()->getDatabaseName())
            ->whereNotIn('COLUMN_NAME', ["created_at", "updated_at"])
            ->select(DB::raw('TABLE_NAME t, GROUP_CONCAT(COLUMN_NAME) gc'))
            ->groupBy('TABLE_NAME')
            ->get()
            ->map(function ($tgc) {
                return [
                    $tgc->t,
                    DB::table($tgc->t)->get()->toArray(),
                ];
            });

        echo 'bootstrap data integrity code: ' . crc32(json_encode($tables)) . PHP_EOL;
    }
}

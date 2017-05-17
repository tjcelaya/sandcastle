<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
// use SqlFormatter;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DBLoggingListener
{
<<<<<<< Updated upstream
    public static $pretty = false;
    public static $srsly = true;
=======
    private $logger = app('log');

    public static $pretty = true;
    public static $srsly = false;
>>>>>>> Stashed changes

    public static function start() {
        Log::info('DBLoggingListener start');
        static::$srsly = true;
    }

    public static function stop() {
        Log::info('DBLoggingListener stop');
        static::$srsly = false;
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (!static::$srsly && env('DB_LOG', false)) {
            return;
        }

        $query = $event->sql;
        $bindings = $event->bindings;
        $time = $event->time;

        // Format binding data for sql insertion
        foreach ($bindings as $i => $binding)
        {
            if ($binding instanceof \DateTime)
            {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            }
            else if (is_string($binding))
            {
                $bindings[$i] = "'$binding'";
            }
        }

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings);

        Log::info(
            " -Q-\n".
            (
                (static::$pretty && php_sapi_name() == 'cli')
                ? SqlFormatter::format($query)
                : $query
            )
        );
    }
}

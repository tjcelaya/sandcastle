<?php

function logy()
{
    App\Listeners\DBLoggingListener::start();
}

function logn()
{
    App\Listeners\DBLoggingListener::stop();
}

function array_at(&$arr, ...$idx)
{
    $at = [];
    foreach ($idx as &$i) {
        $at[] = $arr[$i];
    };

    return $at;
}

function array_sample($a, $n = 1)
{
    if ($n === 0) {
        return [];
    }
    if ($a instanceof Illuminate\Support\Collection) {
        $a = $a->all();
    }
    $n    = count($a) < $n ? count($a) : $n;
    $idxs = array_rand($a, $n);
    $idxs = $n == 1 ? [$idxs] : $idxs;
    shuffle($idxs);

    return array_at($a, ...$idxs);
}

function coinflip()
{
    return (bool)rand(0, 1);
}

function timestamps($c, $u = null, $d = null)
{
    $t = [
        'created_at' => $c,
        'updated_at' => $u ? $u : $c,
    ];
    if ($d) {
        $t['deleted_at'] = $d;
    }

    return $t;
}

function enlist($res)
{
    return (is_array($res)
            || $res instanceof Illuminate\Support\Collection)
        ? $res : [$res];
}

function routes($method = null, $displayType = 'options')
{
    $routes = [];

    foreach (APIRoute::getRoutes() as $r) {
        foreach ($r->getRoutes() as $rR) {
            foreach ($rR->getMethods() as $rRM) {
                if (
                    !$method ||
                    (is_string($method) && mb_strtoupper($method) == $rRM)
                ) {
                    $path = $rR->getPath();

                    switch ($displayType) {
                        case 'options':
                            $el = "{$path}";
                            break;
                        case 'link':
                            $url = getenv('APP_URL');
                            $el = "{$url}/{$path}";
                            break;
                        default:
                            break;
                    }

                    $routes[$path] = $el;
                }
            }
        }
    }

    return $routes;
}

function api_route($name, $version = 'v1')
{
    app('Dingo\Api\Routing\UrlGenerator')
        ->version($version)
        ->route($name);
}

function get_things()
{
    return [
        'contact',
        'issue',
        'resource',
        'status',
        'user',
    ];
}

function report_json_field($field_name, $aggregation_type) {
    return '_' . $aggregation_type . '__' . $field_name;
}
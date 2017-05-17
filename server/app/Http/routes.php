<?php

APIRoute::version(
    'v1',
    function () {
        APIRoute::post(
            'oauth/access_token',
            [
                'as'   => 'oauth.access_token',
                'uses' => App\Http\Controllers\v1\RootController::class . '@postAccessToken',
            ]
        );

        APIRoute::get(
            '/',
            [
                'as'   => 'root.index',
                'uses' => App\Http\Controllers\v1\RootController::class . '@getIndex',
            ]
        );

        APIRoute::options(
            '/',
            [
                'as'   => 'root.options',
                'uses' => App\Http\Controllers\v1\RootController::class . '@optionsIndex',
            ]
        );
    }
);

foreach (get_things() as $thing) {

    APIRoute::version(
        'v1',
        function ($api) use ($thing) {
            APIRoute::options(
                '/' . $thing,
                [
                    'as'     => $thing . '.options',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@options',
                    'scopes' => $thing,
                ]
            );
        }
    );


    APIRoute::version(
        'v1',
        ['middleware' => 'oauth:' . $thing],
        function ($api) use ($thing) {

            APIRoute::get(
                $thing,
                [
                    'as'     => $thing . '.index',
                    'scopes' => $thing . '.index',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@index',
                ]
            );

            APIRoute::post(
                $thing,
                [
                    'as'     => $thing . '.store',
                    'scopes' => $thing . '.store',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@store',
                ]
            );
            APIRoute::get(
                $thing . '/{id}',
                [
                    'as'     => $thing . '.show',
                    'scopes' => $thing . '.show',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@show',
                ]
            );
            APIRoute::put(
                $thing . '/{id}',
                [
                    'as'     => $thing . '.update',
                    'scopes' => $thing . '.update',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@update',
                ]
            );
            APIRoute::patch(
                $thing . '/{id}',
                [
                    'as'     => $thing . '.update',
                    'scopes' => $thing . '.update',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@update',
                ]
            );
            APIRoute::delete(
                $thing . '/{id}',
                [
                    'as'     => $thing . '.destroy',
                    'scopes' => $thing . '.destroy',
                    'uses'   => 'App\Http\Controllers\v1\\' . studly_case($thing) . 'Controller@destroy',
                ]
            );
        }
    );
}


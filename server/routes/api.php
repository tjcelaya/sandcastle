<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function (Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
    });

    $api->group(['middleware' => 'jwt.auth'], function (Router $api) {
        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            'uses' => function () {
                return response()->json([
                    'message' => 'Check Headers'
                ]);
            }
        ]);
    });

    $api->post(
        'issue',
        App\Api\V1\Controllers\IssueSubmissionController::class);
});

$api->version('v1',['middleware' => ['jwt.auth','api.auth']], function (Router $api) {
    // ACTION routes
    $api->get(
        'issue/pending',
        [
            'middleware' => ['role:admin|analyst|peer'],
            'uses' => App\Api\V1\Controllers\IssueManagementController::class . '@assignedToUser']);

    $api->get(
        'user-issues',
        [
            'middleware' => ['role:admin|analyst'],
            'uses' => App\Api\V1\Controllers\IssueManagementController::class . '@byUser']);

    $api->put(
        'issue/resource',
        [
            'middleware' => 'permission:' . \App\Model\Permission::NAME_ISSUE_RESOURCE_ELECT,
            'uses' => App\Api\V1\Controllers\IssueManagementController::class . '@electResource']);

    $api->patch(
        'issue/{id}',
        [
            'middleware' => 'permission:' . \App\Model\Permission::NAME_ISSUE_VERIFY,
            'uses' => App\Api\V1\Controllers\IssueManagementController::class . '@verify']);
});

// REST routes
foreach (get_things() as $thing) {

    $api->version(
        'v1',
        function ($api) use ($thing) {
            $api->options(
                '/' . $thing,
                [
                    'as' => $thing . '.options',
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@options',
                    'scopes' => $thing,
                ]
            );
        }
    );

    $api->version(
        'v1',
        ['middleware' => 'jwt.auth'],
        function ($api) use ($thing) {

            $api->get(
                $thing,
                [
                    'as' => "{$thing}.list",
                    'scopes' => "{$thing}.list",
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@index',
                ]
            );

            $thing == 'issue' ? null : $api->post(
                $thing,
                [
                    'as' => "{$thing}.create",
                    'scopes' => "{$thing}.write",
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@store',
                ]
            );
            $api->get(
                $thing . '/{id}',
                [
                    'as' => "{$thing}.view",
                    'scopes' => "{$thing}.read",
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@show',
                ]
            );
            $api->put(
                $thing . '/{id}',
                [
                    'as' => "{$thing}.overwrite",
                    'scopes' => "{$thing}.write",
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@update',
                ]
            );
            $api->patch(
                $thing . '/{id}',
                [
                    'as' => "{$thing}.update",
                    'scopes' => "{$thing}.write",
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@update',
                ]
            );
            $api->delete(
                $thing . '/{id}',
                [
                    'as' => "{$thing}.delete",
                    'scopes' => "{$thing}.write",
                    'uses' => App\Api\V1\Controllers\CrudController::class . '@destroy',
                ]
            );
        }
    );
}



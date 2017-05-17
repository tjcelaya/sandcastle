<?php

namespace App\Functional\Api\V1\Controllers;

use Hash;
use App\Model\User;
use App\TestCase;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginControllerTest extends TestCase
{
    // use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        /** @var Hasher $hasher */
        $hasher = app(Hasher::class);

        $user = new User([
            'name' => 'Test',
            'email' => 'test@email.com',
            'password' => $hasher->make('123456')
        ]);

        $user->save();
    }

    public function testLoginSuccessfully()
    {
        $r = $this->post('api/auth/login', [
            'email' => 'test@email.com',
            'password' => '123456'
        ])->assertJson([
            'status' => 'ok'
        ])->assertJsonStructure([
            'status',
            'token'
        ]);

        $this->assertTrue($r->isOk());

        $this->get('api/protected')->isOk();
    }

    public function testLoginWithReturnsWrongCredentialsError()
    {
        $this->post('api/auth/login', [
            'email' => 'unknown@email.com',
            'password' => '123456'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(403);
    }

    public function testLoginWithReturnsValidationError()
    {
        $this->post('api/auth/login', [
            'email' => 'test@email.com'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(422);
    }
}

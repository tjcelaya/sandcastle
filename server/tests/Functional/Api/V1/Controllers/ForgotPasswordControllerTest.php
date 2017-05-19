<?php

namespace App\Functional\Api\V1\Controllers;

use App\Model\User;
use App\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ForgotPasswordControllerTest extends TestCase
{
    // use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $user = new User([
            'name' => 'Test',
            'email' => 'test@email.com',
            'password' => '123456'
        ]);

        $user->save();
    }

    public function testForgotPasswordRecoverySuccessfully()
    {
        $r = $this->post('api/auth/recovery', [
            'email' => 'test@email.com'
        ])->assertJson([
            'status' => 'ok'
        ]);

        $this->assertTrue($r->isOk());
    }

    public function testForgotPasswordRecoveryReturnsUserNotFoundError()
    {
        $this->post('api/auth/recovery', [
            'email' => 'unknown@email.com'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(404);
    }

    public function testForgotPasswordRecoveryReturnsValidationErrors()
    {
        $this->post('api/auth/recovery', [
            'email' => 'i am not an email'
        ])->assertJsonStructure([
            'error'
        ])->assertStatus(422);
    }
}

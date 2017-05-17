<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 4/20/17
 * Time: 2:53 PM
 */

namespace App;

use App\AuthInfo;
use App\Model\AuthenticatableBase;
use App\Model\User;
use App\TestCase;
use Faker\Factory;
use Illuminate\Contracts\Hashing\Hasher;
use LogicException;

abstract class SingleAuthTestCase extends AuthTestCase
{
    /** @var User */
    protected $authUser;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->authUser = null;
        $this->deauthenticate();
        parent::tearDown();
    }

    protected function authenticate($email = null, $password = null): void
    {
        parent::authenticate(
            $email ?: $this->email,
            $password ?: $this->password
        );
    }

    protected function refreshAuthUser()
    {
        $this->email = $this->faker->email;
        $this->password = $this->faker->password;

        $this->authUser = new User([
            'name' => $this->faker->name,
            'email' => $this->email,
        ]);
        $this->authUser->password = $this->hasher->make($this->password);
        $this->authUser->save();
    }

    protected function authWithRole($roles): void
    {
        $this->refreshAuthUser();
        $this->authUser->attachRoles(enlist($roles));
        $this->authenticate();
    }
}
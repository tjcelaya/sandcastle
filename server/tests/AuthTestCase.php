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
use App\TestCase;
use Faker\Factory;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\TestResponse;
use LogicException;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class AuthTestCase extends TestCase
{
    /** @var array */
    protected $authInfos = [];

    /** @var AuthInfo */
    protected $currentAuthInfo = null;

    protected function headersWithAuth($headers = []): array
    {
        if (empty($this->currentAuthInfo)) {
            throw new LogicException('getAuthHeader called without currentAuthInfo!');
        }

        $headers['Authorization'] = 'Bearer ' . $this->currentAuthInfo->token();
        return $headers;
    }

    protected function authenticate($email, $password): void
    {
        if ($this->currentAuthInfo !== null
            && $this->currentAuthInfo->email() === $email
            && $this->currentAuthInfo->password() === $password
        ) {
            return;
        }

        $info = new AuthInfo($email, $password);
        $token = app('tymon.jwt.auth')->attempt($info->credentials());
        $this->currentAuthInfo = $info->withToken($token);

        $this->authInfos[] = $this->currentAuthInfo;
    }

    protected function deauthenticate(): void
    {
        $this->currentAuthInfo = null;
    }
}
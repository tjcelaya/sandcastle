<?php

namespace App;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use LogicException;

class AuthInfo
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var string */
    protected $token;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function token(): string
    {
        if (empty($this->token)) {
            throw new LogicException('token is unset');
        }

        return $this->token;
    }

    public function withToken(string $token): self
    {
        if (!empty($this->token)) {
            throw new LogicException('overwriting token');
        }

        $this->token = $token;
        return $this;
    }

    public function credentials(): array {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}

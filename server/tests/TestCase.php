<?php

namespace App;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /** @var Generator */
    protected $faker = null;

    /** @var Hasher */
    protected $hasher = null;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }


    protected function setUp()
    {
        parent::setUp();

        $this->hasher = app(Hasher::class);
        $this->faker = Factory::create();
    }
}

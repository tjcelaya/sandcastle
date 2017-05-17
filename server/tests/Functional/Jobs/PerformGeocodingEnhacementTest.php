<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 4/20/17
 * Time: 5:47 PM
 */
namespace App\Jobs;

use App\Exceptions\VagueAddressException;
use App\Functional\Api\V1\Controllers\IssueCrudControllerTest;
use App\Model\Issue;
use App\Providers\EventServiceProvider;
use App\SingleAuthTestCase;
use App\TestCase;
use DomainException;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Illuminate\Config\Repository;
use Geocoder\Laravel\Facades\Geocoder;

class PerformGeocodingEnhancementTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->app->make(Repository::class)
            ->set(EventServiceProvider::KEY_MODEL_EVENT_REDISPATCH, true);
    }

    public function testGeocodingTriggered()
    {
        // expectsJobs _discards_ matching events
        // after _asserting_ they were dispatched within the test
        $this->expectsJobs(PerformGeocodingEnhancement::class);

        factory(Issue::class, 'withContactFields')->create();
    }

    public function testGeocodingFailsWithNoAddress()
    {
        // this time we'll allow the job to be dispatched but expect it to fail
        $this->expectException(VagueAddressException::class);

        factory(Issue::class, 'withVagueContact')->create();
    }

    public function testGeocodingReceivesCall()
    {
        Geocoder::shouldReceive('geocode')
            ->andReturn(new AddressCollection([new Address(
                null, null, '123', 'StreetName', '55555'
            )]));

        factory(Issue::class, 'withContactFields')->create();
    }
}
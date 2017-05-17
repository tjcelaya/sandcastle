<?php

namespace App\Jobs;

use App\Exceptions\VagueAddressException;
use App\Model\Contact;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Model\Address as AddressEntity;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use DomainException;
use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Log\Writer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\CountryRepository;


class PerformGeocodingEnhancement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Contact */
    private $contact;

    /** @var Geocoder */
    private $geocoder;

    /** @var Writer  */
    private $logger;

    const ADDRESS_KEYS = ['address', 'city', 'state', 'zip'];

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function handle()
    {
        $this->geocoder = app('geocoder');
        $this->logger = app('log');

        $shouldReverse = empty($this->contact->latitude) || empty($this->contact->longitude);

        $address = $shouldReverse
            ? $this->retrieveFromAddress()
            : $this->retrieveFromLatLong();
    }

    private function retrieveFromAddress(): Address
    {
        $fields = collect($this->contact->fields)
            ->only(self::ADDRESS_KEYS);

        if ($fields->count() < 2) {
            $e = new VagueAddressException('not enough fields to find a location, fields given: ' . $fields->keys()->toJson());
            $this->fail($e);
            throw $e;
        }

        return $this->geocoder->geocode($this->formatFields($fields))->first();
    }

    private function retrieveFromLatLong(): Address
    {
        dd('failed');
        return $this->geocoder->reverse($this->contact->latitude, $this->contact->latitude)->first();
    }

    private function formatFields(Collection $fields)
    {
        $addressFormatRepository = new AddressFormatRepository();
        $countryRepository = new CountryRepository();
        $subdivisionRepository = new SubdivisionRepository();
        $formatter = new DefaultFormatter(
            $addressFormatRepository,
            $countryRepository,
            $subdivisionRepository);

        $address = (new AddressEntity)
            ->withCountryCode('US')
            ->withPostalCode($fields->get('zip'))
            ->withAdministrativeArea($fields->get('state'))
            ->withLocality($fields->get('city'))
            ->withAddressLine1($fields->get('address'));

        return $formatter->format($address);
    }
}

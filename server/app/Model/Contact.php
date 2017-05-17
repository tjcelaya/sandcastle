<?php

namespace App\Model;

use App\Jobs\PerformGeocodingEnhancement;
use App\Model\Location;
use App\Model\Base;
use App\Providers\EventServiceProvider;
use Illuminate\Config\Repository;

/**
 * Class Contact
 *
 * @package App\Model
 *
 * @property string $latitude
 * @property string $longitude
 * @property array $fields
 */
class Contact extends Base
{
    protected $table = 'contact';

    public $timestamps = true;

    protected $fillable = [
        'fields',
        'latitude',
        'longitude',
        'image_url',
    ];

    protected $casts = [
        'fields' => 'array',
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public function contactable()
    {
        return $this->morphTo();
    }

    public function location() {
        return $this->morphOne(Location::class, 'locatable');
    }
}
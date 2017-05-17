<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 4/20/17
 * Time: 8:08 PM
 */

namespace App\Observer;

use App\Jobs\PerformGeocodingEnhancement;
use App\Model\Contact;
use App\Providers\EventServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Log\Writer;

class ContactSavedObserver
{
    public function created(Contact $contact)
    {
        $this->modified($contact);
    }

    public function updated(Contact $contact)
    {
        $this->modified($contact);
    }

    private function modified(Contact $contact)
    {
        $shouldPropagate = app(ConfigRepository::class)
            ->get(EventServiceProvider::KEY_MODEL_EVENT_REDISPATCH);

        if (!$shouldPropagate) {
            return;
        }

        if ($contact->doesntHave('location')) {
            dispatch(new PerformGeocodingEnhancement($contact));
        }
    }
}
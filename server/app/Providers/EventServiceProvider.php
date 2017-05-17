<?php

namespace App\Providers;

use App\Model\Contact;
use App\Observer\ContactSavedObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    const KEY_MODEL_EVENT_REDISPATCH = 'app.model_event_redispatch_enabled';

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // \Illuminate\Database\Events\QueryExecuted::class => [
        //     \App\Listeners\DBLoggingListener::class,
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Contact::observe(ContactSavedObserver::class);
    }
}

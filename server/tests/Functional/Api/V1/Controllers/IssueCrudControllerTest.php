<?php

namespace App\Functional\Api\V1\Controllers;

use App\Model\Contact;
use App\Model\Issue;
use App\Providers\EventServiceProvider;
use App\SingleAuthTestCase;
use Illuminate\Config\Repository;

class IssueCrudControllerTest extends SingleAuthTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->app->make(Repository::class)
            ->set(EventServiceProvider::KEY_MODEL_EVENT_REDISPATCH, false);

        /** @var Issue $issue */
        $issue = Issue::create([
            'name' => $this->faker->name,
            'notes' => implode(' ', $this->faker->words),
        ]);
        $contact = new Contact([
            'fields' => [
                'phone' => $this->faker->phoneNumber
            ]
        ]);
        $contact->contactable()->associate($issue);
        $contact->save();
    }

    public function testIssueCanBeRetrievedAsList()
    {
        $this->refreshAuthUser();
        $this->authenticate($this->email, $this->password);
        $this->get('api/issue', $this->headersWithAuth())
            ->assertJsonStructure([
            'total',
            'per_page',
            'prev_page_url',
            'next_page_url',
            'from',
            'to',
            'data' => [
                '*' => [
                    'name',
                    'notes',
                ]
            ]
        ]);
    }
}

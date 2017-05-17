<?php

namespace App\Functional\Api\V1\Controllers;

use App\AuthTestCase;
use App\Exceptions\DatabaseStateException;
use App\Model\Contact;
use App\Model\Issue;
use App\Model\Role;
use App\Model\User;
use App\Providers\EventServiceProvider;
use App\SingleAuthTestCase;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\DB;

class IssueManagementControllerTest extends SingleAuthTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->app->make(Repository::class)
            ->set(EventServiceProvider::KEY_MODEL_EVENT_REDISPATCH, false);

        $db = $this->app->make('db.connection');
        $db->affectingStatement('SET FOREIGN_KEY_CHECKS = 0');
        $db->affectingStatement('TRUNCATE ' . (new User)->roles()->getTable());
        $db->affectingStatement('TRUNCATE ' . User::databaseTable());
        $db->affectingStatement('SET FOREIGN_KEY_CHECKS = 1');

        $peers = factory(User::class)
            ->create()
            ->each(function (User $u) {
                $u->attachRole(Role::PEER);
            });

        factory(Issue::class, 20)
            ->create()
            ->each(function ($r) use ($peers) {
                foreach ([
                             'navs' => $peers,
                         ] as $relName => $things) {

//                    foreach (array_sample($things, rand(0, 4)) as $thing) {
//                        $assignTime = \Carbon\Carbon::now()->subMinutes(rand(4, 100000));
//
//                        // attach a thing, or attach it as "removed"
//                        if (coinflip()) {
//                            $r->{$relName}()->attach($thing, timestamps($assignTime), true);
//                        } else {
//                            $unassignTime = $assignTime->addMinutes(rand(4, 100000));
//                            $r->{$relName}()->attach($thing, timestamps($assignTime, $assignTime, $unassignTime), true);
//                        }
//                    }
                }

//                if (coinflip()) {
//                    $r->statuses()->save($completeStatus);
//                }
//
//                if (coinflip()) {
//                    $r->delete();
//                }
            });
    }

    public function testAssignedToUserFailsIfNotAuthed()
    {
        $this->get('api/user-issues')
            ->assertStatus(400)
            ->assertJson(['error' => 'token_not_provided']);
    }

    public function testAssignedToUserFailsWithAuthForWrongUser()
    {
        $this->authWithRole(Role::PEER);
        $this->get('api/user-issues', $this->headersWithAuth())
            ->assertStatus(403)
            ->assertJsonStructure(['error' => ['message', 'status_code']]);
    }

    public function testAssignedToUserSucceedsWithAuth()
    {
        $this->authWithRole(Role::ANALYST);
        $this->get('api/user-issues', $this->headersWithAuth())
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'discriminant' => 'user_id',
                    'aggregate_field' => 'issue_id',
                    'aggregate_type' => 'count',
                ],
                'data' => [],
            ]);
    }

    public function testAssignedToUserSucceedsWithAuthAndData()
    {
        $this->authWithRole(Role::ANALYST);
        $expectedAggregateField = 'issue_id';
        $expectedAggregateType = 'count';
        $this->get('api/user-issues', $this->headersWithAuth())
            ->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'discriminant' => 'user_id',
                    'aggregate_field' => $expectedAggregateField,
                    'aggregate_type' => $expectedAggregateType,
                    'aggregate_list_contents' => true
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'user_id',
                        report_json_field('issue_id', 'count'),
                        report_json_field('issue_id', 'list'),
                    ]
                ],
            ]);
    }

}

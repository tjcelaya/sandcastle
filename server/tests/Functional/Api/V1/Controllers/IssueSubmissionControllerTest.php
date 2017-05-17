<?php

namespace App\Functional\Api\V1\Controllers;

use App\Model\Issue;
use App\Model\User;
use App\TestCase;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IssueSubmissionControllerTest extends TestCase
{
    // use DatabaseTransactions;

    public function testIssueValidation()
    {
        $f = \Faker\Factory::create();
        $this->post('api/issue', [
            'name' => $f->name,
            'notes' => implode(' ', $f->words),
            'contact' => [
                'fields' => [
                    'phone' => $f->phoneNumber
                ]
            ],
        ])->assertStatus(201);

        $this->assertGreaterThan(0, Issue::on()->count(), 'Issue was not created?');
    }
}

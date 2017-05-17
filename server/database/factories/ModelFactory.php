<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Model\Contact;
use App\Model\Issue;
use App\Model\User;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(Contact::class, 'forIssue', function (Faker\Generator $faker) {
    return [
        'fields' => [
            'address' => $faker->address,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode
        ],
        'contactable_type' => Issue::class,
    ];
});


$factory->defineAs(Contact::class, 'forIssueVague', function (Faker\Generator $faker) {
    return [
        'fields' => [
            'city' => $faker->city,
        ],
        'contactable_type' => Issue::class,
    ];
});

$factory->define(Issue::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'notes' => implode(' ', $faker->words),
    ];
});

$factory->defineAs(Issue::class,  'withVagueContact', function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'notes' => implode(' ', $faker->words),
        'contact_id' => function (array $issue) {
            return factory(Contact::class, 'forIssueVague')->create()->id;
        }
    ];
});

$factory->defineAs(Issue::class,  'withContactFields', function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'notes' => implode(' ', $faker->words),
        'contact_id' => function (array $issue) {
            return factory(Contact::class, 'forIssue')->create()->id;
        }
    ];
});
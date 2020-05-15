<?php

use App\Models\SalesMember;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(SalesMember::class, function (Faker $faker) {
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $skypeId = $firstName.'_'.$lastName.'_'. $faker->randomNumber(3);

    return [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $faker->unique()->safeEmail,
        'contact_number' => $faker->e164PhoneNumber,
        'skype_id' => $skypeId
    ];
});

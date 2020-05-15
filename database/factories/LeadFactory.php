<?php

use App\Models\Lead;
use App\Models\Form;
use App\Models\User;
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



$factory->define(Lead::class, function (Faker $faker) {

    $interested = $faker->randomElement(['interested', 'not_interested', 'unreachable']);

    return [
        'interested' => function (array $lead) use($interested) {
            return $lead['status'] == 'actioned' ? $interested : NULL;
        },
        'appointment_booked' => function (array $lead) use($interested) {
            return $lead['status'] == 'actioned' ? rand(0, 1) : 0;
        },
        'time_taken' => function (array $lead) use($faker) {
            return $lead['status'] == 'actioned' ? $faker->numberBetween(180, 300) : NULL;
        },
        
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Cases::class, function (Faker $faker) {
    return [
        'title'        => 'BO ' . $faker->name,
        'client_email' => $faker->unique()->safeEmail,
        'website'      => $faker->domainName,
        'country'      => $faker->country,
        'user_id'      => null,
    ];
});

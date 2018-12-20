<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Cards::class, function (Faker $faker) {
    return [
        'name'        => $faker->name,
        'last_number' => $faker->randomNumber(4),
        'total_value' => $faker->randomNumber(),
        'card_type'   => \App\Models\Cards::TYPE_CREDIT,
        'close_date'  => $faker->date(),
        'case_id'     => \App\Models\Cases::all()->random()->id,
    ];
});

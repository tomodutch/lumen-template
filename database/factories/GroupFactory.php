<?php

$factory->define(\App\Group::class, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});
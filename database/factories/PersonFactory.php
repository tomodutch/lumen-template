<?php

$factory->define(\App\Person::class, function(\Faker\Generator $faker) {
    return [
                                                                                    'title' => $faker->word,
                                                                                    'body' => $faker->word,
                                                                                    'is_featured' => $faker->boolean,
                                        ];
});
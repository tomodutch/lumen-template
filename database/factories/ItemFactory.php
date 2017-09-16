<?php

$factory->define(\App\Item::class, function(\Faker\Generator $faker) {
    return [
                                                                                    'title' => $faker->word,
                                                                                    'body' => $faker->word,
                                                                                    'is_featured' => $faker->boolean,
                                        ];
});
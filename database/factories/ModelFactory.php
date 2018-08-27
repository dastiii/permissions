<?php

$factory->define(\dastiii\Permissions\Models\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => strtolower($faker->unique()->word.'.'.$faker->word),
        'human_readable_name' => implode(' ', $faker->words),
        'description' => $faker->text,
        'is_backend' => $faker->boolean
    ];
});

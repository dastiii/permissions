<?php

$factory->define(dastiii\Permissions\Test\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\dastiii\Permissions\Models\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => strtolower($faker->unique()->word.'.'.$faker->word),
        'display_name' => implode(' ', $faker->words),
        'description' => $faker->text,
        'is_backend' => $faker->boolean,
    ];
});

$factory->define(\dastiii\Permissions\Models\Group::class, function (Faker\Generator $faker) {
    return [
        'name' => ucfirst($faker->unique()->word),
        'description' => $faker->text,
    ];
});

$factory->define(\dastiii\Permissions\Models\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => ucfirst($faker->unique()->word),
        'weight' => $faker->randomDigit,
        'description' => $faker->text,
        'is_default' => $faker->boolean,
    ];
});

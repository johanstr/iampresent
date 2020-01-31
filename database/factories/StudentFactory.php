<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Student;
use Faker\Generator as Faker;

$factory->define(Student::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'student_number' => $faker->randomNumber(7,false),
        'group_id' => $faker->numberBetween(0,5),
        'card_one_uid' => $faker->isbn10,
        'card_two_uid' => $faker->isbn10,
        'photo' => 'https://ao-alfa.org/img/default-student.png'
    ];
});

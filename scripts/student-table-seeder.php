#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Student;

$faker = Faker\Factory::create('ru_RU');
$app = require __DIR__ . '/../src/app.php';

/** @var \App\CookieAuthService $authService */
$authService = $app['authService'];

$studentsCount = $argv[1] ?? 5;
for ($i = 0; $i < $studentsCount; $i++) {
    $gender = $faker->randomElement([Student::GENDER_FEMALE, Student::GENDER_MALE]);

    if ($gender === Student::GENDER_FEMALE) {
        $name    = $faker->firstNameFemale();
        $surname = $faker->lastName() . 'a';
    } else {
        $name    = $faker->firstNameMale();
        $surname = $faker->lastName();
    }

    $student = new Student([
        'name'    => $name,
        'surname' => $surname,
        'gender'  => $gender,
        'email'   => $faker->email,
        'rating'  => $faker->numberBetween(Student::RATING_MIN, Student::RATING_MAX),
        'group'   => $faker->regexify('[a-zA-Z]{1,3}\d{1,2}')
    ]);

    $authorizedStudent = $authService->registerStudent($student);
    echo
        $authorizedStudent->getName() . ' ' .
        $authorizedStudent->getToken() . ' ' .
        $authorizedStudent->getEmail(), PHP_EOL;
}

#!/usr/bin/env php
<?php

use App\Entity\Student;

require_once __DIR__ . '/../vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');
$config = require __DIR__ . '/../config/config_app.php';
$app = require __DIR__ . '/../src/app.php';

/** @var \App\Service\CookieAuthService $authService */
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
        'group'   => $faker->regexify('[a-zA-Z]{1,3}\d{1,2}'),
    ]);

    $authService->registerStudent($student);
    echo
        $student->getName() . ' ' .
        $student->getToken() . ' ' .
        $student->getEmail(), PHP_EOL;
}

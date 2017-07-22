<?php

declare(strict_types=1);

namespace Tests;

use App\Entity\Student;

trait StudentTestTrait
{
    protected function getNonexistentStudent(): Student
    {
        return new Student([
            'name' => 'Иван',
            'surname' => 'Иванов',
            'email' => 'nonexistent@mail.ru',
            'gender' => Student::GENDER_MALE,
            'rating' => 1,
            'group' => 'ИС25',
        ]);
    }
}
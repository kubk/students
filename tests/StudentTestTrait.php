<?php

declare(strict_types=1);

namespace Tests;

use App\Student;

trait StudentTestTrait
{
    protected function getNonexistentStudentArray(): array
    {
        return [
            'name' => '',
            'surname' => '',
            'email' => 'nonexistent@mail.ru',
            'gender' => Student::GENDER_MALE,
            'rating' => 1,
            'group' => '',
        ];
    }
}
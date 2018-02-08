<?php

declare(strict_types=1);

namespace App\Validation;

use Symfony\Component\Validator\Constraint;

class StudentEmailUnique extends Constraint
{
    public function getErrorMessage()
    {
        return 'Почта "%email%" уже занята другим пользователем';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

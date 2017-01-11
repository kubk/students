<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Validator\Constraint;

class UniqueEmail extends Constraint
{
    public function getErrorMessage() {
        return 'Почта "%email%" уже занята другим пользователем';
    }
}
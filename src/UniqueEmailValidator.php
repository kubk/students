<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
    /**
     * @var StudentGateway
     */
    private $studentGateway;

    public function __construct(StudentGateway $studentGateway)
    {
        $this->studentGateway = $studentGateway;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($student = $this->studentGateway->findByEmail($value)) {
            // Если пользователь обновляет свои данные, не меняя email, то violation не создаётся
            if ($student->getToken() !== $this->context->getObject()->getToken()) {
                $this->context->buildViolation($constraint->getErrorMessage())
                    ->setParameter('%email%', $value)
                    ->addViolation();
            }
        }
    }
}
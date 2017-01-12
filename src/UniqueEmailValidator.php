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
        $studentInDb = $this->studentGateway->findByEmail($value->getEmail());

        if ($studentInDb && $studentInDb->getToken() !== $value->getToken()) {
            $this->context->buildViolation($constraint->getErrorMessage())
                ->atPath('email')
                ->setParameter('%email%', $value->getEmail())
                ->addViolation();
        }
    }
}
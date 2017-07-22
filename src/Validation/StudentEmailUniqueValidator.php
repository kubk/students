<?php

declare(strict_types=1);

namespace App\Validation;

use App\Service\StudentGateway;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StudentEmailUniqueValidator extends ConstraintValidator
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

        if ($studentInDb && !$studentInDb->isEqualTo($value)) {
            $this->context->buildViolation($constraint->getErrorMessage())
                ->atPath('email')
                ->setParameter('%email%', $value->getEmail())
                ->addViolation();
        }
    }
}

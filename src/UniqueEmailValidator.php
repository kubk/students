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

    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(StudentGateway $studentGateway, AuthService $authService)
    {
        $this->studentGateway = $studentGateway;
        $this->authService    = $authService;
    }

    public function validate($value, Constraint $constraint)
    {
        $studentInDb = $this->studentGateway->findByEmail($value->getEmail());

        if ($studentInDb && !$this->authService->studentsAreTheSame($studentInDb, $value)) {
            $this->context->buildViolation($constraint->getErrorMessage())
                ->atPath('email')
                ->setParameter('%email%', $value->getEmail())
                ->addViolation();
        }
    }
}
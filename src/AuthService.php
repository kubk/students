<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\{ParameterBag, Cookie, ResponseHeaderBag};

class AuthService
{
    /**
     * @var StudentGateway
     */
    private $gateway;

    public function __construct(StudentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function getRegisteredStudent(ParameterBag $parameterBag)
    {
        $token = (string) $parameterBag->get('token');
        return $this->gateway->findByToken($token);
    }

    public function registerStudent(Student $student): Student
    {
        $token = $this->generateRandomToken();
        $student->setToken($token);
        $this->gateway->save($student);
        return $student;
    }

    private function generateRandomToken(): string
    {
        $chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        shuffle($chars);
        return join('', $chars);
    }

    public function rememberStudent(Student $student, ResponseHeaderBag $headers): ResponseHeaderBag
    {
        if (!$this->isStudentRegistered($student)) {
            throw new \LogicException('Attempt to remember not registered student');
        }

        $cookie = new Cookie('token', $student->getToken(), strtotime('+10 years'));
        $headers->setCookie($cookie);
        return $headers;
    }

    public function isStudentRegistered(Student $student): bool
    {
        return !! $student->getToken();
    }

    public function studentsAreTheSame(Student $studentA, Student $studentB): bool
    {
        return $studentA->getToken() === $studentB->getToken();
    }

    public function unregister(ResponseHeaderBag $headers): ResponseHeaderBag
    {
        $headers->clearCookie('token');
        return $headers;
    }
}

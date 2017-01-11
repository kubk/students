<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\{Request, Response, Cookie};

class AuthService
{
    /**
     * @var StudentGateway
     */
    private $gateway;
    private $tokenKey;

    public function __construct(StudentGateway $gateway)
    {
        $this->gateway  = $gateway;
        $this->tokenKey = 'token';
    }

    public function getRegisteredStudent(Request $request)
    {
        $token = $request->cookies->get($this->tokenKey);
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
        // 32 символа из диапазона [a-zA-Z0-9]
        $chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        shuffle($chars);
        return join('', $chars);
    }

    public function rememberStudent(Student $student, Response $response): Response
    {
        if (!$this->isStudentRegistered($student)) {
            throw new \LogicException('Attempt to remember not registered student');
        }

        $cookie = new Cookie($this->tokenKey, $student->getToken(), strtotime('+10 years'));
        $response->headers->setCookie($cookie);
        return $response;
    }

    public function isStudentRegistered(Student $student): bool
    {
        return !! $student->getToken();
    }

    public function unregister(Response $response): Response
    {
        $response->headers->clearCookie($this->tokenKey);
        return $response;
    }

    public function getTokenKey(): string
    {
        return $this->tokenKey;
    }
}
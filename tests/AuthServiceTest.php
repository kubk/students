<?php

declare(strict_types=1);

namespace Tests;

use App\Student;
use App\AuthService;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;

class AuthServiceTest extends StudentTestCase
{
    /**
     * @var AuthService
     */
    private $authService;

    public function setUp()
    {
        $this->authService = new AuthService($this->getStudentGatewayMock());
    }

    public function testServiceReturnsRegisteredUser()
    {
        $authService = $this->authService;
        $requestWithExistingInDbToken = $this->getRequestWithCookieToken('existing_token');
        $requestWithNonExistentInDbToken = $this->getRequestWithCookieToken('nonexistent_token');
        $this->assertInstanceOf(Student::class, $authService->getRegisteredStudent($requestWithExistingInDbToken));
        $this->assertFalse($authService->getRegisteredStudent($requestWithNonExistentInDbToken) instanceof Student);
    }

    private function getRequestWithCookieToken($token): Request
    {
        $request = new Request();
        $tokenKey = $this->authService->getTokenKey();
        $request->cookies = new ParameterBag([$tokenKey => $token]);
        return $request;
    }

    public function testRegisterStudent()
    {
        $authService = $this->authService;
        $student = $this->createMock(Student::class);
        $student->expects($this->once())->method('setToken');
        $student = $authService->registerStudent($student);
        $this->assertInstanceOf(Student::class, $student);
    }

    public function testRememberStudent()
    {
        $authService = $this->authService;
        $student = $this->createMock(Student::class);
        $token = 'registration_token';
        $student->method('getToken')->willReturn($token);
        $response = $authService->rememberStudent($student, new Response());
        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue(
            $this->assertResponseContainCookieWithKeyValue($response, $authService->getTokenKey(), $token)
        );
    }

    private function assertResponseContainCookieWithKeyValue(Response $response, $key, $value): bool
    {
        $cookies = $response->headers->getCookies();

        foreach ($cookies as $cookie) {
            /** @var Cookie $cookie */
            if ($cookie->getName() === $key && $cookie->getValue() === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @expectedException \LogicException
     */
    public function testRememberNotRegisteredStudentThrowsException()
    {
        $authService = $this->authService;
        $student = $this->createMock(Student::class);
        $student->method('getToken')->willReturn(null);
        $authService->rememberStudent($student, new Response());
    }

    public function testUnregister()
    {
        $authService = $this->authService;
        $cookie = new Cookie($authService->getTokenKey(), 'secret_token', strtotime('+10 years'));
        $response = new Response();
        $response->headers->setCookie($cookie);
        $response = $authService->unregister($response);
        $this->assertTrue(
            $this->assertCookieTokenHasExpiryDateInThePast($response, $authService->getTokenKey())
        );
    }

    private function assertCookieTokenHasExpiryDateInThePast(Response $response, $key): bool
    {
        $cookies = $response->headers->getCookies();

        foreach ($cookies as $cookie) {
            /** @var Cookie $cookie */
            if ($cookie->getName() === $key) {
                return $cookie->getExpiresTime() < time();
            }
        }

        return false;
    }
}

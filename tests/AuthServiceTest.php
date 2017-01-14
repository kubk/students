<?php

declare(strict_types=1);

namespace Tests;

use App\Student;
use App\AuthService;
use App\StudentGateway;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AuthServiceTest extends TestCase
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function setUp()
    {
        $app = require __DIR__ . '/../src/app.php';
        $this->pdo = $app['pdo'];
        $this->pdo->beginTransaction();
        $studentGateway = new StudentGateway($this->pdo);
        $this->authService = new AuthService($studentGateway);
    }

    public function testServiceDoesNotReturnStudentForEmptyParameterBag()
    {
        $parameterBag = new ParameterBag();
        $result = $this->authService->getRegisteredStudent($parameterBag);
        $this->assertFalse($result instanceof Student);
    }

    public function testLoopback()
    {
        $authService = $this->authService;
        $student = new Student($this->getNonexistentStudentArray());
        $this->assertFalse($authService->isStudentRegistered($student));

        $registeredStudent = $authService->registerStudent($student);
        $this->assertTrue($authService->isStudentRegistered($registeredStudent));

        $rememberedStudentHeaders = $authService->rememberStudent($registeredStudent, new ResponseHeaderBag());
        $parameterBag = $this->convertResponseHeaderBagToParameterBag($rememberedStudentHeaders);
        $this->assertInstanceOf(Student::class, $authService->getRegisteredStudent($parameterBag));

        $unregisteredStudentHeaders = $authService->unregister($rememberedStudentHeaders);
        $parameterBag = $this->convertResponseHeaderBagToParameterBag($unregisteredStudentHeaders);
        $this->assertFalse($authService->getRegisteredStudent($parameterBag) instanceof Student);
    }

    private function getNonexistentStudentArray(): array
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

    private function convertResponseHeaderBagToParameterBag(ResponseHeaderBag $responseHeaderBag): ParameterBag
    {
        $parameterBag = new ParameterBag();

        foreach ($responseHeaderBag->getCookies() as $cookie) {
            $parameterBag->set($cookie->getName(), $cookie->getValue());
        }

        return $parameterBag;
    }

    public function tearDown()
    {
        $this->pdo->rollBack();
    }
}

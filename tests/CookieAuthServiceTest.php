<?php

declare(strict_types=1);

namespace Tests;

use App\Entity\Student;
use PHPUnit\Framework\TestCase;
use App\Service\{StudentGateway, CookieAuthService};
use Symfony\Component\HttpFoundation\{ParameterBag, ResponseHeaderBag};

class CookieAuthServiceTest extends TestCase
{
    use StudentTestTrait;

    /**
     * @var CookieAuthService
     */
    private $authService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function setUp()
    {
        $config = require __DIR__ . '/../config/config_tests.php';
        $app = require __DIR__ . '/../src/app.php';
        $this->pdo = $app['pdo'];
        $this->pdo->beginTransaction();
        $this->pdo->exec(file_get_contents(__DIR__ . '/../create-students-table.sql'));
        $studentGateway = new StudentGateway($this->pdo);
        $this->authService = new CookieAuthService($studentGateway);
    }

    public function testServiceDoesNotReturnStudentForEmptyParameterBag()
    {
        $cookieBag = new ParameterBag();
        $result = $this->authService->getRegisteredStudent($cookieBag);
        $this->assertNull($result);
    }

    public function testLoopback()
    {
        $authService = $this->authService;
        $student = $this->getNonexistentStudent();
        $this->assertFalse($authService->isStudentRegistered($student));

        $authService->registerStudent($student);
        $this->assertTrue($authService->isStudentRegistered($student));

        $responseHeaderBag = new ResponseHeaderBag();
        $authService->rememberStudent($student, $responseHeaderBag);
        $cookieBag = $this->convertResponseHeaderBagToParameterBag($responseHeaderBag);
        $this->assertInstanceOf(Student::class, $authService->getRegisteredStudent($cookieBag));

        $authService->logOut($responseHeaderBag);
        $cookieBag = $this->convertResponseHeaderBagToParameterBag($responseHeaderBag);
        $this->assertNull($authService->getRegisteredStudent($cookieBag));
    }

    private function convertResponseHeaderBagToParameterBag(ResponseHeaderBag $responseHeaderBag): ParameterBag
    {
        $cookieBag = new ParameterBag();

        foreach ($responseHeaderBag->getCookies() as $cookie) {
            $cookieBag->set($cookie->getName(), $cookie->getValue());
        }

        return $cookieBag;
    }

    public function tearDown()
    {
        $this->pdo->rollBack();
    }
}

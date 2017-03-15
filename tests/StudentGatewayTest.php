<?php

declare(strict_types=1);

namespace Tests;

use App\Student;
use App\StudentGateway;

// https://phpunit.de/manual/current/en/database.html
// Tip: Use your own Abstract Database TestCase
class StudentGatewayTest extends \PHPUnit_Extensions_Database_TestCase
{
    use StudentTestTrait;

    /**
     * @var StudentGateway
     */
    private $studentGateway;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $connection;

    public function setUp()
    {
        $config = require __DIR__ . '/../config/config_tests.php';
        $app = require __DIR__ . '/../src/app.php';
        $this->pdo = $app['pdo'];
        $this->pdo->beginTransaction();
        $this->studentGateway = new StudentGateway($this->pdo);
        $this->connection = $this->createDefaultDBConnection($this->pdo);
        parent::setUp();
    }

    protected function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/fixtures/students-seed.xml');
    }

    protected function getConnection(): \PHPUnit_Extensions_Database_DB_IDatabaseConnection
    {
        $this->pdo->exec(file_get_contents(__DIR__ . '/../create-students-table.sql'));
        return $this->connection;
    }

    public function testCount()
    {
        $this->assertEquals(4, $this->studentGateway->count());
    }

    public function testFindBy()
    {
        $studentGateway = $this->studentGateway;
        $this->assertInstanceOf(Student::class, $studentGateway->findByEmail('ivan@mail.u'));
        $this->assertInstanceOf(Student::class, $studentGateway->findByToken('affd33434b'));
        $this->assertNull($studentGateway->findByEmail('nonexistent@mail.u'));
        $this->assertNull($studentGateway->findByToken('nonexistent_token'));
    }

    public function testGatewayAddsNewStudent()
    {
        $studentCount = $this->studentGateway->count();
        $student = $this->getNonexistentStudent();
        $student->setToken('token');
        $this->studentGateway->save($student);
        $this->assertSame($studentCount + 1, $this->studentGateway->count());
    }

    public function testGatewayUpdatesExistingStudent()
    {
        $studentGateway = $this->studentGateway;
        $student = $studentGateway->findByEmail('ivan@mail.u');
        $student->setRating(100);
        $studentGateway->save($student);
        $student = $studentGateway->findByEmail('ivan@mail.u');
        $this->assertEquals(100, $student->getRating());
    }

    /**
     * @dataProvider searchProvider
     */
    public function testFindAllWith($search, $column, $order, $offset, $limit, $ids)
    {
        $studentGateway = $this->studentGateway;
        $students = $studentGateway->findAllWith($search, $column, $order, $offset, $limit);
        $studentsIds = array_map(function ($s) { return $s->getId(); }, $students);
        $this->assertEquals($studentsIds, $ids);
    }

    public function searchProvider()
    {
        return [
            'Поиск регистронезависимый, учитываются имя, фамилия, группа' => ['и', 'name', 'ASC', 0, 100, [1, 3, 4]],
            ['', 'name', 'ASC', 0, 100, [1, 3, 4, 2]],
            ['', 'name', 'DESC', 1, 2, [4, 3]],
            ['Несуществующий студент', 'name', 'ASC', 0, 100, []],
            ['', 'invalid_column', 'invalid_order', 0, 100, [1, 2, 3, 4]],
        ];
    }

    public function tearDown()
    {
        $this->pdo->rollBack();
    }
}
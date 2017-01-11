<?php

declare(strict_types=1);

namespace Tests;

use App\Student;
use App\StudentGateway;
use PHPUnit\Framework\TestCase;

class StudentTestCase extends TestCase
{
    /**
     * @return StudentGateway
     */
    protected function getStudentGatewayMock(): StudentGateway
    {
        $registeredStudent = $this->createMock(Student::class);
        $registeredStudent->method('getToken')->willReturn('existing_token');

        $studentGateway = $this->createMock(StudentGateway::class);
        // https://phpunit.de/manual/current/en/test-doubles.html#test-doubles.stubs.examples.StubTest5.php
        $findByTokenValueMap = [
            ['existing_token', $registeredStudent],
            ['nonexistent_token', null],
        ];
        $studentGateway->method('findByToken')
            ->will($this->returnValueMap($findByTokenValueMap));

        $findByEmailValueMap = [
            ['existing@mail.ru', $registeredStudent],
            ['nonexistent@mail.ru', null],
        ];
        $studentGateway->method('findByEmail')
            ->will($this->returnValueMap($findByEmailValueMap));

        return $studentGateway;
    }
}
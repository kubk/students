<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Student;

class StudentGateway
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Student $student)
    {
        $preparedStatementsParams = [
            ':name' => $student->getName(),
            ':surname' => $student->getSurname(),
            ':email' => $student->getEmail(),
            ':gender' => $student->getGender(),
            ':rating' => $student->getRating(),
            ':group' => $student->getGroup(),
        ];

        if (null === $student->getId()) {
            $query = $this->pdo->prepare('
                INSERT INTO students (name, surname, email, gender, rating, "group", token)
                VALUES (:name, :surname, :email, :gender, :rating, :group, :token)
            ');

            $query->execute(array_merge($preparedStatementsParams, [':token' => $student->getToken()]));
        } else {
            $query = $this->pdo->prepare('
                UPDATE students
                SET name = :name,
                    surname = :surname,
                    email = :email,
                    gender = :gender,
                    rating = :rating,
                    "group" = :group
                WHERE id = :id
            ');

            $query->execute(array_merge($preparedStatementsParams, [':id' => $student->getId()]));
        }
    }

    public function findAllWith(string $search = '', string $column, string $order, int $offset, int $limit)
    {
        if (!in_array($column, ['name', 'surname', 'group', 'rating', 'gender'])) {
            $column = 'id';
        }

        if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
            $order = 'ASC';
        }

        $query = $this->pdo->prepare("
            SELECT *
            FROM students
            WHERE CONCAT(name, ' ', surname, ' ', \"group\", ' ', rating) ILIKE :search
            ORDER BY \"{$column}\" {$order}
            LIMIT :limit OFFSET :offset
        ");

        $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $query->bindValue(':search', "%{$search}%");
        $query->execute();
        $rows = $query->fetchAll();

        return array_map([$this, 'mapRowToObject'], $rows);
    }

    public function count(string $search = ''): int
    {
        $query = $this->pdo->prepare("
            SELECT COUNT(id)
            FROM students
            WHERE CONCAT(name, ' ', surname, ' ', \"group\", ' ', rating) ILIKE :search
        ");
        $query->execute([':search' => "%{$search}%"]);

        return (int) $query->fetch(\PDO::FETCH_COLUMN);
    }

    public function findByEmail(string $email)
    {
        return $this->findByColumn('email', $email);
    }

    public function findByToken($token)
    {
        return $this->findByColumn('token', $token);
    }

    private function findByColumn(string $column, $columnValue)
    {
        $sql   = "SELECT * FROM students WHERE {$column} = :columnValue";
        $query = $this->pdo->prepare($sql);
        $query->execute([':columnValue' => $columnValue]);

        return ($row = $query->fetch())
            ? $this->mapRowToObject($row)
            : null;
    }

    private function mapRowToObject(array $row): Student
    {
        return new Student([
            'id' => $row['id'],
            'name' => $row['name'],
            'surname' => $row['surname'],
            'email' => $row['email'],
            'group' => $row['group'],
            'rating' => $row['rating'],
            'token' => $row['token'],
            'gender' => $row['gender'],
        ]);
    }
}


<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Constraint;

class Student
{
    const GENDER_FEMALE = 'f';
    const GENDER_MALE = 'm';
    const RATING_MIN = 1;
    const RATING_MAX = 200;

    private $id;
    private $name;
    private $surname;
    private $email;
    private $gender;
    private $group;
    private $rating;
    private $token;

    public function __construct(array $studentArray = [])
    {
        foreach ($studentArray as $property => $value) {
            if (property_exists(self::class, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // http://symfony.com/doc/current/reference/constraints/Regex.html
        // Нужно вручную указывать htmlPattern для регулярных выражений, содержащих флаг
        $metadata->addPropertyConstraints('name', [
            new Constraint\NotNull(),
            new Constraint\Regex([
                'pattern' => "~^[-а-яёa-zА-ЯЁA-Z\\s]{1,20}$~u",
                'htmlPattern' => "[-а-яёa-zА-ЯЁA-Z\\s]{1,20}",
                'message' => 'Имя может содержать только кириллицу, латиницу, дефисы, пробелы и должно быть не длиннее 20-и символов'
            ]),
        ]);

        $metadata->addPropertyConstraints('surname', [
            new Constraint\NotNull(),
            new Constraint\Regex([
                'pattern' => "~^[-'а-яёa-zА-ЯЁA-Z\\s]{1,20}$~u",
                'htmlPattern' => "[-'а-яёa-zА-ЯЁA-Z\\s]{1,20}",
                'message' => 'Фамилия может содержать только кириллицу, латиницу, дефисы, апострофы, пробелы и должна быть не длиннее 20-и символов'
            ])
        ]);

        $metadata->addPropertyConstraints('group', [
            new Constraint\NotBlank(),
            new Constraint\Regex([
                'pattern' => "~^[а-яёa-zА-ЯЁA-Z0-9]{2,5}$~u",
                'htmlPattern' => "[а-яёa-zА-ЯЁA-Z0-9]{2,5}",
                'message' => 'Группа должна содержать от 2 до 5 цифр или букв',
            ]),
        ]);

        $metadata->addPropertyConstraint('rating', new Constraint\Range([
            'min' => Student::RATING_MIN,
            'max' => Student::RATING_MAX,
            'minMessage' => 'Минимальная длина номера группы - {{ limit }}',
            'maxMessage' => 'Максимальная длина номера группы - {{ limit }}',
        ]));

        $metadata->addPropertyConstraint('gender', new Constraint\Choice([
            Student::GENDER_MALE, Student::GENDER_FEMALE
        ]));

        $metadata->addPropertyConstraints('email', [
            new Constraint\NotBlank([
                'message' => 'Почта обязательна для заполнения'
            ]),
            // Constraint\Email генерирует input типа email, который считает невалидной почту c кириллицей (иван@почта.рф)
            new Constraint\Regex(['pattern' => '/^[^@]+@[^@]+$/']),
            new Constraint\Length(['max' => 60, 'maxMessage' => 'Максимальная длина почты - {{ limit }}']),
        ]);
        $metadata->addConstraint(new StudentEmailUnique());
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
}

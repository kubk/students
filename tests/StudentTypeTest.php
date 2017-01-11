<?php

declare(strict_types=1);

namespace Tests;

use App\Student;
use App\StudentType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;

class StudentTypeTest extends StudentTestCase
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    public function setUp()
    {
        $app = require __DIR__ . '/../src/app.php';
        $this->formFactory = $app['form.factory'];
        $app['studentGateway'] = $this->getStudentGatewayMock();
    }

    /**
     * @dataProvider getFormData
     * @param array $studentData Данные, пришедшие в форму от пользователя
     * @param array $fieldsWithErrors Список полей, которые должны содержать ошибку
     * @param Student $student Студент, может иметь или не иметь токен
     */
    public function testFormIsValid(array $studentData, array $fieldsWithErrors, Student $student)
    {
        $options = ['csrf_protection' => false];
        /** @var Form $form */
        $form = $this->formFactory->createBuilder(StudentType::class, $student, $options)->getForm();
        $form->submit($studentData);
        $this->assertTrue($form->isSynchronized());
        /** @var FormError $formError */
        $formErrors = $form->getErrors(true/* Включать ошибки дочерних форм */);
        $this->assertCount(count($fieldsWithErrors), $formErrors);

        foreach ($fieldsWithErrors as $fieldWithError) {
            $formErrorIterator = $form->get($fieldWithError)->getErrors();
            $this->assertGreaterThanOrEqual(1, count($formErrorIterator));
        }
    }

    public function getFormData()
    {
        return [
            // https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html
            // Example 2.6: Using a data provider with named datasets
            [
                'Пустой email' => [
                    'name' => 'Петя',
                    'surname' => 'Петухов',
                    'email' => 'a',
                    'gender' => Student::GENDER_MALE,
                    'rating' => 23,
                    'group' => 'it25',
                ],
                'Поля с ошибкой' => ['email'],
                new Student(),
            ],
            [
                'Валидно только поле email' => [
                    'name' => '',
                    'surname' => '',
                    'email' => 'valid@mail.ru',
                    'gender' => 'invalid gender',
                    'rating' => 'invalid rating',
                    'group' => 'too long group name',
                ],
                'Поля с ошибкой' => ['name', 'surname', 'gender', 'rating', 'gender'],
                new Student(),
            ],
            [
                'Почта с кириллицей не вызовет ошибку, слишком большой рейтинг - вызовет' => [
                    'name' => 'Иван',
                    'surname' => 'Римский-Корсаков',
                    'email' => 'иван@почта.рф',
                    'gender' => Student::GENDER_FEMALE,
                    'rating' => 900000,
                    'group' => 'it25',
                ],
                'Поля с ошибкой' => ['rating'],
                new Student(),
            ],
            [
                'Всё верно, но email занят' => [
                    'name' => 'Петя',
                    'surname' => "O'Генри",
                    'email' => 'existing@mail.ru',
                    'gender' => Student::GENDER_MALE,
                    'rating' => 13,
                    'group' => 'it25',
                ],
                'Поля с ошибкой' => ['email'],
                new Student(),
            ],
            [
                'Всё верно' => [
                    'name' => 'Петя',
                    'surname' => 'Петухов',
                    'email' => 'nonexistent@mail.ru',
                    'gender' => Student::GENDER_MALE,
                    'rating' => 23,
                    'group' => 'it25',
                ],
                'Поля с ошибкой' => [],
                new Student(),
            ],
            [
                'Зарегистрированный пользователь обновляет свои данные, поэтому существующий email не вызывает ошибки' => [
                    'name' => 'Цзынь',
                    'surname' => 'Ю',
                    'email' => 'existing@mail.ru',
                    'gender' => Student::GENDER_MALE,
                    'rating' => 150,
                    'group' => 'it25',
                ],
                'Поля с ошибкой' => [],
                new Student(['token' => 'existing_token']),
            ],
            [
                'Зарегистрированный пользователь обновляет свои данные, но указывает занятый кем-то email' => [
                    'name' => 'Петя',
                    'surname' => 'Петухов',
                    'email' => 'existing@mail.ru',
                    'gender' => Student::GENDER_MALE,
                    'rating' => 99,
                    'group' => 'it25',
                ],
                'Поля с ошибкой' => ['email'],
                new Student(['token' => 'another_token']),
            ],
        ];
    }
}

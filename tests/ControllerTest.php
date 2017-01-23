<?php

declare(strict_types=1);

namespace Tests;

use App\Student;
use Silex\WebTestCase;

// http://silex.sensiolabs.org/doc/2.0/testing.html#webtestcase
class ControllerTest extends WebTestCase
{
    /**
     * @dataProvider studentDataProvider
     */
    public function testForm($name, $surname, $email, $gender, $group, $rating, $fieldsWithError)
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/form');

        $formPage = new FormPageObject($crawler);
        $formPage->typeName($name);
        $formPage->typeSurname($surname);
        $formPage->typeEmail($email);
        $formPage->typeRating($rating);
        $formPage->typeGender($gender);
        $formPage->typeGroup($group);

        if (!$fieldsWithError) {
            $formPage->submitForm($client);
            $this->assertTrue($client->getResponse()->isRedirect());
            $client->request('GET', '/logout');
            // Удостоверимся, что со старыми данными зарегистрироваться не выйдет, так как email занят
            $formPage->submitForm($client);
            $this->assertFalse($client->getResponse()->isRedirect());
        } else {
            $formWithErrorCrawler = $formPage->submitForm($client);
            $formPage = new FormPageObject($formWithErrorCrawler);
            $this->assertTrue($formPage->checkFormHasErrorsInFields($fieldsWithError));
            $this->assertEquals(count($fieldsWithError), $formPage->getFormErrorsCount());
        }
    }

    public function studentDataProvider()
    {
        return [
            'Всё верно' => [
                'Цзынь',
                'Ю',
                'hui22@mail.ru',
                Student::GENDER_MALE,
                'it23',
                200,
                'Поля с ошибкой' => []
            ],
            'Неправильный email, слишком большой рейтинг' => [
                'Иван',
                "O'Генри",
                'invalid_email',
                Student::GENDER_MALE,
                'it23',
                999999,
                'Поля с ошибкой' => ['email', 'rating']
            ],
            'Слишком длинное имя, фамилия содержит недопустимые символы' => [
                str_repeat('too_long_name', 100),
                '!@#invalid_',
                'valid@mail.ru',
                Student::GENDER_MALE,
                'it23',
                200,
                'Поля с ошибкой' => ['name', 'surname']
            ],
            'Невалидное имя группы, рейтинг не может быть отрицательным' => [
                'Иван',
                'Римский-Корсаков',
                'иван@почта.рф',
                Student::GENDER_FEMALE,
                'invalid_group_name',
                -2,
                'Поля с ошибкой' => ['group', 'rating']
            ],
        ];
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../src/app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);
        $app['session.test'] = true;
        $app['pdo']->beginTransaction();
        require __DIR__ . '/../src/routes.php';
        return $this->app = $app;
    }

    public function testIndex()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function tearDown()
    {
        $this->app['pdo']->rollBack();
    }
}
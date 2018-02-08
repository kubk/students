<?php

declare(strict_types=1);

namespace Tests;

use App\Entity\Student;
use Silex\WebTestCase;
use Tests\PageObject\LogOutForm;
use Tests\PageObject\ProfileForm;

/**
 * @see http://silex.sensiolabs.org/doc/2.0/testing.html#webtestcase
 * @see https://github.com/silexphp/Silex/blob/master/src/Silex/WebTestCase.php
 */
class ControllerTest extends WebTestCase
{
    /**
     * @dataProvider studentDataProvider
     */
    public function testForm($name, $surname, $email, $gender, $group, $rating, $fieldsWithError)
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/form');

        $profileForm = new ProfileForm($crawler);
        $profileForm->typeName($name);
        $profileForm->typeSurname($surname);
        $profileForm->typeEmail($email);
        $profileForm->typeRating($rating);
        $profileForm->typeGender($gender);
        $profileForm->typeGroup($group);

        if ($fieldsWithError) {
            $formWithErrorCrawler = $profileForm->submit($client);
            $profileForm = new ProfileForm($formWithErrorCrawler);
            $this->assertTrue($profileForm->checkFormHasErrorsInFields($fieldsWithError));
            $this->assertEquals(count($fieldsWithError), $profileForm->getFormErrorsCount());
        } else {
            $profileForm->submit($client);
            $this->assertTrue($client->getResponse()->isRedirect());

            // Выход
            $logOutForm = new LogOutForm($client->request('GET', '/form'));
            $logOutForm->submit($client);
            $this->assertTrue($client->getResponse()->isRedirect());

            // Удостоверимся, что со старыми данными зарегистрироваться не выйдет, так как email занят
            $formWithErrorCrawler = $profileForm->submit($client);
            $profileForm = new ProfileForm($formWithErrorCrawler);
            $this->assertTrue($profileForm->checkFormHasErrorsInFields(['email']));
            $this->assertEquals(1, $profileForm->getFormErrorsCount());
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
                'Поля с ошибкой' => [],
            ],
            'Неправильный email, слишком большой рейтинг' => [
                'Иван',
                "O'Генри",
                'invalid_email',
                Student::GENDER_MALE,
                'it23',
                999999,
                'Поля с ошибкой' => ['email', 'rating'],
            ],
            'Слишком длинное имя, фамилия содержит недопустимые символы' => [
                str_repeat('too_long_name', 100),
                '!@#invalid_',
                'valid@mail.ru',
                Student::GENDER_MALE,
                'it23',
                200,
                'Поля с ошибкой' => ['name', 'surname'],
            ],
            'Невалидное имя группы, рейтинг не может быть отрицательным' => [
                'Иван',
                'Римский-Корсаков',
                'иван@почта.рф',
                Student::GENDER_FEMALE,
                'invalid_group_name',
                -2,
                'Поля с ошибкой' => ['group', 'rating'],
            ],
        ];
    }

    public function createApplication()
    {
        $config = require __DIR__.'/../config/config_tests.php';
        $app = require __DIR__.'/../src/app.php';
        $app['session.test'] = true;
        $app['pdo']->beginTransaction();
        $app['pdo']->exec(file_get_contents(__DIR__.'/../create-students-table.sql'));
        require __DIR__.'/../src/routes.php';

        return $app;
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

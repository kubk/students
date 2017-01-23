<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;

class FormPageObject
{
    /**
     * @var \Symfony\Component\DomCrawler\Form
     */
    private $form;

    /**
     * @var Crawler
     */
    private $crawler;

    private $formId = 'student_submit';
    private $errorsClass = '.has-error';

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
        $this->form = $crawler->selectButton($this->formId)->form();
    }

    public function submitForm(Client $client): Crawler
    {
        return $client->submit($this->form);
    }

    public function typeName(string $name)
    {
        $this->form['student[name]'] = $name;
    }

    public function typeSurname(string $surname)
    {
        $this->form['student[surname]'] = $surname;
    }

    public function typeEmail(string $email)
    {
        $this->form['student[email]'] = $email;
    }

    public function typeRating(int $rating)
    {
        $this->form['student[rating]'] = $rating;
    }

    public function typeGender(string $gender)
    {
        $this->form['student[gender]'] = $gender;
    }

    public function typeGroup(string $group)
    {
        $this->form['student[group]'] = $group;
    }

    public function getFormErrorsCount(): int
    {
        return $this->crawler->filter($this->errorsClass)->count();
    }

    public function checkFormHasErrorsInFields(array $fieldsWithError): bool
    {
        foreach ($fieldsWithError as $fieldWithError) {
            $selector = "{$this->errorsClass} > #student_{$fieldWithError}";
            if ($this->crawler->filter($selector)->count() !== 1) {
                return false;
            }
        }

        return true;
    }
}
<?php

declare(strict_types=1);

namespace Tests\PageObject;

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;

class LogOutForm
{
    /**
     * @var \Symfony\Component\DomCrawler\Form
     */
    private $form;

    /**
     * @var Crawler
     */
    private $crawler;

    private $formId = 'log_out_submit';

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
        $this->form = $crawler->selectButton($this->formId)->form();
    }

    public function submit(Client $client): Crawler
    {
        return $client->submit($this->form);
    }
}

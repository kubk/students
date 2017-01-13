<?php

declare(strict_types=1);

namespace Tests;

use App\StudentTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;

class StudentTwigExtensionTest extends TestCase
{
    /**
     * @var StudentTwigExtension
     */
    private $studentTwigExtension;

    public function setUp()
    {
        $urlGenerator = $this->createMock(UrlGenerator::class);
        $this->studentTwigExtension = new StudentTwigExtension($urlGenerator);
    }

    /**
     * @dataProvider stringFoundProvider
     */
    public function testStringFound($string, $search)
    {
        $result = $this->studentTwigExtension->markSearch($string, $search);
        $this->assertContains($search, $result, '' /* message */, true /* ignore case */);
        $this->assertNotEquals($result, $string);
    }

    public function stringFoundProvider()
    {
        return [
            [
                'Поляков',
                'Поляк',
            ],
            'Поиск регистронезависимый' => [
                'aaabaaa',
                'B',
            ],
        ];
    }

    /**
     * @dataProvider stringNotFoundProvider
     */
    public function testStringNotFound($string, $search)
    {
        $result = $this->studentTwigExtension->markSearch($string, $search);
        $this->assertEquals($result, $string);
    }

    public function stringNotFoundProvider()
    {
        return [
            'Если искомая строка не найдена, то возвращается прежняя строка' => [
                'aaaa',
                'bbbb',
            ],
            'Спецсимволы регулярных выражений не ломают поиск' => [
                'wonder',
                '\w/i',
            ],
        ];
    }

    public function testSpecialCharactersConvertedToHtmlEntities()
    {
        $result = $this->studentTwigExtension->markSearch("a'sd<sdfa>sdfsd", "a'sd");
        $this->assertContains('&lt;', $result);
        $this->assertContains('&gt;', $result);
        $this->assertContains('&#039;', $result);
    }
}

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
        $urlGenerator->method('generate')->with('form')->willReturn('/form');

        $this->studentTwigExtension = new StudentTwigExtension($urlGenerator, [$this, 'wrapFound']);
    }

    public function wrapFound(array $matches)
    {
        $found = $matches[1];
        return "<{$found}>";
    }

    /**
     * @dataProvider searchStringProvider
     * @param $string string Строка, в которой ищем
     * @param $search string Что ищем
     * @param $marked string Ожидаемый результат
     */
    public function testMarkSearch($string, $search, $marked)
    {
        $this->assertSame(
            $this->studentTwigExtension->markSearch($search, $string),
            $marked
        );
    }

    public function searchStringProvider()
    {
        return [
            [
                'Поляк',
                'Поляков',
                '<Поляк>ов',
            ],
            'Если искомая строка не найдена, то возвращается прежняя строка' => [
                'bbbb',
                'aaaa',
                'aaaa'
            ],
            'Спецсимволы регулярных выражений не ломают поиск' => [
                '\w/i',
                'wonder',
                'wonder'
            ],
            'Поиск регистронезависимый' => [
                'B',
                'aaabaaa',
                'aaa<b>aaa',
            ]
        ];
    }

    /**
     * Проверка на то, что метод использует UrlGenerator для генерации путей
     */
    public function testPathToRoute()
    {
        $this->assertSame('/form', $this->studentTwigExtension->pathToRoute('form'));
    }
}

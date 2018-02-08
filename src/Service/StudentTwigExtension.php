<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Student;
use Symfony\Component\Routing\Generator\UrlGenerator;

class StudentTwigExtension extends \Twig_Extension
{
    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('mark_search', [$this, 'markSearch']),
            new \Twig_SimpleFilter('gender', [$this, 'getGenderForConstant']),
        ];
    }

    public function markSearch(string $input, string $search): string
    {
        // Обойдёмся без twig_escape_filter, для него нужны хаки:
        // http://stackoverflow.com/questions/28097270/twig-how-can-i-manually-escape-inside-a-custom-function
        $input = htmlspecialchars($input, ENT_QUOTES);
        $search = htmlspecialchars($search, ENT_QUOTES);

        if ($search) {
            $pattern = '/('.preg_quote($search, '/').')/iu';

            return preg_replace_callback($pattern, [$this, 'wrapFound'], $input);
        }

        return $input;
    }

    private function wrapFound(array $matches): string
    {
        $found = $matches[1];

        return "<mark>{$found}</mark>";
    }

    public function getGenderForConstant(string $constant): string
    {
        switch ($constant) {
            case Student::GENDER_FEMALE: return 'Женский';
            case Student::GENDER_MALE: return 'Мужской';
            default: throw new \Exception('Undefined gender');
        }
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('path_to', [$this, 'pathToRoute']),
        ];
    }

    public function pathToRoute(string $route): string
    {
        return $this->urlGenerator->generate($route);
    }

    public function getName()
    {
        return 'student';
    }
}

<?php

declare(strict_types=1);

namespace App\Helper;

class LinkGenerator
{
    private $search;
    private $order;
    private $perPage;
    private $pageNumber;
    private $sortBy;

    public function __construct(
        string $search,
        string $order,
        int $perPage,
        int $pageNumber,
        string $sortBy
    ) {
        $this->search     = $search;
        $this->order      = $order;
        $this->perPage    = $perPage;
        $this->pageNumber = $pageNumber;
        $this->sortBy     = $sortBy;
    }

    public function getSortingLink(string $columnName): array
    {
        $columnLink = $this->getColumnLink($columnName);

        $newOrder = $this->order;
        $arrow = '';
        if ($columnLink === $this->sortBy) {
            if ($this->order === 'ASC') {
                $arrow = '&#9660';
                $newOrder = 'DESC';
            } else {
                $arrow = '&#9650';
                $newOrder = 'ASC';
            }
        }

        $queryParams = $this->getQueryParamsWith(['order' => $newOrder, 'sort_by' => $columnLink]);

        return [
            'href' => '?' . http_build_query($queryParams),
            'name' => "{$columnName} {$arrow}",
        ];
    }

    private function getColumnLink(string $columnName): string
    {
        $map = [
            'ID' => 'id',
            'Имя' => 'name',
            'Фамилия' => 'surname',
            'Группа' => 'group',
            'Пол' => 'gender',
            'Рейтинг' => 'rating',
        ];

        if (!array_key_exists($columnName, $map)) {
            $correctColumnNames = implode(', ', array_keys($map));
            throw new \Exception("Column name {$columnName} is not presented. Only {$correctColumnNames} are correct");
        }

        return $map[$columnName];
    }

    private function getQueryParamsWith(array $params): array
    {
        return array_merge([
            'page_number' => $this->pageNumber,
            'search' => $this->search,
            'order' => $this->order,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
        ], $params);
    }

    public function getLinkForPage(int $pageNumber): string
    {
        $queryParams = $this->getQueryParamsWith(['page_number' => $pageNumber]);
        return '?' . http_build_query($queryParams);
    }
}

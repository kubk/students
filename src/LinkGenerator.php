<?php

declare(strict_types=1);

namespace App;

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
    )
    {
        $this->search     = $search;
        $this->order      = $order;
        $this->perPage    = $perPage;
        $this->pageNumber = $pageNumber;
        $this->sortBy     = $sortBy;
    }

    public function getSortingLink(string $columnName): string
    {
        $columnLink = $this->getColumnLink($columnName);

        $linkName = $columnName;
        $order = $this->order;
        if ($columnLink == $this->sortBy) {
            if ($this->order === 'ASC') {
                $linkName .= ' &#9660';
                $order = 'DESC';
            } else {
                $linkName .= ' &#9650';
                $order = 'ASC';
            }
        }

        $url = http_build_query([
            'page_number' => $this->pageNumber,
            'search' => $this->search,
            'order' => $order,
            'per_page' => $this->perPage,
            'sort_by' => $columnLink,
        ]);

        return "<a href='?{$url}'>{$linkName}</a>";
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

    public function getLinkForPage(int $pageNumber): string
    {
        return '?' . http_build_query([
            'page_number' => $pageNumber,
            'search' => $this->search,
            'order' => $this->order,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
        ]);
    }
}

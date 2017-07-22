<?php

declare(strict_types=1);

namespace App\Helper;

class Paginator
{
    private $perPage;
    private $pageNumber;
    private $recordsCount;

    public function __construct(int $count, int $perPage, int $pageNumber)
    {
        $this->recordsCount = $count;
        $this->perPage = $perPage;
        $this->pageNumber = $pageNumber;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotalPagesCount(): int
    {
        return (int) ceil($this->recordsCount / $this->perPage);
    }

    public function getTotalPagesRange(): array
    {
        return range(1, $this->getTotalPagesCount());
    }

    public function getOffset(): int
    {
        if ($this->pageNumber < 2) {
            return 0;
        }

        return ($this->pageNumber - 1) * $this->perPage;
    }

    public function getCurrentPage(): int
    {
        return $this->pageNumber;
    }
}

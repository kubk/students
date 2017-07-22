<?php

declare(strict_types=1);

namespace Tests;

use App\Helper\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    /**
     * @dataProvider paginationDataProvider
     */
    public function testGetOffset(
        $pageNumber,
        $count,
        $perPage,
        $expectedOffset,
        $expectedTotalPages
    ) {
        $paginator = new Paginator($count, $perPage, $pageNumber);
        $this->assertSame($expectedOffset, $paginator->getOffset());
        $this->assertSame($expectedTotalPages, $paginator->getTotalPagesCount());
    }

    public function paginationDataProvider()
    {
        return [
            [1, 10, 2, 0, 5],
            [5, 15, 2, 8, 8],
        ];
    }
}

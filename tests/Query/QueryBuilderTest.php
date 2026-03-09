<?php

namespace Vogelyt\AbsenceIoClient\Tests\Query;

use PHPUnit\Framework\TestCase;
use Vogelyt\AbsenceIoClient\Query\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    public function testBasicFilter()
    {
        $query = (new QueryBuilder())
            ->where('status', 'active')
            ->build();

        $this->assertArrayHasKey('filter', $query);
        $this->assertEquals('active', $query['filter']['status']);
    }

    public function testWhereIn()
    {
        $query = (new QueryBuilder())
            ->whereIn('id', [1, 2, 3])
            ->build();

        $this->assertArrayHasKey('filter', $query);
        $this->assertEquals(['$in' => [1, 2, 3]], $query['filter']['id']);
    }

    public function testWhereGreaterThan()
    {
        $query = (new QueryBuilder())
            ->whereGreaterThan('salary', 50000)
            ->build();

        $this->assertEquals(['$gt' => 50000], $query['filter']['salary']);
    }

    public function testWhereLike()
    {
        $query = (new QueryBuilder())
            ->whereLike('name', 'John')
            ->build();

        $this->assertEquals(['like' => 'John'], $query['filter']['name']);
    }

    public function testSorting()
    {
        $query = (new QueryBuilder())
            ->orderBy('name')
            ->orderByDesc('created_at')
            ->build();

        $this->assertArrayHasKey('sortBy', $query);
        $this->assertEquals(1, $query['sortBy']['name']);
        $this->assertEquals(-1, $query['sortBy']['created_at']);
    }

    public function testPagination()
    {
        $query = (new QueryBuilder())
            ->skip(10)
            ->limit(25)
            ->build();

        $this->assertEquals(10, $query['skip']);
        $this->assertEquals(25, $query['limit']);
    }

    public function testPage()
    {
        $query = (new QueryBuilder())
            ->page(3, 20)
            ->build();

        $this->assertEquals(40, $query['skip']); // (3-1) * 20
        $this->assertEquals(20, $query['limit']);
    }

    public function testLimitCap()
    {
        $query = (new QueryBuilder())
            ->limit(200)
            ->build();

        $this->assertEquals(100, $query['limit']); // Capped at 100
    }

    public function testRelations()
    {
        $query = (new QueryBuilder())
            ->with('assignedToId')
            ->with(['departmentId', 'teamId'])
            ->build();

        $this->assertArrayHasKey('relations', $query);
        $this->assertContains('assignedToId', $query['relations']);
        $this->assertContains('departmentId', $query['relations']);
        $this->assertContains('teamId', $query['relations']);
    }

    public function testComplexQuery()
    {
        $query = (new QueryBuilder())
            ->where('status', 'active')
            ->whereGreaterThan('salary', 50000)
            ->whereLike('name', 'John')
            ->orderByDesc('created_at')
            ->page(2, 50)
            ->with(['department', 'team'])
            ->build();

        $this->assertArrayHasKey('filter', $query);
        $this->assertArrayHasKey('sortBy', $query);
        $this->assertEquals(50, $query['skip']); // (2-1) * 50
        $this->assertEquals(50, $query['limit']);
        $this->assertCount(2, $query['relations']);
    }

    public function testReset()
    {
        $query = new QueryBuilder();
        $query->where('status', 'active')->limit(10);

        $query->reset();
        $result = $query->build();

        $this->assertArrayNotHasKey('filter', $result);
        $this->assertEquals(50, $result['limit']); // Reset to default
    }
}

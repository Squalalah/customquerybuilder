<?php

namespace tests\QueryBuilder;

use _PHPStan_532094bc1\React\Dns\Query\Query;
use CustomQueryBuilder\Builder\QueryBuilder;
use Exception;
use PHPUnit\Framework\TestCase;
use tests\MyDB;

require_once("autoload.php");

class QueryBuilderTest extends TestCase
{
    private MyDB $database;
    protected function setUp(): void
    {
        $this->database = new MyDB('logs\test.db');

        parent::setUp();
    }

    /**
    * @test
    *
    * Given a data in testdb table with a "abc" as name
    * When making the query to fetch the row by the name "abc"
    * It returns the matching row with "abc" as name
    **/
    public function itReturnsRowWithAbcAsName(): void
    {
        $query = (new QueryBuilder())
            ->select('name')
            ->from('testdb')
            ->where('name = :name');
        $query->addParameter('name', 'abc');

        $result = $this->database->query($query)->fetchArray(SQLITE3_ASSOC);

        $this->assertEquals(1, count($result));
        $this->assertEquals("abc", $result["name"]);
    }

    /**
    * @test
    *
    * Given a query with custom argument in where but no given parameters
    * When executing the query
    * It throws an Exception
    **/
    public function itThrowsOnCustomArgumentWithNoParameters(): void
    {
        $this->expectException(Exception::class);

        $query = (new QueryBuilder())
            ->select('*')
            ->from('testdb')
            ->where('name = :name');

        $this->database->query($query)->fetchArray(SQLITE3_ASSOC);
    }
}

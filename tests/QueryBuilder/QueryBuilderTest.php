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

        $this->assertCount(1, $result);
        $this->assertEquals("abc", $result["name"]);
    }

    /**
    * @test
    *
    * Given a data in testdb with "a" as name and "b" as description
    * When making a query to fetch the data by its name and description
    * It returns the matching data with "a" as name and "b" as description
    **/
    public function itReturnsRowWithAAsNameAndBAsDescription(): void
    {
        $query = (new QueryBuilder())
            ->select('*')
            ->from('testdb')
            ->where('name = :name AND description = :description')
            ->addParameter('name', 'a')
            ->addParameter('description', 'b');

        $resultQuery = $this->database->query($query);
        $result = $resultQuery->fetchArray(SQLITE3_ASSOC);


        $this->assertEquals("a", $result["name"]);
        $this->assertEquals("b", $result["description"]);
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

    /**
    * @test
    *
    * Given a query with a dynamic argument and 2 dynamic arguments added
    * When executing the query
    * It does not throw any exception
    **/
    public function itDoesNotThrowOnQueryWithOneArgumentAndNonExistingArgumentAdded(): void
    {
        $query = (new QueryBuilder())
            ->select('*')
            ->from('testdb')
            ->where('name = :name')
            ->addParameter('description', 'nonExistingArgument');

        $result = $this->database->query($query)->fetchArray(SQLITE3_ASSOC);

        $this->assertFalse($result);

    }
}

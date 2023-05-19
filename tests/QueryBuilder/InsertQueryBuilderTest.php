<?php

namespace tests\QueryBuilder;

require("autoload.php");

use CustomQueryBuilder\Builder\QueryBuilder;
use tests\AppTestCase;

class InsertQueryBuilderTest extends AppTestCase
{
    /**
    * @test
    *
    * Given create query with a name and a description
    * When executing the query
    * It creates a row with given name and description in table
    **/
    public function itInsertsRowInTable(): void
    {
        $name = "a" . rand(0, 999);
        $description = "b" . rand(0, 999);
        $query = (new QueryBuilder())
            ->insertInto("testdb")
            ->inFields("name", "description")
            ->withValues($name, $description);

        $this->database->query($query);

        $query = (new QueryBuilder())
            ->select("name", "description")
            ->from("testdb")
            ->where("name = :name AND description = :description")
            ->addParameter("name", $name)
            ->addParameter("description", $description);

        $result = $this->database->query($query)->fetchArray(SQLITE3_ASSOC);

        $this->assertEquals($name, $result["name"]);
        $this->assertEquals($description, $result["description"]);
    }
}

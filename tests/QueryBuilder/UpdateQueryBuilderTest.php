<?php

namespace tests\QueryBuilder;

use CustomQueryBuilder\Builder\QueryBuilder;
use tests\AppTestCase;
use tests\Builder\FixtureBuilder;

class UpdateQueryBuilderTest extends AppTestCase
{
    /**
    * @test
    *
    * Given a data in database with a name
    * When updating the name of that data
    * It updates the data name
    **/
    public function itUpdatesDataOnUpdateQuery(): void
    {
        $name = "update" . rand();
        $updatedName = "updatedName";
        FixtureBuilder::create()->withName($name)->build();

        $query = (new QueryBuilder())
            ->update(self::DATABASE_NAME)
            ->set("name", $updatedName)
            ->set("description", "helloWorld")
            ->where("name = :name")
            ->addParameter("name", $name);

        $this->database->query($query);
        $this->assertNotEmpty($this->getRowByName($updatedName));
    }

    /**
     * @return array|false
     */
    private function getRowByName(string $name)
    {
        $query = (new QueryBuilder())
            ->select("*")
            ->from(self::DATABASE_NAME)
            ->where("name = :name")
            ->addParameter("name", $name);

        return $this->database->query($query)->fetchArray();
    }
}

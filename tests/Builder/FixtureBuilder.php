<?php

namespace tests\Builder;

use tests\AppTestCase;
use tests\MyDB;

class FixtureBuilder
{
    private ?string $name = null;
    private ?string $description = null;

    public function withName(string $name): FixtureBuilder
    {
        $this->name = $name;

        return $this;
    }

    public function withDescription(string $description): FixtureBuilder
    {
        $this->description = $description;

        return $this;
    }

    public function build(): void
    {
        $name = "'" . ($this->name ?? rand(0, 999)) . "'";
        $description = "'" . ($this->description ?? rand(0, 999)) . "'";
        $database = new MyDB(AppTestCase::DATABASE_URL);

        $database->query("INSERT INTO testdb (name, description) values ($name,$description)");
    }

    public static function create(): FixtureBuilder
    {
        return new FixtureBuilder();
    }
}

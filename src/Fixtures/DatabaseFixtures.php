<?php

namespace CustomQueryBuilder\Fixtures;

use tests\AppTestCase;
use tests\MyDB;

class DatabaseFixtures
{
    private MyDB $database;
    public function load(): void
    {
        $this->database = new MyDB(AppTestCase::DATABASE_URL);
        $this->database->query(
            "CREATE TABLE IF NOT EXISTS testdb(id INTEGER primary key autoincrement,name VARCHAR(50),description VARCHAR(50) default '{}');"
        );
    }

    public function purge(): void
    {
        $this->database->query(
            "DROP TABLE IF EXISTS testdb;"
        );
    }
}

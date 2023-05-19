<?php

namespace tests;

use CustomQueryBuilder\Fixtures\DatabaseFixtures;
use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase
{
    public const DATABASE_URL = 'logs\test.db';
    public const DATABASE_NAME = 'testdb';
    private DatabaseFixtures $databaseFixtures;
    protected MyDB $database;
    protected function setUp(): void
    {
        $this->database = new MyDB(self::DATABASE_URL);
        $this->databaseFixtures = new DatabaseFixtures();
        $this->databaseFixtures->load();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->databaseFixtures->purge();
        parent::tearDown();
    }
}

<?php

namespace tests;

use CustomQueryBuilder\Fixtures\DatabaseFixtures;
use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase
{
    public const DATABASE_URL = 'logs\test.db';
    private DatabaseFixtures $databaseFixtures;
    protected function setUp(): void
    {
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

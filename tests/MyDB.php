<?php

namespace tests;

use SQLite3;

class MyDB extends SQLite3
{
    public function __construct($filename, $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryptionKey = null)
    {
        parent::__construct($filename, $flags, $encryptionKey);
    }
}

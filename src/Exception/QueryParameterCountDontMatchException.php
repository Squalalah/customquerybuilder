<?php

namespace CustomQueryBuilder\Exception;

use Exception;

class QueryParameterCountDontMatchException extends Exception
{
    private const ERROR_MESSAGE = 'The QueryBuilder parameters count does not match the number of dynamic parameter in WHERE clause';
    public function __construct()
    {
        parent::__construct(self::ERROR_MESSAGE);
    }
}

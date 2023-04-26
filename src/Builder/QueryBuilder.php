<?php

namespace CustomQueryBuilder\Builder;

use CustomQueryBuilder\Exception\QueryParameterCountDontMatchException;
use tests\MyDB;

class QueryBuilder
{
    private const DYNAMIC_ARGUMENT_DELIMITER = ":";
    private const SELECT = "SELECT";
    private const FROM = "FROM";
    private const WHERE = "WHERE";
    private const SELECT_DELIMITER_PARAM = ",";
    private const ESCAPE_DELIMITER = "'";

    private string $selectQuery;
    private string $fromTableQuery;

    private string $whereClause;

    /** @var array<mixed> $parameters */
    private array $parameters = [];
    public function select(string... $args): self
    {
        $select = self::SELECT . " " . implode(self::SELECT_DELIMITER_PARAM, $args) . " ";
        $this->selectQuery = $select;

        return $this;
    }

    public function from(string $table): self
    {
        $from = self::FROM . " " . $table . " ";
        $this->fromTableQuery = $from;

        return $this;
    }

    public function where(string $whereClause): self
    {
        $where = self::WHERE . " " . $whereClause;
        $this->whereClause = $where;

        return $this;
    }

    public function addParameter(string $parameterName, string $parameterValue): self
    {
        $this->parameters[$parameterName] = $parameterValue;

        return $this;
    }

    public function buildQuery(): string
    {
        $query = "";
        if (isset($this->selectQuery)) {
            $query .= $this->selectQuery;
        }
        if (isset($this->fromTableQuery)) {
            $query .= $this->fromTableQuery;
        }

        if (isset($this->whereClause)) {
            $whereQuery = $this->whereClause;
            if(!$this->checkParametersCountMatchesWithWhereParams()) {
                throw new QueryParameterCountDontMatchException();
            }

            if($this->countParametersInQuery() > 0) {
                /** @var string $parameterValue */
                foreach($this->parameters as $parameterName => $parameterValue) {
                    $parsedParameterName = $this->parseParameterName($parameterName);
                    $parsedParameterValue = $this->parseParameterValue($parameterValue);

                    if(false === $this->isGivenParameterInQuery($parameterName)) {
                        continue;
                    }
                    $whereQuery = $this->putParameterInQuery($parsedParameterName, $parsedParameterValue, $whereQuery);
                }
            }
            $query .= $whereQuery;
        }

        return $query;
    }
    public function __toString(): string
    {
        return $this->buildQuery();
    }

    private function parseParameterName(string $parameter): string
    {
        return self::DYNAMIC_ARGUMENT_DELIMITER . $parameter;
    }

    private function parseParameterValue(string $parameter): string
    {
        return self::ESCAPE_DELIMITER . $parameter . self::ESCAPE_DELIMITER;
    }

    /**
     * @return false|int
     */
    private function isGivenParameterInQuery(string $parameter)
    {
        return strpos($this->whereClause, $parameter);
    }

    private function putParameterInQuery(string $parameterName, string $parameterValue, string $query): string
    {
        return str_replace($parameterName, $parameterValue, $query);
    }

    private function countParametersInQuery(): int
    {
        return substr_count($this->whereClause, self::DYNAMIC_ARGUMENT_DELIMITER);
    }

    private function checkParametersCountMatchesWithWhereParams(): bool
    {
        return substr_count($this->whereClause, ':') === count($this->parameters);
    }
}

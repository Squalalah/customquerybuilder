<?php

namespace CustomQueryBuilder\Builder;

use CustomQueryBuilder\Entity\Parameter;
use CustomQueryBuilder\Exception\QueryParameterCountDontMatchException;

class QueryBuilder
{
    private ?string $type = null;
    private const SELECT = "SELECT";
    private const INSERT_INTO = "INSERT INTO";
    private const FROM = "FROM";
    private const WHERE = "WHERE";
    private const VALUES = "VALUES";
    private const SELECT_DELIMITER_PARAM = ",";
    private const ESCAPE_DELIMITER = "'";
    private const DYNAMIC_ARGUMENT_DELIMITER = ":";

    private string $firstQueryBlock;
    /** @var array<string> $insertIntoColumns */
    private array $insertIntoColumns;
    /** @var array<string> $insertIntoValues */
    private array $insertIntoValues;
    private string $fromTableQuery;
    private string $whereClause;

    /** @var Parameter[] $parameters */
    private array $parameters = [];
    public function select(string... $args): self
    {
        $this->type = self::SELECT;
        $select = self::SELECT . " " . implode(self::SELECT_DELIMITER_PARAM, $args) . " ";
        $this->firstQueryBlock = $select;

        return $this;
    }

    public function insertInto(string $table): self
    {
        $this->type = self::INSERT_INTO;
        $insert = self::INSERT_INTO . " " . $table . " ";
        $this->firstQueryBlock = $insert;

        return $this;
    }

    public function inFields(string... $args): self
    {
        $this->insertIntoColumns = $args;

        return $this;
    }

    public function withValues(string... $args): self
    {
        $this->insertIntoValues = $args;

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
        $this->parameters[] = new Parameter($parameterName, $parameterValue);

        return $this;
    }

    public function buildQuery(): string
    {
        $query = "";
        switch($this->type) {
            case self::SELECT: {
                $query .= $this->buildSelect();
                break;
            }
            case self::INSERT_INTO: {
                $query .= $this->buildInsert();
                break;
            }
        }

        return $query;
    }
    public function __toString(): string
    {
        return $this->buildQuery();
    }

    private function buildInsert(): string
    {
        $result = '';
        if (isset($this->firstQueryBlock)) {
            $result = $this->firstQueryBlock;
        }
        $insertColumn = "(" . implode(',', $this->insertIntoColumns) . ")";
        $insertValue = self::VALUES . "('" . implode("','", $this->insertIntoValues) . "')";

        $result .= $insertColumn . $insertValue;

        return $result;
    }

    private function buildSelect(): string
    {
        $result = '';
        if (isset($this->firstQueryBlock)) {
            $result = $this->firstQueryBlock;
        }
        if (isset($this->fromTableQuery)) {
            $result .= $this->fromTableQuery;
        }
        if (isset($this->whereClause)) {
            $result .= $this->buildWhereClause();
        }

        return $result;
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
        return strpos($this->whereClause, self::DYNAMIC_ARGUMENT_DELIMITER . $parameter);
    }

    private function putParameterInQuery(string $parameterName, string $parameterValue, string $query): string
    {
        return str_replace($parameterName, $parameterValue, $query);
    }

    private function countDynamicParametersInQuery(): int
    {
        return substr_count($this->whereClause, self::DYNAMIC_ARGUMENT_DELIMITER);
    }

    private function buildWhereClause(): string
    {
        $whereQuery = $this->whereClause;
        if(!$this->checkParametersCountMatchesWithWhereParams()) {
            throw new QueryParameterCountDontMatchException();
        }

        if($this->countDynamicParametersInQuery() > 0) {
            foreach($this->parameters as $parameter) {
                $parsedParameterName = $this->parseParameterName($parameter->getName());
                $parsedParameterValue = $this->parseParameterValue($parameter->getValue());

                if(false === $this->isGivenParameterInQuery($parameter->getName())) {
                    continue;
                }
                $whereQuery = $this->putParameterInQuery($parsedParameterName, $parsedParameterValue, $whereQuery);
            }
        }
        return $whereQuery;
    }

    private function checkParametersCountMatchesWithWhereParams(): bool
    {
        return substr_count($this->whereClause, ':') === count($this->parameters);
    }
}

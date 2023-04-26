<?php

namespace CustomQueryBuilder\Builder;

use tests\MyDB;

class QueryBuilder
{
    private const DYNAMIC_ARGUMENT_DELIMITER = ':';

    private const ESCAPE_DELIMITER = '\'';
    private string $query;
    private string $selectQuery = "SELECT ";

    private string $fromTableQuery = "FROM ";

    private string $whereClause = "WHERE ";

    /** @var array<mixed> $parameters */
    private array $parameters = [];
    public function select(string... $args): self
    {
        $this->selectQuery .= implode(", ", $args);
        $this->selectQuery .= ' ';

        return $this;
    }

    public function from(string $table): self
    {
        $this->fromTableQuery .= $table;
        $this->fromTableQuery .= ' ';

        return $this;
    }

    public function where(string $whereClause): self
    {
        $this->whereClause .= $whereClause;
        $this->whereClause .= ' ';

        return $this;
    }

    public function addParameter(string $parameterName, string $parameterValue): self
    {
        $this->parameters[$parameterName] = $parameterValue;

        return $this;
    }

    public function buildQuery(): string
    {
        $this->query = $this->selectQuery . $this->fromTableQuery;
        if (isset($this->whereClause)) {
            if(!$this->checkParametersCountMatchesWithWhereParams()) {
                throw new \Exception("The QueryBuilder parameters count does not match the number of dynamic parameter in WHERE clause");
            }
            $numDynamicParametersInQuery = substr_count($this->whereClause, self::DYNAMIC_ARGUMENT_DELIMITER, 0);
            if($numDynamicParametersInQuery > 0) {
                /**
                 * @var string $parameterName
                 * @var string $parameterValue
                 */
                foreach($this->parameters as $parameterName => $parameterValue) {
                    $parsedParameterName = $this->parseParameterName($parameterName);
                    $parsedParameterValue = $this->parseParameterValue($parameterValue);

                    if(false === $this->isGivenParameterInQuery($parameterName)) {
                        continue;
                    }
                    $this->whereClause = $this->putParameterInQuery($parsedParameterName, $parsedParameterValue);
                }
            }
            $this->query .= trim($this->whereClause);
        }

        return $this->query;
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

    private function putParameterInQuery(string $parameterName, string $parameterValue): string
    {
        return str_replace($parameterName, $parameterValue, $this->whereClause);
    }

    private function checkParametersCountMatchesWithWhereParams(): bool
    {
        return substr_count($this->whereClause, ':') === count($this->parameters);
    }
}

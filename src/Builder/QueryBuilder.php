<?php

namespace CustomQueryBuilder\Builder;

class QueryBuilder
{
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
        $this->parameters[] = [$parameterName => $parameterValue];

        return $this;
    }

    public function buildQuery(): string
    {
        $this->query = $this->selectQuery . $this->fromTableQuery;
        if (isset($this->whereClause)) {
            if(!$this->checkParametersCountMatchesWithWhereParams()) {
                throw new \Exception("The QueryBuilder parameters count does not match the number of dynamic parameter in WHERE clause");
            }

            for($i = 0; $i < substr_count($this->whereClause, ':', 0); $i++) {
                $pos = strpos($this->whereClause, ':', 0);
                if($pos !== false) {
                    foreach($this->parameters as $parameter) {
                        /** @var array<mixed> $parameter */
                        /** @var string $name */
                        $name = array_key_first($parameter);
                        $this->whereClause = substr_replace($this->whereClause, "'" . $parameter[$name] . "'", $pos, strlen($name)+1);
                    }
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

    private function checkParametersCountMatchesWithWhereParams(): bool
    {
        return substr_count($this->whereClause, ':') === count($this->parameters);
    }
}

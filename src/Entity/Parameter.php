<?php

namespace CustomQueryBuilder\Entity;

class Parameter
{
    private string $name;
    private string $value;

    public function __construct(string $parameterName, string $parameterValue)
    {
        $this->name = $parameterName;
        $this->value = $parameterValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

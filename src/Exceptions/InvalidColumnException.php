<?php

namespace Kinetics\Exceptions;

class InvalidColumnException extends TableException
{
    public static function notDefined(string $key): static
    {
        return new static("Column [{$key}] is not defined in the table.");
    }
}

<?php

namespace Kinetics\Exceptions;

use Kinetics\Contracts\PipeInterface;

class InvalidPipeException extends TableException
{
    public static function doesNotImplementInterface(string $class): static
    {
        return new static(
            "Pipe [{$class}] must implement ".PipeInterface::class
        );
    }
}

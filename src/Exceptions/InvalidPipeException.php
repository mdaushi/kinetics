<?php

namespace Kinetics\Exceptions;

class InvalidPipeException extends TableException
{
    public static function doesNotImplementInterface(string $class): static
    {
        return new static(
            "Pipe [{$class}] must implement " . \Kinetics\Contracts\PipeInterface::class
        );
    }
}

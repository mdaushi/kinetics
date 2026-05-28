<?php

namespace Kinetics\Contracts;

interface ColumnInterface
{
    public function toArray(): array;
    public function getKey(): string;
}

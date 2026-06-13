<?php

namespace Kinetics\Filters;

class TextFilter extends Filter
{
    protected ?string $type = 'text';

    protected array $operators = ['contains', 'equals', 'starts_with', 'ends_with'];
}

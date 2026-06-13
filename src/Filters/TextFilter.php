<?php

namespace Kinetics\Filters;

use Illuminate\Database\Eloquent\Builder;

class TextFilter extends Filter
{
    protected ?string $type = 'text';

    protected array $operators = ['contains', 'equals', 'starts_with', 'ends_with'];

    public function apply(Builder $query, mixed $payload): void
    {
        [$operator, $value] = $this->parsePayload($payload);

        if ($value === null || $value === '') {
            return;
        }

        $column = $query->qualifyColumn($this->getColumn());

        $this->applyOperator($query, $column, $operator, $value);
    }
}

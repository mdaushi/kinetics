<?php

namespace Kinetics\Filters;

use Illuminate\Database\Eloquent\Builder;

class DateFilter extends Filter
{
    protected ?string $type = 'date';

    protected array $operators = [
        'equals' => 'Equals',
        '<' => 'Before',
        '>' => 'After',
    ];

    protected bool $isRange = false;

    /**
     * Set this filter to act purely as a date range filter.
     */
    public function range(bool $condition = true): static
    {
        $this->isRange = $condition;

        if ($condition) {
            $this->operators(['between' => 'Between']);
        }

        return $this->meta('is_range', $condition);
    }

    public function apply(Builder $query, mixed $payload): void
    {
        [$operator, $value] = $this->parsePayload($payload);

        if ($value === null || $value === '') {
            return;
        }

        $column = $query->qualifyColumn($this->getColumn());

        if ($this->isRange && $operator !== 'between') {
            $operator = 'between';
        }

        $this->applyOperator($query, $column, $operator, $value);
    }
}

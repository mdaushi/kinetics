<?php

namespace Kinetics\Filters;

use Illuminate\Database\Eloquent\Builder;

class NumberFilter extends Filter
{
    protected ?string $type = 'number';

    protected array $operators = ['equals', 'not_equals', '>', '>=', '<', '<='];

    protected bool $isRange = false;

    /**
     * Set this filter to act purely as a number range filter.
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

        if ($this->isRange && $operator !== 'between') {
            $operator = 'between';
        }

        $this->resolveAndApplyOperator($query, $operator, $value);
    }
}

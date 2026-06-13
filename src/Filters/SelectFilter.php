<?php

namespace Kinetics\Filters;

use Illuminate\Database\Eloquent\Builder;

class SelectFilter extends Filter
{
    protected ?string $type = 'select';

    protected array $operators = ['is', 'is_not'];

    /**
     * Set the options for the select filter.
     * Can be an array of key-value pairs ['active' => 'Active', 'inactive' => 'Inactive']
     * or a simple array ['active', 'inactive'].
     */
    public function options(array $options): static
    {
        $formatted = [];
        foreach ($options as $value => $label) {
            if (is_int($value)) {
                // Numeric array ['active', 'inactive'] -> value and label are the same
                $formatted[] = ['value' => $label, 'label' => $label];
            } else {
                // Associative array ['active' => 'Active Status']
                $formatted[] = ['value' => $value, 'label' => $label];
            }
        }

        return $this->meta('options', $formatted);
    }

    public function apply(Builder $query, mixed $payload): void
    {
        [$operator, $value] = $this->parsePayload($payload);

        if ($value === null || $value === '') {
            return;
        }

        $column = $query->qualifyColumn($this->getColumn());

        if (is_array($value)) {
            $value = array_filter($value, fn ($v) => $v !== null && $v !== '');
            if (empty($value)) {
                return;
            }
        }

        $this->applyOperator($query, $column, $operator, $value);
    }
}

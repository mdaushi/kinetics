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
     * You can also pass the formatted array directly: [['value' => 1, 'label' => 'Yes']]
     */
    public function options(array $options): static
    {
        // If the array is already in the final format [['value' => x, 'label' => y]]
        if (! empty($options) && array_is_list($options) && is_array($options[0]) && array_key_exists('value', $options[0]) && array_key_exists('label', $options[0])) {
            return $this->meta('options', $options);
        }

        $formatted = [];
        $isList = array_is_list($options);

        foreach ($options as $value => $label) {
            if ($isList) {
                // Numeric array ['active', 'inactive'] -> value and label are the same
                $formatted[] = ['value' => $label, 'label' => $label];
            } else {
                // Associative array ['active' => 'Active Status', 1 => 'Yes', 0 => 'No']
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

        if (is_array($value)) {
            $value = array_filter($value, fn ($v) => $v !== null && $v !== '');
            if (empty($value)) {
                return;
            }
        }

        $this->resolveAndApplyOperator($query, $operator, $value);
    }
}

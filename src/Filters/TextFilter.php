<?php

namespace Kinetics\Filters;

use Illuminate\Database\Eloquent\Builder;

class TextFilter extends Filter
{
    protected ?string $type = 'text';
    protected array $operators = ['contains', 'equals', 'starts_with', 'ends_with'];

    public function apply(Builder $query, mixed $payload): void
    {
        $operator = 'contains';
        $value = $payload;

        // Support payload array: ['operator' => 'contains', 'value' => '...']
        if (is_array($payload) && isset($payload['operator'])) {
            $operator = $payload['operator'];
            $value = $payload['value'] ?? null;
        }

        if ($value === null || $value === '') {
            return;
        }

        match ($operator) {
            'equals' => $query->where($this->key, '=', $value),
            'starts_with' => $query->where($this->key, 'like', "{$value}%"),
            'ends_with' => $query->where($this->key, 'like', "%{$value}"),
            default => $query->where($this->key, 'like', "%{$value}%"), // contains
        };
    }
}

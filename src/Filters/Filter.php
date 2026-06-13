<?php

namespace Kinetics\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Contracts\FilterInterface;

abstract class Filter implements FilterInterface
{
    protected string $key;

    protected string $label;

    protected ?string $type = null;

    protected array $operators = [];

    protected ?string $column = null;

    protected array $meta = [];

    protected static array $customResolvers = [];

    protected function __construct(string $key)
    {
        $this->key = $key;
        $this->label = str(str_replace('.', '_', $key))->replace('_', ' ')->title()->toString();
    }

    public static function make(string $key): static
    {
        return new static($key);
    }

    /**
     * Get all available universal operators (default + custom registered).
     */
    public static function getAvailableUniversalOperators(): array
    {
        $defaults = [
            'equals',
            'is',
            'not_equals',
            'is_not',
            'contains',
            'starts_with',
            'ends_with',
            'in',
            'not_in',
            '>',
            '>=',
            '<',
            '<=',
        ];

        return array_values(array_unique(array_merge($defaults, array_keys(static::$customResolvers))));
    }

    /**
     * Register a custom global operator logic.
     */
    public static function resolveOperator(string $operator, \Closure $resolver): void
    {
        static::$customResolvers[$operator] = $resolver;
    }

    /**
     * Get a simple array of allowed operator keys.
     */
    public function getAllowedOperators(): array
    {
        $allowed = [];
        foreach ($this->operators as $key => $value) {
            $allowed[] = is_int($key) ? $value : $key;
        }

        return $allowed;
    }

    /**
     * Parse the frontend payload and validate the operator.
     */
    protected function parsePayload(mixed $payload): array
    {
        $allowedOperators = $this->getAllowedOperators();
        $defaultOperator = $allowedOperators[0] ?? 'equals';

        if (is_array($payload) && isset($payload['operator'])) {
            $operator = $payload['operator'];
            $value = $payload['value'] ?? null;
        } else {
            $operator = $defaultOperator;
            $value = $payload;
        }

        // Validate: fallback to default if user sends an unknown operator
        if (! empty($allowedOperators) && ! in_array($operator, $allowedOperators)) {
            $operator = $defaultOperator;
        }

        return [$operator, $value];
    }

    /**
     * Centralized Query Builder for all filters.
     */
    protected function applyOperator(Builder $query, string $column, string $operator, mixed $value): void
    {
        // Check if the user has registered a custom resolver for this operator
        if (isset(static::$customResolvers[$operator])) {
            call_user_func(static::$customResolvers[$operator], $query, $column, $value);

            return;
        }

        if ($this->type === 'date') {
            $this->applyDateOperator($query, $column, $operator, $value);
        } else {
            $this->applyStandardOperator($query, $column, $operator, $value);
        }
    }

    protected function applyDateOperator(Builder $query, string $column, string $operator, mixed $value): void
    {
        if ($operator === 'between') {
            if (is_array($value) && count($value) === 2 && ! empty($value[0]) && ! empty($value[1])) {
                $query->whereBetween($column, [
                    Carbon::parse($value[0])->startOfDay(),
                    Carbon::parse($value[1])->endOfDay(),
                ]);
            } elseif (is_array($value) && count($value) === 2 && ! empty($value[0])) {
                $query->whereDate($column, '>=', Carbon::parse($value[0])->startOfDay());
            } elseif (is_array($value) && count($value) === 2 && ! empty($value[1])) {
                $query->whereDate($column, '<=', Carbon::parse($value[1])->endOfDay());
            }

            return;
        }

        $date = Carbon::parse($value);

        match ($operator) {
            'equals', 'is' => $query->whereDate($column, '=', $date),
            'not_equals', 'is_not' => $query->whereDate($column, '!=', $date),
            '>' => $query->whereDate($column, '>', $date),
            '>=' => $query->whereDate($column, '>=', $date),
            '<' => $query->whereDate($column, '<', $date),
            '<=' => $query->whereDate($column, '<=', $date),
            default => throw new \InvalidArgumentException("Filter operator [{$operator}] is not supported for dates."),
        };
    }

    protected function applyStandardOperator(Builder $query, string $column, string $operator, mixed $value): void
    {
        if ($operator === 'between') {
            if (is_array($value) && count($value) === 2 && $value[0] !== null && $value[0] !== '' && $value[1] !== null && $value[1] !== '') {
                $query->whereBetween($column, $value);
            } elseif (is_array($value) && count($value) === 2 && $value[0] !== null && $value[0] !== '') {
                $query->where($column, '>=', $value[0]);
            } elseif (is_array($value) && count($value) === 2 && $value[1] !== null && $value[1] !== '') {
                $query->where($column, '<=', $value[1]);
            } elseif (! is_array($value) && $value !== null && $value !== '') {
                $query->where($column, '>=', $value);
            }

            return;
        }

        match ($operator) {
            'equals', 'is' => is_array($value) ? $query->whereIn($column, $value) : $query->where($column, '=', $value),
            'not_equals', 'is_not' => is_array($value) ? $query->whereNotIn($column, $value) : $query->where($column, '!=', $value),
            'contains' => $query->where($column, 'like', "%{$value}%"),
            'starts_with' => $query->where($column, 'like', "{$value}%"),
            'ends_with' => $query->where($column, 'like', "%{$value}"),
            'in' => $query->whereIn($column, (array) $value),
            'not_in' => $query->whereNotIn($column, (array) $value),
            '>' => $query->where($column, '>', $value),
            '>=' => $query->where($column, '>=', $value),
            '<' => $query->where($column, '<', $value),
            '<=' => $query->where($column, '<=', $value),
            default => throw new \InvalidArgumentException(
                "Filter operator [{$operator}] is not supported by the system. ".
                    'Please register it using Filter::resolveOperator().'
            ),
        };
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function column(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Override the allowed operators for this filter.
     * Example: ->operators(['contains', 'equals']) or ->operators(['is' => 'Adalah'])
     */
    public function operators(array $operators): static
    {
        $this->operators = $operators;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function meta(string|array $key, mixed $value = null): static
    {
        if (is_array($key)) {
            $this->meta = array_merge($this->meta, $key);
        } else {
            $this->meta[$key] = $value;
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->key,
            'label' => $this->label,
            'operators' => $this->formatOperators(),
            'meta' => $this->meta,
        ];
    }

    private function formatOperators(): array
    {
        $formatted = [];
        foreach ($this->operators as $value => $label) {
            if (is_int($value)) {
                $formatted[] = [
                    'value' => $label,
                    'label' => str(str_replace('_', ' ', $label))->title()->toString(),
                ];
            } else {
                $formatted[] = ['value' => $value, 'label' => $label];
            }
        }

        return $formatted;
    }
}

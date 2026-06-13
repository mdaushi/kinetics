<?php

namespace Kinetics\Filters;

use Kinetics\Contracts\FilterInterface;

abstract class Filter implements FilterInterface
{
    protected string $key;
    protected string $label;
    protected ?string $type = null;
    protected array $operators = [];
    protected ?string $column = null;

    protected function __construct(string $key)
    {
        $this->key = $key;
        $this->label = str(str_replace('.', '_', $key))->replace('_', ' ')->title()->toString();
    }

    public static function make(string $key): static
    {
        return new static($key);
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

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->key,
            'label' => $this->label,
            'operators' => $this->formatOperators(),
            // Subclasses will merge their specific properties here
        ];
    }

    private function formatOperators(): array
    {
        $formatted = [];
        foreach ($this->operators as $value => $label) {
            if (is_int($value)) {
                $formatted[] = [
                    'value' => $label,
                    'label' => str(str_replace('_', ' ', $label))->title()->toString()
                ];
            } else {
                $formatted[] = ['value' => $value, 'label' => $label];
            }
        }
        return $formatted;
    }
}

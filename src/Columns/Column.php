<?php

namespace Kinetics\Columns;

use Kinetics\Contracts\ColumnInterface;

class Column implements ColumnInterface
{
    protected string  $key;
    protected string  $label;
    protected bool    $sortable    = false;
    protected bool    $searchable  = false;
    protected bool    $filterable  = false;
    protected bool    $visible     = true;
    protected ?string $type        = 'text';
    protected array   $filterOptions = [];
    protected ?string $relation    = null;
    protected ?string $relationKey = null;
    protected ?\Closure $formatUsing = null;

    protected function __construct(string $key)
    {
        $this->key = $key;
        $this->label = str($key)->replace('_', ' ')->title()->toString();
    }

    // Static constructor

    public static function make(string $key): static
    {
        return new static($key);
    }

    // Fluent modifiers

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function sortable(bool $value = true): static
    {
        $this->sortable = $value;
        return $this;
    }

    public function searchable(bool $value = true): static
    {
        $this->searchable = $value;
        return $this;
    }

    /**
     * Mark column as filterable with optional predefined options.
     *
     * Example:
     *   Column::make('status')->filterable(['active', 'inactive'])
     *   Column::make('role')->filterable(['admin' => 'Administrator', 'user' => 'Regular User'])
     */
    public function filterable(array $options = [], bool $value = true): static
    {
        $this->filterable = $value;
        $this->filterOptions = $options;
        return $this;
    }

    public function hidden(bool $value = true): static
    {
        $this->visible = !$value;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * For relationship columns.
     *
     * Example:
     *   Column::make('department_name')
     *       ->relation('department', 'name')
     *       ->sortable()
     */
    public function relation(string $relation, string $foreignKey): static
    {
        $this->relation = $relation;
        $this->relationKey = $foreignKey;
        return $this;
    }

    /**
     * Custom format closure — runs after data is fetched.
     *
     * Example:
     *   Column::make('amount')->formatUsing(fn($val) => 'Rp '.number_format($val))
     */
    public function formatUsing(\Closure $callback): static
    {
        $this->formatUsing = $callback;
        return $this;
    }

    // Getters

    public function getKey(): string
    {
        return $this->key;
    }
    public function getRelation(): ?string
    {
        return $this->relation;
    }
    public function getRelationKey(): ?string
    {
        return $this->relationKey;
    }
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
    public function isSortable(): bool
    {
        return $this->sortable;
    }
    public function isFilterable(): bool
    {
        return $this->filterable;
    }
    public function getFormatter(): ?\Closure
    {
        return $this->formatUsing;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'filterable' => $this->filterable,
            'filterOptions' => $this->filterOptions,
            'visible' => $this->visible,
            'type' => $this->type,
        ];
    }
}

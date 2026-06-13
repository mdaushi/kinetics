<?php

namespace Kinetics\Columns;

use Kinetics\Contracts\ColumnInterface;

abstract class Column implements ColumnInterface
{
    protected string $key;

    protected string $outputKey;

    protected string $label;

    protected bool $sortable = false;

    protected bool $searchable = false;

    protected bool $visible = true;

    protected ?string $type = 'text';

    protected array $meta = [];

    protected ?string $relation = null;

    protected ?string $relationKey = null;

    protected ?\Closure $formatUsing = null;

    protected function __construct(string $key)
    {
        // Auto-parse dot notation: 'user.name' -> relation='user', relationKey='name', outputKey='user_name'
        if (str_contains($key, '.')) {
            [$this->relation, $this->relationKey] = explode('.', $key, 2);
            $this->outputKey = str_replace('.', '_', $key);
        } else {
            $this->outputKey = $key;
        }

        $this->key = $key;
        $this->label = str(str_replace('.', '_', $key))->replace('_', ' ')->title()->toString();
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

    /**
     * Set an alias for the output key in the row data.
     * Use this when two columns read from the same source field
     * to avoid the second overwriting the first.
     *
     * Example:
     *   TextColumn::make('created_at')->date('d/m/Y'),
     *   TextColumn::make('created_at')->as('created_at_formatted')->formatUsing(fn($v) => ...),
     */
    public function as(string $outputKey): static
    {
        $this->outputKey = $outputKey;

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

    public function hidden(bool $value = true): static
    {
        $this->visible = ! $value;

        return $this;
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

    /**
     * Explicitly override the relation and field for this column.
     *
     * Prefer dot-notation in make() for the common case:
     *   TextColumn::make('user.name')       // auto: relation=user, key=name, output=user_name
     *
     * Use ->relation() only when the output key needs to differ from the relation path:
     *   TextColumn::make('author_name')     // custom output key
     *       ->relation('user', 'name')      // explicit relation override
     */
    public function relation(string $relation, string $relationKey): static
    {
        $this->relation = $relation;
        $this->relationKey = $relationKey;

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

    /**
     * The output key used in row data (may differ from the source DB field).
     */
    public function getKey(): string
    {
        return $this->outputKey;
    }

    /**
     * The source field name from the DB / model attribute.
     */
    public function getSourceKey(): string
    {
        if ($this->relation !== null) {
            return $this->outputKey;
        }

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

    public function getFormatter(): ?\Closure
    {
        return $this->formatUsing;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->outputKey,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'visible' => $this->visible,
            'type' => $this->type,
            'meta' => $this->meta,
        ];
    }
}

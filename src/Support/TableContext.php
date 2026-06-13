<?php

namespace Kinetics\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Kinetics\Columns\Column;
use Kinetics\Contracts\FilterInterface;

/**
 * The context object carried throughout the pipeline.
 *
 * Access to column definitions, requests, and configuration — not just queries.
 * The context is the "trolley" that carries everything.
 */
class TableContext
{
    /** @var Column[] */
    private array $columns;

    /** @var FilterInterface[] */
    private array $filters;

    private array $meta = [];

    public function __construct(
        public Builder $query,
        public readonly Request $request,
        public readonly TableConfig $config,
        array $columns,
        array $filters = [],
        public readonly array $actions = [],
    ) {
        $this->columns = $columns;
        $this->filters = $filters;
    }

    // Column helpers

    /** @return Column[] */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getSortableKeys(): array
    {
        return collect($this->columns)
            ->filter(fn (Column $c) => $c->isSortable())
            ->map(fn (Column $c) => $c->getKey())
            ->values()
            ->toArray();
    }

    public function getSearchableKeys(): array
    {
        return collect($this->columns)
            ->filter(fn (Column $c) => $c->isSearchable())
            ->map(fn (Column $c) => $c->getKey())
            ->values()
            ->toArray();
    }

    /** @return FilterInterface[] */
    public function getFilterObjects(): array
    {
        return $this->filters;
    }

    public function getFilterableKeys(): array
    {
        return collect($this->filters)
            ->map(fn ($f) => $f->getKey())
            ->values()
            ->toArray();
    }

    public function getFiltersArray(): array
    {
        return collect($this->filters)
            ->map(fn ($f) => $f->toArray())
            ->values()
            ->toArray();
    }

    /**
     * Unique relation names needed by relation columns.
     * Used by PaginatePipe to eager-load in a single batch query per relation.
     *
     * @return string[]
     */
    public function getRelationNames(): array
    {
        return collect($this->columns)
            ->filter(fn (Column $c) => $c->getRelation() !== null)
            ->map(fn (Column $c) => $c->getRelation())
            ->unique()
            ->values()
            ->toArray();
    }

    // Request helpers

    public function getSortColumn(): ?string
    {
        return $this->request->get('sort');
    }

    public function getSortDirection(): string
    {
        $dir = strtolower((string) $this->request->get('direction', 'asc'));

        return in_array($dir, ['asc', 'desc']) ? $dir : 'asc';
    }

    public function getSearch(): ?string
    {
        $search = (string) $this->request->get('search', '');

        return strlen($search) >= 2 ? $search : null;
    }

    public function getFilters(): array
    {
        return (array) $this->request->get('filters', []);
    }

    public function getPerPage(): int
    {
        $requested = (int) $this->request->get('per_page', $this->config->defaultPerPage);

        return min(max($requested, 1), $this->config->maxPerPage);
    }

    // Meta bag — pipes bisa menaruh info tambahan di sini

    public function setMeta(string $key, mixed $value): void
    {
        $this->meta[$key] = $value;
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }

    public function allMeta(): array
    {
        return $this->meta;
    }
}

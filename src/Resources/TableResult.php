<?php

namespace Kinetics\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Kinetics\Columns\ActionColumn;
use Kinetics\Columns\Column;
use Kinetics\Support\TableContext;

/**
 * Converts LengthAwarePaginator + TableContext
 * into a shape ready for consumption by FE (TanStack Table).
 */
class TableResult
{
    public function __construct(
        private readonly LengthAwarePaginator $paginator,
        private readonly TableContext $context,
        private readonly array $columns,
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->transformData(),
            'columns' => $this->transformColumns(),
            'meta' => $this->buildMeta(),
            'state' => $this->buildState(),
        ];
    }

    // Data: apply formatters + resolve ActionColumn per-row

    private function transformData(): array
    {
        $formatters = collect($this->columns)
            ->filter(fn(Column $c) => ! ($c instanceof ActionColumn) && $c->getFormatter() !== null)
            ->keyBy(fn(Column $c) => $c->getKey())
            ->toArray();

        /** @var ActionColumn[] $actionColumns */
        $actionColumns = collect($this->columns)
            ->filter(fn(Column $c) => $c instanceof ActionColumn && $c->hasActions())
            ->toArray();

        return collect($this->paginator->items())
            ->map(function ($item) use ($formatters, $actionColumns) {
                $row = $item instanceof \Illuminate\Database\Eloquent\Model
                    ? $item->toArray()
                    : (array) $item;

                // Apply value formatters
                foreach ($formatters as $key => $column) {
                    if (array_key_exists($key, $row)) {
                        $row[$key] = ($column->getFormatter())($row[$key], $row, $item);
                    }
                }

                // Resolve actions per-row dan inject ke dalam data
                foreach ($actionColumns as $actionColumn) {
                    $row[$actionColumn->getKey()] = $actionColumn->resolveForRow($row);
                }

                return $row;
            })
            ->values()
            ->toArray();
    }

    // Column definitions for TanStack Table

    private function transformColumns(): array
    {
        return collect($this->columns)
            ->map(fn(Column $c) => $c->toArray())
            ->values()
            ->toArray();
    }

    // Pagination meta

    private function buildMeta(): array
    {
        return [
            'current_page' => $this->paginator->currentPage(),
            'last_page' => $this->paginator->lastPage(),
            'per_page' => $this->paginator->perPage(),
            'total' => $this->paginator->total(),
            'from' => $this->paginator->firstItem(),
            'to' => $this->paginator->lastItem(),
        ];
    }

    // Current state (sort, search, filters) — dipakai FE untuk sync UI

    private function buildState(): array
    {
        $req = $this->context->request;

        return [
            'sort' => $req->get('sort'),
            'direction' => $req->get('direction', 'asc'),
            'search' => $req->get('search', ''),
            'filters' => (array) $req->get('filters', []),
            'per_page' => $this->context->getPerPage(),
        ];
    }
}

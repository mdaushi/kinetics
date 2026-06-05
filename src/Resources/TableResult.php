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
class TableResult implements \JsonSerializable
{
    public function __construct(
        private readonly LengthAwarePaginator $paginator,
        private readonly TableContext $context,
        private readonly array $columns,
    ) {
    }

    /**
     * Called automatically by json_encode() / response()->json().
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'data' => $this->getData(),
            'columns' => $this->getColumns(),
            'meta' => $this->getMeta(),
            'state' => $this->getState(),
        ];
    }

    /**
     * Transformed row data (formatters + action columns applied).
     */
    public function getData(): array
    {
        return $this->transformData();
    }

    /**
     * Pagination meta
     */
    public function getMeta(): array
    {
        return $this->buildMeta();
    }

    /**
     * Current request state
     */
    public function getState(): array
    {
        return $this->buildState();
    }

    /**
     * Column definitions serialized for the frontend.
     */
    public function getColumns(): array
    {
        return $this->transformColumns();
    }

    /**
     * Total number of rows matching the current query.
     */
    public function getTotal(): int
    {
        return $this->paginator->total();
    }

    /**
     * Current page number.
     */
    public function getCurrentPage(): int
    {
        return $this->paginator->currentPage();
    }

    /**
     * Last page number.
     */
    public function getLastPage(): int
    {
        return $this->paginator->lastPage();
    }

    /**
     * Items per page.
     */
    public function getPerPage(): int
    {
        return $this->paginator->perPage();
    }

    /**
     * Raw Laravel LengthAwarePaginator — for advanced inspection in tests.
     */
    public function getPaginator(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    // Internal builders

    private function transformData(): array
    {
        $formatterColumns = collect($this->columns)
            ->filter(fn(Column $c) => ! ($c instanceof ActionColumn) && $c->getFormatter() !== null)
            ->values();

        /** @var Column[] $relationColumns */
        $relationColumns = collect($this->columns)
            ->filter(fn(Column $c) => ! ($c instanceof ActionColumn) && $c->getRelation() !== null)
            ->values()
            ->toArray();

        /** @var ActionColumn[] $actionColumns */
        $actionColumns = collect($this->columns)
            ->filter(fn(Column $c) => $c instanceof ActionColumn && $c->hasActions())
            ->values()
            ->toArray();

        return collect($this->paginator->items())
            ->map(function ($item) use ($formatterColumns, $relationColumns, $actionColumns) {
                $row = $item instanceof \Illuminate\Database\Eloquent\Model
                    ? $item->toArray()
                    : (array) $item;

                if ($item instanceof \Illuminate\Database\Eloquent\Model) {
                    foreach ($relationColumns as $column) {
                        $relationName = $column->getRelation();
                        $relationKey = $column->getRelationKey();
                        $outputKey = $column->getKey();

                        if (! $item->relationLoaded($relationName)) {
                            $row[$outputKey] = null;
                            continue;
                        }

                        $related = $item->getRelation($relationName);
                        $row[$outputKey] = $related ? data_get($related, $relationKey) : null;
                    }
                }

                foreach ($formatterColumns as $column) {
                    $sourceKey = $column->getSourceKey();
                    $outputKey = $column->getKey();

                    if (! array_key_exists($sourceKey, $row)) {
                        continue;
                    }

                    $row[$outputKey] = ($column->getFormatter())($row[$sourceKey], $row, $item);
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

    private function transformColumns(): array
    {
        return collect($this->columns)
            ->map(fn(Column $c) => $c->toArray())
            ->values()
            ->toArray();
    }

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

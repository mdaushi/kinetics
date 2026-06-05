<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Columns\Column;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Handles all sorting — both local columns and relation columns.
 *
 * Local column:   ORDER BY column_name ASC
 * Relation column: LEFT JOIN related_table ON ... ORDER BY related_table.column ASC
 */
class SortPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        $sortColumn = $ctx->getSortColumn() ?? $ctx->config->defaultSort;
        $sortDirection = $ctx->getSortColumn()
            ? $ctx->getSortDirection()
            : $ctx->config->defaultDirection;

        if (! $sortColumn) {
            return $next($query);
        }

        // Find the column definition — must be sortable and exist in column list
        $columnDef = collect($ctx->getColumns())
            ->first(fn(Column $c) => $c->getKey() === $sortColumn && $c->isSortable());

        // Not a registered sortable column — apply default sort if configured
        if (! $columnDef) {
            if ($ctx->config->defaultSort) {
                $query->orderBy($ctx->config->defaultSort, $ctx->config->defaultDirection);
            }
            return $next($query);
        }

        // Relation column — resolve via BelongsTo JOIN
        if ($columnDef->getRelation()) {
            $this->applyRelationSort($query, $columnDef, $sortDirection);
            return $next($query);
        }

        // Local column — direct orderBy
        $query->orderBy($sortColumn, $sortDirection);

        return $next($query);
    }

    /**
     * Apply a LEFT JOIN and ORDER BY for a BelongsTo relation column.
     *
     * Only BelongsTo is supported because we need getForeignKeyName()
     * and getOwnerKeyName() to build the JOIN condition.
     */
    private function applyRelationSort(Builder $query, Column $column, string $direction): void
    {
        $relation = $column->getRelation();
        $relationKey = $column->getRelationKey();
        $model = $query->getModel();

        if (! method_exists($model, $relation)) {
            return;
        }

        $relationInstance = $model->{$relation}();

        // Only BelongsTo supports getForeignKeyName() / getOwnerKeyName()
        if (! $relationInstance instanceof BelongsTo) {
            return;
        }

        $relatedTable = $relationInstance->getRelated()->getTable();
        $foreignKey = $relationInstance->getForeignKeyName();
        $ownerKey = $relationInstance->getOwnerKeyName();
        $localTable = $model->getTable();

        // Only JOIN if not already joined (idempotent)
        $alreadyJoined = collect($query->getQuery()->joins ?? [])
            ->pluck('table')
            ->contains($relatedTable);

        if (! $alreadyJoined) {
            $query->leftJoin(
                $relatedTable,
                "{$localTable}.{$foreignKey}",
                '=',
                "{$relatedTable}.{$ownerKey}"
            );
        }

        // Qualify the main table to avoid ambiguous column errors after JOIN
        $query->select("{$localTable}.*");

        $query->orderBy("{$relatedTable}.{$relationKey}", $direction);
    }
}

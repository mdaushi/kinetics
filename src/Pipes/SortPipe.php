<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Columns\Column;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Pipes\Concerns\JoinsRelations;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * Handles all sorting — both local columns and relation columns.
 *
 * Local column:   ORDER BY column_name ASC
 * Relation column: LEFT JOIN related_table ON ... ORDER BY related_table.column ASC
 */
class SortPipe implements PipeInterface
{
    use JoinsRelations;

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
            if ($this->joinRelationIfNeeded($query, $columnDef)) {
                $relationInstance = $query->getModel()->{$columnDef->getRelation()}();
                $relatedTable = $relationInstance->getRelated()->getTable();
                $query->orderBy("{$relatedTable}.{$columnDef->getRelationKey()}", $sortDirection);
            }
            return $next($query);
        }

        // Local column — direct orderBy
        $query->orderBy($sortColumn, $sortDirection);

        return $next($query);
    }
}

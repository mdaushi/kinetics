<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Columns\Column;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * Handles sorting for columns derived from relations.
 *
 * Example: Column::make('department_name')->relation('department', 'name')->sortable()
 * Will result in: JOIN departments ON ... ORDER BY departments.name
 */
class RelationSortPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        $sortColumn = $ctx->getSortColumn();
        if (! $sortColumn) {
            return $next($query);
        }

        // Cari column definition yang match sort request
        $column = collect($ctx->getColumns())
            ->first(fn(Column $c) => $c->getKey() === $sortColumn && $c->getRelation());

        if (! $column) {
            return $next($query);
        }

        $relation = $column->getRelation();
        $relationKey = $column->getRelationKey();
        $model = $query->getModel();

        // Resolve relasi dari Eloquent untuk dapat table name
        if (! method_exists($model, $relation)) {
            return $next($query);
        }

        $relationInstance = $model->{$relation}();
        $relatedTable = $relationInstance->getRelated()->getTable();
        $foreignKey = $relationInstance->getForeignKeyName();
        $ownerKey = $relationInstance->getOwnerKeyName();
        $localTable = $model->getTable();

        // JOIN jika belum ada
        $joins = collect($query->getQuery()->joins ?? [])
            ->pluck('table')
            ->toArray();

        if (! in_array($relatedTable, $joins)) {
            $query->leftJoin(
                $relatedTable,
                "{$localTable}.{$foreignKey}",
                '=',
                "{$relatedTable}.{$ownerKey}"
            );
        }

        // Select * dari tabel utama untuk hindari ambiguous column
        $query->select("{$localTable}.*");

        $query->orderBy("{$relatedTable}.{$relationKey}", $ctx->getSortDirection());

        return $next($query);
    }
}

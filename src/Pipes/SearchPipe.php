<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Columns\Column;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Pipes\Concerns\JoinsRelations;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class SearchPipe implements PipeInterface
{
    use JoinsRelations;

    public function handle(Builder $query, Closure $next): mixed
    {
        /** @var TableContext $ctx */
        $ctx = $this->getContext($query);

        if (! $ctx || ! $search = $ctx->getSearch()) {
            return $next($query);
        }

        $columns = collect($ctx->getColumns())
            ->filter(fn(Column $c) => $c->isSearchable());

        $localTable = $query->getModel()->getTable();

        $query->where(function (Builder $q) use ($columns, $search, $localTable, $query) {
            foreach ($columns as $column) {
                if ($relation = $column->getRelation()) {
                    // Try to apply JOIN instead of whereHas
                    if ($this->joinRelationIfNeeded($query, $column)) {
                        $model = $query->getModel();
                        $relationInstance = $model->{$relation}();
                        $relatedTable = $relationInstance->getRelated()->getTable();

                        $q->orWhere("{$relatedTable}.{$column->getRelationKey()}", 'LIKE', "%{$search}%");
                    } else {
                        // Fallback to whereHas if relation is not supported for JOIN (e.g. HasMany)
                        $q->orWhereHas($relation, function (Builder $rel) use ($column, $search) {
                            $rel->where($column->getRelationKey(), 'LIKE', "%{$search}%");
                        });
                    }
                } else {
                    // Qualify local column to prevent ambiguous column errors after JOIN
                    $q->orWhere("{$localTable}.{$column->getKey()}", 'LIKE', "%{$search}%");
                }
            }
        });

        // Simpan ke meta untuk dipakai di response
        $ctx->setMeta('search', $search);

        return $next($query);
    }

    private function getContext(Builder $query): ?TableContext
    {
        return $query->getModel()->datatableContext ?? null;
    }
}

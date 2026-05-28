<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Columns\Column;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class SearchPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        /** @var TableContext $ctx */
        $ctx = $this->getContext($query);

        if (! $ctx || ! $search = $ctx->getSearch()) {
            return $next($query);
        }

        $columns = collect($ctx->getColumns())
            ->filter(fn(Column $c) => $c->isSearchable());

        $query->where(function (Builder $q) use ($columns, $search) {
            foreach ($columns as $column) {
                if ($relation = $column->getRelation()) {
                    // Search melalui relasi: users.name -> whereHas('department', ...)
                    $q->orWhereHas($relation, function (Builder $rel) use ($column, $search) {
                        $rel->where($column->getRelationKey(), 'LIKE', "%{$search}%");
                    });
                } else {
                    $q->orWhere($column->getKey(), 'LIKE', "%{$search}%");
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

<?php

namespace Kinetics\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;

class FilterPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        $requestFilters = $ctx->getFilters();
        $filterObjects = collect($ctx->getFilterObjects())->keyBy(fn ($f) => $f->getKey());

        foreach ($requestFilters as $column => $value) {
            // Only filter processes are allowed (those registered in the Table)
            if (! $filterObjects->has($column)) {
                continue;
            }

            $filter = $filterObjects->get($column);
            $filter->apply($query, $value);
        }

        $ctx->setMeta('filters', $requestFilters);

        return $next($query);
    }
}

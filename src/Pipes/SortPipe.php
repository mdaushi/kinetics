<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class SortPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        /** @var TableContext $ctx */
        $ctx = $query->getModel()->datatableContext ?? null;

        // Context disimpan di query macro — see Table::buildContext()
        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        $sortColumn = $ctx->getSortColumn() ?? $ctx->config->defaultSort;
        $sortDirection = $ctx->getSortColumn()
            ? $ctx->getSortDirection()
            : $ctx->config->defaultDirection;

        $allowed = $ctx->getSortableKeys();

        if ($sortColumn && in_array($sortColumn, $allowed)) {
            $query->orderBy($sortColumn, $sortDirection);
        } elseif ($ctx->config->defaultSort) {
            // Fallback ke default sort
            $query->orderBy($ctx->config->defaultSort, $ctx->config->defaultDirection);
        }

        return $next($query);
    }
}

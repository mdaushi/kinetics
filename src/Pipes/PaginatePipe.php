<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Support\TableContext;

/**
 * Terminal pipe — does not call $next().
 * Executes the query and returns a paginator.
 */
class PaginatePipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): LengthAwarePaginator
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        $perPage = $ctx instanceof TableContext
            ? $ctx->getPerPage()
            : 15;

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }
}

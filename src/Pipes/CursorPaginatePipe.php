<?php

namespace Kinetics\Pipes;

use Closure;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;

/**
 * Use as a replacement for PaginatePipe for very large datasets.
 * Cursor pagination is more efficient because it doesn't use COUNT(*).
 *
 * Caveat: Doesn't support jumping to a specific page (no page number).
 *
 * Penggunaan:
 *   Table::model(Log::class)
 *       ->withoutPipes([PaginatePipe::class])
 *       ->pipes([CursorPaginatePipe::class])
 *       ->columns([...])
 *       ->make();
 */
class CursorPaginatePipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): CursorPaginator
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        $perPage = $ctx instanceof TableContext
            ? $ctx->getPerPage()
            : 15;

        return $query->cursorPaginate($perPage)->withQueryString();
    }
}

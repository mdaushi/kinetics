<?php

namespace Kinetics\Pipes;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;

/**
 * Handles the request's date range filter:
 * ?date_from=2024-01-01&date_to=2024-12-31
 *
 * Can be combined with FilterPipe for more specific date filters.
 */
class DateRangeFilterPipe implements PipeInterface
{
    public function __construct(
        private readonly string $column = 'created_at',
        private readonly string $fromParam = 'date_from',
        private readonly string $toParam = 'date_to',
    ) {}

    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;
        $request = $ctx instanceof TableContext ? $ctx->request : request();

        $from = $request->get($this->fromParam);
        $to = $request->get($this->toParam);

        $column = $query->qualifyColumn($this->column);

        if ($from && $to) {
            $query->whereBetween($column, [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        } elseif ($from) {
            $query->where($column, '>=', Carbon::parse($from)->startOfDay());
        } elseif ($to) {
            $query->where($column, '<=', Carbon::parse($to)->endOfDay());
        }

        return $next($query);
    }
}

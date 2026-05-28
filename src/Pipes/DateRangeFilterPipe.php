<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Handles the request's date range filter:
 * ?date_from=2024-01-01&date_to=2024-12-31&date_column=created_at
 *
 * Can be combined with FilterPipe for more specific date filters.
 */
class DateRangeFilterPipe implements PipeInterface
{
    public function __construct(
        private readonly string $column   = 'created_at',
        private readonly string $fromParam = 'date_from',
        private readonly string $toParam   = 'date_to',
    ) {}

    public function handle(Builder $query, Closure $next): mixed
    {
        $request = request();

        $from = $request->get($this->fromParam);
        $to   = $request->get($this->toParam);

        if ($from && $to) {
            $query->whereBetween($this->column, [
                \Carbon\Carbon::parse($from)->startOfDay(),
                \Carbon\Carbon::parse($to)->endOfDay(),
            ]);
        } elseif ($from) {
            $query->where($this->column, '>=', \Carbon\Carbon::parse($from)->startOfDay());
        } elseif ($to) {
            $query->where($this->column, '<=', \Carbon\Carbon::parse($to)->endOfDay());
        }

        return $next($query);
    }
}

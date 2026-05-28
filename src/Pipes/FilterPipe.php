<?php

namespace Kinetics\Pipes;

use Closure;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Support\TableContext;
use Illuminate\Database\Eloquent\Builder;

class FilterPipe implements PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed
    {
        $ctx = $query->getModel()->datatableContext ?? null;

        if (! $ctx instanceof TableContext) {
            return $next($query);
        }

        $filters  = $ctx->getFilters();
        $allowed  = $ctx->getFilterableKeys();

        foreach ($filters as $column => $value) {
            // Hanya proses filter yang diizinkan
            if (! in_array($column, $allowed)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            // Array value -> whereIn
            if (is_array($value)) {
                $value = array_filter($value, fn($v) => $v !== null && $v !== '');
                if (! empty($value)) {
                    $query->whereIn($column, $value);
                }
                continue;
            }

            // Range filter: { from: '2024-01-01', to: '2024-12-31' }
            if (is_array($value) && isset($value['from'], $value['to'])) {
                $query->whereBetween($column, [$value['from'], $value['to']]);
                continue;
            }

            // Default: exact match
            $query->where($column, $value);
        }

        $ctx->setMeta('filters', $filters);

        return $next($query);
    }
}

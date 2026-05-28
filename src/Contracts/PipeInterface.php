<?php

namespace Kinetics\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Builder;

interface PipeInterface
{
    public function handle(Builder $query, Closure $next): mixed;
}

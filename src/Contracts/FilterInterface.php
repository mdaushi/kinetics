<?php

namespace Kinetics\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    /**
     * Get the filter key (usually the column name in the database).
     */
    public function getKey(): string;

    /**
     * Apply the filter query to the builder.
     */
    public function apply(Builder $query, mixed $value): void;

    /**
     * Serialize the filter config for the frontend.
     */
    public function toArray(): array;
}

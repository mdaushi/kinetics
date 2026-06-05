<?php

namespace Kinetics\Pipes\Concerns;

use Kinetics\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait JoinsRelations
{
    /**
     * Apply a LEFT JOIN for a BelongsTo relation column to optimize queries.
     * Returns true if joined successfully, false if fallback is needed.
     */
    protected function joinRelationIfNeeded(Builder $query, Column $column): bool
    {
        $relation = $column->getRelation();
        $model = $query->getModel();

        if (! method_exists($model, $relation)) {
            return false;
        }

        $relationInstance = $model->{$relation}();

        // Currently only BelongsTo supports simple JOIN safely
        if (! $relationInstance instanceof BelongsTo) {
            return false;
        }

        $relatedTable = $relationInstance->getRelated()->getTable();
        $foreignKey = $relationInstance->getForeignKeyName();
        $ownerKey = $relationInstance->getOwnerKeyName();
        $localTable = $model->getTable();

        $alreadyJoined = collect($query->getQuery()->joins ?? [])
            ->pluck('table')
            ->contains($relatedTable);

        if (! $alreadyJoined) {
            $query->leftJoin(
                $relatedTable,
                "{$localTable}.{$foreignKey}",
                '=',
                "{$relatedTable}.{$ownerKey}"
            );
        }

        // We must select localTable.* to prevent column ambiguity.
        // We only apply this if no custom columns have been specified yet.
        if (empty($query->getQuery()->columns)) {
            $query->select("{$localTable}.*");
        }

        return true;
    }
}

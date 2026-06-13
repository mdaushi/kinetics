<?php

namespace Kinetics\Columns;

use Kinetics\Actions\Action;
use Kinetics\Actions\ActionGroup;

/**
 * Dedicated column for action buttons.
 *
 * Differences from regular columns:
 * - Not sortable/searchable/filterable
 * - Carries resolved action definitions per row
 * - FE accepts resolved actions directly in the data, not just meta columns
 *
 * Contoh:
 *   ActionColumn::make()
 *       ->actions([
 *           Action::edit('/users/:id/edit'),
 *           Action::delete('/users/:id'),
 *       ])
 *
 *   ActionColumn::make()
 *       ->actions([
 *           Action::view('/users/:id'),
 *           ActionGroup::make()
 *               ->actions([
 *                   Action::make('approve')->visibleWhen(fn($r) => $r['status'] === 'pending'),
 *                   Action::make('reject')->visibleWhen(fn($r) => $r['status'] === 'pending'),
 *                   Action::delete('/users/:id'),
 *               ]),
 *       ])
 */
class ActionColumn extends Column
{
    /** @var array<Action|ActionGroup> */
    private array $actionDefinitions = [];

    protected function __construct(string $key)
    {
        parent::__construct($key);
        $this->label('');
        $this->type = 'actions';
    }

    public static function make(string $key = '__actions'): static
    {
        return new static($key);
    }

    /**
     * @param  array<Action|ActionGroup>  $actions
     */
    public function actions(array $actions): static
    {
        $this->actionDefinitions = $actions;

        return $this;
    }

    // Resolve — called by TableResult per-row

    /**
     * Resolves all actions for a single row.
     * The results are immediately inserted into the row data with the key '__actions'.
     */
    public function resolveForRow(array|object $row): array
    {
        return collect($this->actionDefinitions)
            ->map(function ($definition) use ($row) {
                if ($definition instanceof Action) {
                    $resolved = $definition->resolve($row);
                    if (empty($resolved)) {
                        return null;
                    }

                    return array_merge(['type' => 'action'], $resolved);
                }

                if ($definition instanceof ActionGroup) {
                    $resolvedGroup = $definition->resolve($row);
                    if (empty($resolvedGroup) || empty($resolvedGroup['actions'])) {
                        return null;
                    }

                    return $resolvedGroup;
                }

                return null;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function hasActions(): bool
    {
        return ! empty($this->actionDefinitions);
    }

    public function sortable(bool $value = true): static
    {
        // Action columns cannot be sorted
        return $this;
    }

    public function searchable(bool $value = true): static
    {
        // Action columns cannot be searched
        return $this;
    }
}

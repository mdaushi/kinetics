<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Enums\ActionVariant;

/**
 * Groups multiple actions into a dropdown menu.
 * Useful when a row has multiple actions to avoid cluttering a column.
 *
 * Contoh:
 *   ActionGroup::make('More')
 *       ->actions([
 *           Action::make('approve')->label('Approve'),
 *           Action::make('reject')->label('Reject'),
 *           Action::make('archive')->label('Archive'),
 *       ])
 */
class ActionGroup extends BaseActionGroup
{
    /** @var Action[] */
    protected array $actions = [];

    protected bool $iconOnly = true;

    protected ActionVariant $variant = ActionVariant::GHOST;

    /**
     * Resolve group for one row — invisible filter action.
     */
    public function resolve(array|object|null $row = null): array
    {
        $resolved = collect($this->actions)
            ->map(fn (Action $a) => $a->resolve($row))
            ->filter(fn ($a) => ! empty($a))
            ->values()
            ->toArray();

        return array_merge($this->resolveBaseAttributes(), [
            'actions' => $resolved,
        ]);
    }
}

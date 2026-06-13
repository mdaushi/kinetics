<?php

namespace Kinetics\Actions;

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
class ActionGroup
{
    private string $label = 'Actions';

    private ?string $icon = 'ellipsis-vertical';

    /** @var Action[] */
    private array $actions = [];

    private function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label = 'Actions'): static
    {
        return new static($label);
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /** @param Action[] $actions */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Resolve group for one row — invisible filter action.
     */
    public function resolve(array|object $row): array
    {
        $resolved = collect($this->actions)
            ->map(fn (Action $a) => $a->resolve($row))
            ->filter(fn ($a) => ! empty($a))
            ->values()
            ->toArray();

        return [
            'type' => 'group',
            'label' => $this->label,
            'icon' => $this->icon,
            'actions' => $resolved,
        ];
    }
}

<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Enums\ActionVariant;

/**
 * Groups multiple BulkActions into a dropdown menu.
 *
 * Example:
 *   BulkActionGroup::make('More')
 *       ->icon('ellipsis-vertical')
 *       ->floating()
 *       ->actions([
 *           BulkAction::make('archive')->label('Archive'),
 *           BulkAction::make('restore')->label('Restore'),
 *       ])
 */
class BulkActionGroup extends BaseActionGroup
{
    protected ActionVariant $variant = ActionVariant::SECONDARY;

    private string $position = 'floating';

    /** @var BulkAction[] */
    protected array $actions = [];

    protected bool $iconOnly = true;

    /**
     * @param  'toolbar'|'floating'  $position
     */
    public function position(string $position): static
    {
        if ($position == 'toolbar') {
            $this->iconOnly = false;
            $this->variant = ActionVariant::OUTLINE;
        }
        $this->position = $position;

        return $this;
    }

    public function toolbar(): static
    {
        return $this->position('toolbar');
    }

    public function floating(): static
    {
        return $this->position('floating');
    }

    public function resolve(): array
    {
        $resolved = collect($this->actions)
            ->map(fn (BulkAction $a) => $a->resolve())
            ->filter(fn ($a) => ! empty($a))
            ->values()
            ->toArray();

        return array_merge($this->resolveBaseAttributes(), [
            'position' => $this->position,
            'actions' => $resolved,
        ]);
    }
}

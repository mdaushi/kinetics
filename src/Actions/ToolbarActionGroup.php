<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Enums\ActionVariant;

/**
 * Groups multiple ToolbarActions into a dropdown menu in the toolbar.
 *
 * Example:
 *   ToolbarActionGroup::make('More')
 *       ->icon('ellipsis-vertical')
 *       ->actions([
 *           ToolbarAction::make('import')->label('Import'),
 *           ToolbarAction::make('export')->label('Export All'),
 *       ])
 */
class ToolbarActionGroup extends BaseActionGroup
{
    protected ActionVariant $variant = ActionVariant::OUTLINE;

    /** @var ToolbarAction[] */
    protected array $actions = [];

    protected ?string $icon = 'zap';

    public function resolve(): array
    {
        $resolved = collect($this->actions)
            ->map(fn (ToolbarAction $a) => $a->resolve())
            ->filter(fn ($a) => ! empty($a))
            ->values()
            ->toArray();

        return array_merge($this->resolveBaseAttributes(), [
            'actions' => $resolved,
        ]);
    }
}

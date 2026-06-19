<?php

namespace Kinetics\Actions;

/**
 * A global toolbar action — appears in the toolbar, no row context.
 *
 * Use this for page-level actions like "Create", "Import", "Export All".
 *
 * Example:
 *   ToolbarAction::make('create')->label('New User')->icon('plus')->href('users.create')
 *   ToolbarAction::make('import')->label('Import')->icon('upload')->href('users.import')->method('get')
 */
class ToolbarAction extends BaseAction
{
    private bool $asModal = false;

    // Preset constructors

    public static function create(?string $routeName = null): static
    {
        $action = static::make('create')
            ->label('Create')
            ->icon('plus');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    public static function import(?string $routeName = null): static
    {
        $action = static::make('import')
            ->label('Import')
            ->icon('upload');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    public function modal(bool $value = true): static
    {
        $this->asModal = $value;

        return $this;
    }

    public function resolve(): array
    {
        if (! $this->isVisible()) {
            return [];
        }

        $href = $this->href ? route($this->href) : null;

        return array_merge($this->resolveBaseAttributes(), [
            'href' => $href,
            'modal' => $this->asModal,
            'disabled' => $this->isDisabled(),
        ]);
    }
}

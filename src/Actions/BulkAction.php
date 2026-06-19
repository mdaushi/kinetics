<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Enums\ActionVariant;

/**
 * A bulk action — active only when rows are selected.
 *
 * Appears either in the toolbar (when rows selected) or as a floating bar
 * at the bottom of the viewport. Default is floating.
 *
 * The resolved payload sent to the server will include:
 *   - ids: string[]    — selected row IDs
 *   - select_all: bool — whether all records across pages are selected
 *
 * Example:
 *   BulkAction::delete('users.bulk-destroy')->floating()
 *   BulkAction::export('users.export')->toolbar()
 *   BulkAction::make('archive')->label('Archive')->href('users.bulk-archive')->method('post')
 */
class BulkAction extends BaseAction
{
    protected string $method = 'post';

    private string $position = 'floating';

    protected bool $iconOnly = true;

    protected ActionVariant $variant = ActionVariant::SECONDARY;

    /**
     * Preset: bulk delete with confirmation dialog.
     */
    public static function delete(?string $routeName = null): static
    {
        $action = static::make('bulk-delete')
            ->label('Delete')
            ->icon('trash-2')
            ->variant(ActionVariant::DESTRUCTIVE)
            ->method('delete')
            ->confirm(
                title: 'Delete selected records?',
                message: 'This action cannot be undone. All selected records will be permanently deleted.'
            );

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    /**
     * Preset: bulk export.
     */
    public static function export(?string $routeName = null): static
    {
        $action = static::make('bulk-export')
            ->label('Export')
            ->icon('download')
            ->method('post');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    /**
     * Preset: bulk archive.
     */
    public static function archive(?string $routeName = null): static
    {
        $action = static::make('bulk-archive')
            ->label('Archive')
            ->icon('archive')
            ->method('post')
            ->confirm(
                title: 'Archive selected records?',
                message: 'The selected records will be archived and hidden from the main view.'
            );

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    /**
     * Preset: bulk restore.
     */
    public static function restore(?string $routeName = null): static
    {
        $action = static::make('bulk-restore')
            ->label('Restore')
            ->icon('rotate-ccw')
            ->method('post');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    /**
     * Where this bulk action appears in the UI.
     *
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

    public function getPosition(): string
    {
        return $this->position;
    }

    /** Tampil di toolbar saat ada selection (menggantikan regular actions). */
    public function toolbar(): static
    {
        return $this->position('toolbar');
    }

    /** Tampil sebagai floating bar di bottom center viewport. */
    public function floating(): static
    {
        return $this->position('floating');
    }

    public function resolve(): array
    {
        if (! $this->isVisible()) {
            return [];
        }

        $href = $this->href ? $this->resolveHref() : null;

        return array_merge($this->resolveBaseAttributes(), [
            'href' => $href,
            'disabled' => $this->isDisabled(),
            'position' => $this->position,
        ]);
    }

    private function resolveHref(): ?string
    {
        if (! $this->href) {
            return null;
        }

        try {
            return route($this->href);
        } catch (\Exception) {
            return $this->href;
        }
    }
}

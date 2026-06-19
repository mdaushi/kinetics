<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Enums\ActionVariant;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Represents a single action (button/link) in the action column (per row).
 *
 * Each Action has:
 * - a label and icon for UI
 * - an href or handler for navigation/modal
 * - visibility and disabled conditions (closures evaluated per row)
 * - variants for styling
 *
 * For toolbar-level actions, use ToolbarAction.
 * For bulk/selection actions, use BulkAction.
 */
class Action extends BaseAction
{
    protected ActionVariant $variant = ActionVariant::OUTLINE;

    private bool $asModal = false;

    // Static constructor

    // Preset constructors for common actions

    public static function edit(?string $routeName = null): static
    {
        $action = static::make('edit')
            ->label('Edit')
            ->icon('pencil');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    public static function view(?string $routeName = null): static
    {
        $action = static::make('view')
            ->label('View')
            ->icon('eye');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    public static function delete(?string $routeName = null): static
    {
        $action = static::make('delete')
            ->label('Delete')
            ->icon('trash')
            ->variant(ActionVariant::DESTRUCTIVE)
            ->method('delete')
            ->confirm(message: 'Are you sure you want to delete this record?');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    /**
     * Open in modal/dialog instead of navigation.
     */
    public function modal(bool $value = true): static
    {
        $this->asModal = $value;

        return $this;
    }

    // Internals

    /**
     * Resolve action for one row — evaluate all closures.
     */
    public function resolve(array|object|null $row = null): array
    {
        $rowArr = $this->normalizeRow($row);

        if (! $this->isVisible($row)) {
            return [];
        }

        return array_merge($this->resolveBaseAttributes(), [
            'href' => $this->resolveHref($rowArr),
            'modal' => $this->asModal,
            'disabled' => $this->isDisabled($row),
        ]);
    }

    // Internals

    private function resolveHref(array|object|null $row = null): ?string
    {
        if (! $this->href) {
            return null;
        }

        if ($this->href instanceof \Closure) {
            return ($this->href)($row);
        }

        $rowArr = $this->normalizeRow($row);

        try {
            $route = app('router')->getRoutes()->getByName($this->href);

            if ($route) {
                $params = [];
                foreach ($route->parameterNames() as $name) {
                    if (array_key_exists($name, $rowArr)) {
                        $params[$name] = $rowArr[$name];
                    } elseif (array_key_exists('id', $rowArr)) {
                        $params[$name] = $rowArr['id'];
                    }
                }

                return route($this->href, $params);
            }

            return route($this->href, $rowArr);
        } catch (RouteNotFoundException $e) {
            // Fallback to raw string if it's a URL or invalid route
            return $this->href;
        } catch (\InvalidArgumentException $e) {
            return $this->href;
        }
    }

    private function normalizeRow(array|object|null $row): array
    {
        if ($row === null) {
            return [];
        }

        return is_array($row) ? $row : (array) $row;
    }
}

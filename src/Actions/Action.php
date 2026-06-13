<?php

namespace Kinetics\Actions;

/**
 * Represents a single action (button/link) in the action column.
 *
 * Each Action has:
 * - a label and icon for UI
 * - an href or handler for navigation/modal
 * - visibility and disabled conditions (closures evaluated per row)
 * - variants for styling
 */
class Action
{
    private string $key;

    private string $label;

    private ?string $icon = null;

    private string $variant = 'outline';

    /** @var string|\Closure|null */
    private mixed $href = null;

    private string $method = 'get';

    private bool $asModal = false;

    private ?\Closure $visibleWhen = null;

    private ?\Closure $disabledWhen = null;

    private bool $requiresConfirmation = false;

    private ?string $confirmationMessage = null;

    private ?string $confirmationTitle = null;

    private array $meta = [];

    private function __construct(string $key)
    {
        $this->key = $key;
    }

    // Static constructor

    public static function make(string $key): static
    {
        return new static($key);
    }

    // Preset constructors for common actions

    public static function edit(?string $routeName = null): static
    {
        $action = static::make('edit')
            ->label('Edit')
            ->icon('pencil')
            ->variant('outline');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    public static function view(?string $routeName = null): static
    {
        $action = static::make('view')
            ->label('View')
            ->icon('eye')
            ->variant('outline');

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
            ->variant('destructive')
            ->method('delete')
            ->confirm(message: 'Are you sure you want to delete this record?');

        if ($routeName) {
            $action->href($routeName);
        }

        return $action;
    }

    // Fluent API

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set the styling variant.
     *
     * @param  'default'|'destructive'|'ghost'|'outline'  $variant
     */
    public function variant(string $variant): static
    {
        $allowedVariants = ['default', 'destructive', 'ghost', 'outline'];

        if (! in_array($variant, $allowedVariants, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid variant "%s". Allowed variants are: %s', $variant, implode(', ', $allowedVariants))
            );
        }
        $this->variant = $variant;

        return $this;
    }

    /**
     * Route name for the action, or a closure.
     *
     * Example:
     * ->href('users.edit')
     * ->href(fn($row) => route('users.edit', $row['id']))
     */
    public function href(string|\Closure $href): static
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Set method.
     *
     * @param  'get'|'post'|'put'|'patch'|'delete'  $method
     */
    public function method(string $method): static
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Open in modal/dialog instead of navigation.
     */
    public function modal(bool $value = true): static
    {
        $this->asModal = $value;

        return $this;
    }

    /**
     * Per-row visibility conditions.
     *
     * Example:
     * ->visibleWhen(fn($row) => $row->status === 'draft')
     */
    public function visibleWhen(\Closure $condition): static
    {
        $this->visibleWhen = $condition;

        return $this;
    }

    /**
     * Per-row disabled condition.
     *
     * Example:
     * ->disabledWhen(fn($row) => ! auth()->user()->can('edit', $row))
     */
    public function disabledWhen(\Closure $condition): static
    {
        $this->disabledWhen = $condition;

        return $this;
    }

    /**
     * Display a confirmation dialog before the action is executed.
     */
    public function confirm(string $title = 'Are you sure?', string $message = ''): static
    {
        $this->requiresConfirmation = true;
        $this->confirmationMessage = $message;
        $this->confirmationTitle = $title;

        return $this;
    }

    /**
     * Arbitrary metadata sent to FE.
     */
    public function meta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    // Resolve per-row

    /**
     * Resolve action for one row — evaluate all closures.
     */
    public function resolve(array|object $row): array
    {
        $rowArr = is_array($row) ? $row : (array) $row;

        $isVisible = $this->visibleWhen ? ($this->visibleWhen)($row) : true;
        $isDisabled = $this->disabledWhen ? ($this->disabledWhen)($row) : false;

        if (! $isVisible) {
            return [];
        }

        return [
            'key' => $this->key,
            'label' => $this->label,
            'icon' => $this->icon,
            'variant' => $this->variant,
            'href' => $this->resolveHref($rowArr),
            'method' => $this->method,
            'modal' => $this->asModal,
            'disabled' => $isDisabled,
            'confirm' => $this->requiresConfirmation ? [
                'message' => $this->confirmationMessage,
                'title' => $this->confirmationTitle,
            ] : null,
            'meta' => $this->meta,
        ];
    }

    // Internals

    private function resolveHref(array|object $row): ?string
    {
        if (! $this->href) {
            return null;
        }

        if ($this->href instanceof \Closure) {
            return ($this->href)($row);
        }

        $rowArr = is_array($row) ? $row : (array) $row;

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

            return route($this->href, $row);
        } catch (\Throwable $e) {
            // Fallback to raw string if it's a URL or invalid route
            return $this->href;
        }
    }
}

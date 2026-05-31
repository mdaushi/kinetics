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
    private string $variant = 'default';
    private ?string $href = null;
    private string $method = 'get';
    private bool $asModal = false;
    private ?\Closure $visibleWhen = null;
    private ?\Closure $disabledWhen = null;
    private bool $requiresConfirmation = false;
    private ?string $confirmationMessage = null;
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

    public static function edit(?string $routePattern): static
    {
        return static::make('edit')
            ->label('Edit')
            ->icon('pencil')
            ->variant('default')
            ->href($routePattern ?? ':id/edit');
    }

    public static function view(?string $routePattern): static
    {
        return static::make('view')
            ->label('View')
            ->icon('eye')
            ->variant('default')
            ->href($routePattern ?? ':id');
    }

    public static function delete(?string $routePattern): static
    {
        return static::make('delete')
            ->label('Delete')
            ->icon('trash')
            ->variant('destructive')
            ->href($routePattern ?? ':id')
            ->method('delete')
            ->confirm('Are you sure you want to delete this record?');
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
     * @param 'default'|'destructive' $variant
     * @return static
     */
    public function variant(string $variant): static
    {
        $allowedVariants = ['default', 'destructive'];

        if (!in_array($variant, $allowedVariants, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid variant "%s". Allowed variants are: %s', $variant, implode(', ', $allowedVariants))
            );
        }
        $this->variant = $variant;
        return $this;
    }

    /** 
     * URL with :column_key placeholder for dynamic routes. 
     * 
     * Example: 
     * ->href('/users/:id/edit') 
     * ->href('/orgs/:org_id/users/:id') 
     */
    public function href(string $href): static
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Set method.
     *
     * @param 'get'|'post'|'put'|'patch'|'delete' $method
     * @return static
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
    public function confirm(string $message = 'Are you sure?'): static
    {
        $this->requiresConfirmation = true;
        $this->confirmationMessage  = $message;
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
     *
     * @param  array|object $row
     */
    public function resolve(array|object $row): array
    {
        $rowArr = is_array($row) ? $row : (array) $row;

        $isVisible  = $this->visibleWhen  ? ($this->visibleWhen)($row)  : true;
        $isDisabled = $this->disabledWhen ? ($this->disabledWhen)($row) : false;

        if (! $isVisible) {
            return [];
        }

        return [
            'key'      => $this->key,
            'label'    => $this->label,
            'icon'     => $this->icon,
            'variant'  => $this->variant,
            'href'     => $this->resolveHref($rowArr),
            'method'   => $this->method,
            'modal'    => $this->asModal,
            'disabled' => $isDisabled,
            'confirm'  => $this->requiresConfirmation ? [
                'message' => $this->confirmationMessage,
            ] : null,
            'meta'     => $this->meta,
        ];
    }

    // Internals

    /**
     * Replace the :column_key placeholder with the actual value from the row.
     * Example: '/users/:id/edit' + ['id' => 5] → '/users/5/edit'
     */
    private function resolveHref(array $row): ?string
    {
        if (! $this->href) {
            return null;
        }

        return preg_replace_callback('/:([a-zA-Z_]+)/', function (array $matches) use ($row) {
            $key = $matches[1];
            return $row[$key] ?? $matches[0];
        }, $this->href);
    }
}

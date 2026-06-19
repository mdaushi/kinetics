<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Concerns\HasConfirmation;
use Kinetics\Actions\Concerns\HasDisplayMode;
use Kinetics\Actions\Enums\ActionVariant;

abstract class BaseAction
{
    use HasConfirmation, HasDisplayMode;

    protected string $key;

    protected string $label;

    protected ?string $icon = null;

    protected mixed $href = null;

    protected ActionVariant $variant = ActionVariant::DEFAULT;

    protected string $method = 'get';

    /** @var bool|\Closure */
    protected mixed $visibleWhen = true;

    /** @var bool|\Closure */
    protected mixed $disabledWhen = false;

    protected array $meta = [];

    protected function __construct(string $key)
    {
        $this->key = $key;
        $this->label = str(str_replace('.', '_', $key))->replace('_', ' ')->title()->toString();
    }

    public static function make(string $key): static
    {
        return new static($key);
    }

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

    public function getVariant(): ActionVariant
    {
        return $this->variant;
    }

    public function variant(ActionVariant|string $variant): static
    {
        if (is_string($variant)) {
            $variant = ActionVariant::from($variant);
        }

        $this->variant = $variant;

        return $this;
    }

    /*
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
     * @param  'get'|'post'|'put'|'patch'|'delete'  $method
     */
    public function method(string $method): static
    {
        $this->method = strtolower($method);

        return $this;
    }

    public function meta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Set visibility condition. Can be a boolean or a closure.
     * Closure receives the row data for row actions, or null for global actions.
     */
    public function visibleWhen(bool|\Closure $condition): static
    {
        $this->visibleWhen = $condition;

        return $this;
    }

    /**
     * Set disabled condition. Can be a boolean or a closure.
     * Closure receives the row data for row actions, or null for global actions.
     */
    public function disabledWhen(bool|\Closure $condition): static
    {
        $this->disabledWhen = $condition;

        return $this;
    }

    protected function evaluateCondition(mixed $condition, array|object|null $row = null): bool
    {
        if (is_bool($condition)) {
            return $condition;
        }

        if ($condition instanceof \Closure) {
            return (bool) $condition($row);
        }

        return true;
    }

    protected function isVisible(array|object|null $row = null): bool
    {
        return $this->evaluateCondition($this->visibleWhen, $row);
    }

    protected function isDisabled(array|object|null $row = null): bool
    {
        return $this->evaluateCondition($this->disabledWhen, $row);
    }

    /**
     * Resolve the basic attributes shared across all action types.
     */
    protected function resolveBaseAttributes(): array
    {
        return array_merge(
            $this->resolveDisplayModeAttributes(),
            [
                'type' => 'action',
                'key' => $this->key,
                'label' => $this->label,
                'icon' => $this->icon,
                'variant' => $this->variant->value,
                'method' => $this->method,
                'confirm' => $this->resolveConfirm(),
                'meta' => $this->meta,
            ]
        );
    }
}

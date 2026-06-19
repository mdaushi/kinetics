<?php

namespace Kinetics\Actions;

use Kinetics\Actions\Concerns\HasDisplayMode;
use Kinetics\Actions\Enums\ActionVariant;

abstract class BaseActionGroup
{
    use HasDisplayMode;

    protected ActionVariant $variant = ActionVariant::DEFAULT;

    protected string $label = 'Actions';

    protected ?string $icon = 'ellipsis-vertical';

    protected array $actions = [];

    protected function __construct(string $label)
    {
        $this->label = str(str_replace('.', '_', $label))->replace('_', ' ')->title()->toString();
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

    public function variant(ActionVariant|string $variant): static
    {
        if (is_string($variant)) {
            $variant = ActionVariant::from($variant);
        }

        $this->variant = $variant;

        return $this;
    }

    public function actions(array $actions): static
    {
        foreach ($actions as $action) {
            if (method_exists($action, 'getVariant')) {
                $variant = $action->getVariant();

                if (! in_array($variant, [ActionVariant::DEFAULT, ActionVariant::DESTRUCTIVE], true)) {
                    throw new \InvalidArgumentException(
                        "Variant '{$variant->value}' cannot be used inside an Action Group. Use ActionVariant::DEFAULT or ActionVariant::DESTRUCTIVE."
                    );
                }
            }
        }

        $this->actions = $actions;

        return $this;
    }

    protected function resolveBaseAttributes(): array
    {
        return array_merge([
            'type' => 'group',
            'label' => $this->label,
            'icon' => $this->icon,
            'variant' => $this->variant->value,
        ], $this->resolveDisplayModeAttributes());
    }
}

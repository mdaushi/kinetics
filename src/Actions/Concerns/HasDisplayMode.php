<?php

namespace Kinetics\Actions\Concerns;

trait HasDisplayMode
{
    protected bool $iconOnly = false;

    protected bool $textOnly = false;

    protected bool $iconOnlyOnMobile = false;

    /**
     * Display only the icon (hide the text/label).
     */
    public function iconOnly(bool $condition = true): static
    {
        $this->iconOnly = $condition;

        if ($condition) {
            $this->textOnly = false;
        }

        return $this;
    }

    /**
     * Display only the text/label (hide the icon).
     */
    public function textOnly(bool $condition = true): static
    {
        $this->textOnly = $condition;

        if ($condition) {
            $this->iconOnly = false;
        }

        return $this;
    }

    /**
     * Responsive: collapse to icon-only on mobile devices (sm/md screens).
     */
    public function iconOnlyOnMobile(bool $condition = true): static
    {
        $this->iconOnlyOnMobile = $condition;

        return $this;
    }

    public function isIconOnly(): bool
    {
        return $this->iconOnly;
    }

    public function isTextOnly(): bool
    {
        return $this->textOnly;
    }

    public function isIconOnlyOnMobile(): bool
    {
        return $this->iconOnlyOnMobile;
    }

    protected function resolveDisplayModeAttributes(): array
    {
        return [
            'icon_only' => $this->iconOnly,
            'text_only' => $this->textOnly,
            'icon_only_on_mobile' => $this->iconOnlyOnMobile,
        ];
    }
}

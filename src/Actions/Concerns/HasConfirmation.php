<?php

namespace Kinetics\Actions\Concerns;

/**
 * Shared confirmation dialog behavior for all action types.
 */
trait HasConfirmation
{
    private bool $requiresConfirmation = false;

    private ?string $confirmationTitle = null;

    private ?string $confirmationMessage = null;

    /**
     * Display a confirmation dialog before the action is executed.
     */
    public function confirm(string $title = 'Are you sure?', string $message = ''): static
    {
        $this->requiresConfirmation = true;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;

        return $this;
    }

    protected function resolveConfirm(): ?array
    {
        if (! $this->requiresConfirmation) {
            return null;
        }

        return [
            'title' => $this->confirmationTitle,
            'message' => $this->confirmationMessage,
        ];
    }
}

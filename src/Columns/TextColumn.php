<?php

namespace Kinetics\Columns;

class TextColumn extends Column
{
    protected ?string $type = 'text';

    /**
     * Render this column as a badge.
     */
    public function badge(bool $condition = true): static
    {
        if ($condition) {
            $this->type = 'badge';
        }
        return $this;
    }

    /**
     * Set a static color or map specific values to badge colors.
     * Example: 'default' or ['draft' => 'secondary', 'published' => 'default']
     *
     * @param 'default'|'secondary'|'destructive'|'outline'|'ghost'|'link'|array<string,
     * 'default'|'secondary'|'destructive'|'outline'|'ghost'|'link'> $color
     */
    public function color(string|array $color): static
    {
        if ($this->type !== 'badge') {
            throw new \LogicException("The color() method can only be called after calling badge().");
        }

        $this->meta('color', $color);
        return $this;
    }

    /**
     * Format the column as a date.
     */
    public function date(string $format = 'Y-m-d'): static
    {
        $this->formatUsing(function ($value) use ($format) {
            if (empty($value)) {
                return $value;
            }
            try {
                return \Carbon\Carbon::parse($value)->format($format);
            } catch (\Exception $e) {
                return $value;
            }
        });

        return $this;
    }

    /**
     * Format the column as time.
     */
    public function time(string $format = 'H:i:s'): static
    {
        return $this->date($format);
    }

    /**
     * Format the column as datetime.
     */
    public function dateTime(string $format = 'Y-m-d H:i:s'): static
    {
        return $this->date($format);
    }
}

<?php

namespace Kinetics\Support;

class TableConfig
{
    public function __construct(
        public readonly int $defaultPerPage = 15,
        public readonly int $maxPerPage = 100,
        public readonly string $defaultSort = 'id',
        public readonly string $defaultDirection = 'desc',
        public readonly bool $preserveKeys = false,
        public readonly array $optionsPerPage = [10, 15, 25, 50, 100]
    ) {
    }

    /**
     * Return a new instance with the given fields overridden.
     * Keeps all other values intact — avoids reconstructing the whole object.
     */
    public function with(
        ?int $defaultPerPage = null,
        ?int $maxPerPage = null,
        ?string $defaultSort = null,
        ?string $defaultDirection = null,
    ): static {
        return new static(
            defaultPerPage: $defaultPerPage ?? $this->defaultPerPage,
            maxPerPage: $maxPerPage ?? $this->maxPerPage,
            defaultSort: $defaultSort ?? $this->defaultSort,
            defaultDirection: $defaultDirection ?? $this->defaultDirection,
            preserveKeys: $this->preserveKeys,
            optionsPerPage: $this->optionsPerPage,
        );
    }

    // public static function fromArray(array $config): static
    // {
    //     return new static(
    //         defaultPerPage: $config['default_per_page'] ?? 15,
    //         maxPerPage: $config['max_per_page'] ?? 100,
    //         defaultSort: $config['default_sort'] ?? 'id',
    //         defaultDirection: $config['default_direction'] ?? 'desc',
    //         preserveKeys: $config['preserve_keys'] ?? false,
    //     );
    // }
}

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
    ) {}

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

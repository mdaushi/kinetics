<?php

return [
    'default_per_page' => 15,
    'max_per_page' => 100,
    'default_sort' => 'id',
    'default_direction' => 'desc',
    'options_per_page' => [10, 15, 25, 50, 100],

    /*
    | Default output directory (relative to app/) when running kinetics:pipe.
    | This can be overridden per-call with the --path option.
    */
    'pipe_path' => 'Pipes',
];

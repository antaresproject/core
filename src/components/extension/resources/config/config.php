<?php

return [
    'reserved' => [
        'resources',
        'antares/asset',
        'antares/auth',
        'antares/debug',
        'antares/extension',
        'antares/facile',
        'antares/foundation',
        'antares/html',
        'antares/memory',
        'antares/messages',
        'antares/model',
        'antares/notifier',
        'antares/optimize',
        'antares/platform',
        'antares/resources',
        'antares/support',
        'antares/testbench',
        'antares/view',
        'antares/widget',
    ],
    'paths'    => [
        'src/core/src/modules/*',
        'src/modules/*',
    ],
    'composer' => [
        'parameters' => [
            '--no-interaction',
            '--no-ansi',
            '--prefer-source',
        ],
    ],
];

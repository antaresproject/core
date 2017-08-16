<?php

return [
    'menu' => [
        /*
        |--------------------------------------------------------------------------
        | Rendering options
        |--------------------------------------------------------------------------
        |
        | For more information see: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown#other-rendering-options
        |
        */
        'render' => [
            'depth'             => null,
            'currentAsLink'     => true,
            'currentClass'      => 'current active',
            'ancestorClass'     => 'current_ancestor',
            'firstClass'        => 'first',
            'lastClass'         => 'last',
            'compressed'        => false,
            'allow_safe_labels' => true,
            'clear_matcher'     => true
        ]
    ],

    'priorities' => [
        'main-menu',
        'breadcrumb',
        'page-menu',
    ],

];
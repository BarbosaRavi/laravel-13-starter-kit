<?php

return [
    'permissions' => [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',

        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
    ],

    'roles' => [
        'sys_admin' => ['*'],

        'admin' => [
            'users.view',
            'users.create',
            'users.update',
            'roles.view',
        ],

        'user' => [
            'users.view',
        ],
    ],
];
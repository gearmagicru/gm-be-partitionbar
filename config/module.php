<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'   => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: view, read, add, edit, delete, clear)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Grid'           => ['data', 'supplement', 'view', 'update', 'delete', 'clear', 'filter'],
                'Form'           => ['data', 'view', 'add', 'update', 'delete'],
                'Trigger'        => ['combo'],
                'Search'         => ['data', 'view'],
                'ItemsGrid'      => ['data', 'view', 'update'],
                'ExtensionsGrid' => ['data', 'view', 'update'],
                'ModulesGrid'    => ['data', 'view', 'update'],
                'RolesGrid'      => ['data', 'view', 'update']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Просмотр" (view)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Grid'           => ['data', 'supplement', 'view', 'filter'],
                'Form'           => ['data', 'view'],
                'Trigger'        => ['combo'],
                'Search'         => ['data', 'view'],
                'ItemsGrid'      => ['data', 'view'],
                'ExtensionsGrid' => ['data', 'view'],
                'ModulesGrid'    => ['data', 'view'],
                'RolesGrid'      => ['data', 'view']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Чтение" (read)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Grid'           => ['data'],
                'Form'           => ['data'],
                'Trigger'        => ['combo'],
                'Search'         => ['data'],
                'ItemsGrid'      => ['data'],
                'ExtensionsGrid' => ['data'],
                'ModulesGrid'    => ['data'],
                'RolesGrid'      => ['data']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Добавление" (add)
            'allow',
            'permission'  => 'add',
            'controllers' => [
                'Form' => ['add']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Изменение" (edit)
            'allow',
            'permission'  => 'edit',
            'controllers' => [
                'Grid' => ['update'],
                'Form' => ['update']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Удаление" (delete)
            'allow',
            'permission'  => 'delete',
            'controllers' => [
                'Grid' => ['delete'],
                'Form' => ['delete']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Очистка" (clear)
            'allow',
            'permission'  => 'clear',
            'controllers' => [
                'Grid' => ['clear']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // разрешение "Настройка модуля" (settings)
            'allow',
            'permission'  => 'settings',
            'controllers' => ['Settings'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'viewManager' => [
        'id'          => 'gm-partitionbar-{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
            'form' => '/form.json'
        ]
    ]
];

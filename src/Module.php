<?php
/**
 * Модуль веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Partitionbar;

/**
 * Модуль Панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar
 * @since 1.0
 */
class Module extends \Gm\Panel\Module\Module
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.partitionbar';

    /**
     * {@inheritdoc}
     */
    public function controllerMap(): array
    {
        return [
            'roles'      => 'RolesGrid', // роли пользователей раздела
            'modules'    => 'ModulesGrid', // модули раздела
            'extensions' => 'ExtensionsGrid', // расширения раздела
        ];
    }
}

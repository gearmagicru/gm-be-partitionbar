<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Partitionbar\Model\Workspace;

use Gm;
use Gm\Mvc\Module\BaseModule;
use Gm\Data\Model\BaseModel as DataModel;

/**
 * Модель данных элементов панели пуска рабочего пространства пользователя.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Model\Workspace
 * @since 1.0
 */
class Bootslide extends DataModel
{
    /**
     * @var BaseModule|\Gm\Backend\Partitionbar\Module
     */
    public BaseModule $module;

    /**
     * Возвращает элементы панели пуска c сылками на модули и плагины, доступными 
     * для текущей роли пользователя.
     * 
     * Элементы панели пуска - элементы панели разделов для которых родитель элемент "конфигурация".
     * 
     * Результат:
     * ```php
     * return [
     *     [
     *        'название раздела',
    *         'items' => [
    *             ['пункт меню 1'],
    *             ['пункт меню 2'],
    *             ...
    *         ]
     *     ],
     *     ...
     * ];
     * ```
     * @return array
     */
    public function getItems(): array
    {
        /** 
         * @var \Gm\Backend\Partitionbar\Module|null $modulePbar Модуль панели разделов. 
         * Для локализации названий разделов. 
         */
        $modulePbar = Gm::$app->modules->get('gm.be.partitionbar');
        if ($modulePbar === null) {
            return [];
        }

        /** @var \Gm\Backend\Partitionbar\Model\Partitionbar $pbar */
        $pbar = $this->module->getModel('Partitionbar');
        /** @var \Gm\Db\ActiveRecord $configuration элемент панели разделов "конфигурация" */
        $configuration = $pbar->selectOne(['code' => 'settings']);
        if ($configuration === null || empty($configuration->id)) {
            return [];
        }
        // элементы панели разделов (идентификаторы) у которых родитель "конфигурация"
        $configurationItems = $pbar->getRecursiveChildren($configuration->id, true, true);
        if (empty($configurationItems)) {
            return [];
        }
        $configurationIds = array_fill_keys($configurationItems, true);

        /** @var \Gm\Db\Sql\Select $select */
        $select = $pbar
            ->select(
                ['*'],
                [
                    'id'      => $pbar->getAccessible(),
                    'visible' => 1
                ]
            )
            ->order(['index' => 'ASC']);
        /** @var array $items доступные элементы панели разделов */
        $items = $pbar
            ->getDb()
                ->createCommand($select)
                    ->queryAll($pbar->primaryKey());

        // все доступные пользователю модули, сгруппированные по идентификатору 
        // элемента панели разделов
        $modules = Gm::tempGet('pbarModules', function () use ($pbar) {
            return $pbar->getModules(true, true); 
        });
        // все доступные пользователю расширения модулей, сгруппированные по идентификатору
        // элемента панели разделов 
        $extensions = Gm::tempGet('pbarExtensions', function () use ($pbar) {
            return $pbar->getExtensions(true, true); 
        });

        $partitions = [];
        foreach ($items as $item) {
            // идент. ({{panel_partitionbar}}.id) элемента панели разделов
            $id = $item['id'];
            // если элемент панели разделов не принадлежит родителю "конфигурация"
            if (!isset($configurationIds[$id])) continue;
            // меню кнопки
            $partitionItems = [];
            // если раздел имеет модули
            if (!empty($modules[$id])) {
                // добавляем подпункты
                foreach ($modules[$id] as $module) {
                    $partitionItems[] = [
                        'purpose'     => 'module',
                        'text'        => $module['name'],
                        'description' => $module['description'],
                        'icon'        => $module['icon'],
                        'handler'     => 'loadWidget',
                        'handlerArgs' => ['route' => '@backend/'. $module['route']]
                    ];
                }
            }
            // если раздел имеет расширения модулей
            if (!empty($extensions[$id])) {
                // добавляем подпункты
                foreach ($extensions[$id] as $extension) {
                    $partitionItems[] = [
                        'purpose'     => 'configuration',
                        'text'        => $extension['name'],
                        'description' => $extension['description'],
                        'icon'        => $extension['icon'],
                        'handler'     => 'loadWidget',
                        'handlerArgs' => ['route' => '@backend/'. $extension['baseRoute']]
                    ];
                }
            }

            if ($partitionItems) {
                $partitions[] = [
                    'title' => $modulePbar->tH($item['name']),
                    'items' => $partitionItems
                ];
            }
        }
        return $partitions;
    }
}

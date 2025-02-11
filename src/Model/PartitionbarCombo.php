<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Partitionbar\Model;

use Gm\Panel\Data\Model\TreeComboModel;

/**
 * Модель данных выпадающего списка панелей разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Model
 * @since 1.0
 */
class PartitionbarCombo extends TreeComboModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar}}',
            'primaryKey' => 'id',
            'parentKey'  => 'parent_id',
            'order'      => ['name' => 'index'],
            'fields'     => [
                ['name']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTreeNodes(): array
    {
        $treeNodes = parent::getTreeNodes();

        if (isset($treeNodes['nodes'])) {
            $treeNodes['nodes']  = $this->module->tH($treeNodes['nodes']);
        }
        return $treeNodes;
    }
}

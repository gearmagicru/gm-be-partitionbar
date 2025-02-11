<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Partitionbar\Model;

use Gm;
use Gm\Panel\Data\Model\GridModel;
use Gm\Db\Sql;

/**
 * Модель данных списка расширений модулей панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Model
 * @since 1.0
 */
class ExtensionsGrid extends GridModel
{
    /**
     * Выбранный раздел.
     *
     * @var array
     */
    public array $partition = [];

    /**
     * {@inheritdoc}
     * 
     * не задействован, т.к. вся реализация в {@see ExtensionsGrid::fetchRows()}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName' => '{{panel_partitionbar_extensions}}',
            'useAudit'  => false,
            'fields'    => [
                ['name'],
                ['path'],
                ['route'],
                ['icon'],
                [
                    'partition_id',
                    'alias' => 'partitionId'
                ],
                [
                    'extension_id',
                    'alias' => 'extensionId'
                ],
                ['available']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->partition = $this->module->getStorage()->partition;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilter(Sql\AbstractSql $operator): void
    {
        $operator
            ->where(['partition_id' => $this->partition['id']]);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRows(mixed $receiver = null): array
    {
        $rows = [];
        // конфигурации установленных расширений
        $extensionsInfo = Gm::$app->extensions->getRegistry()->getListInfo(true, false);
        $partitionItems = $receiver->queryAll('extension_id');

        /**
         * @var array $moduleNames Имена и описание модулей в текущей локализации. 
         * Имеет вид: `[module_id => ['name' => 'Name', ...], ...]`.
         */
        $moduleNames = Gm::$app->modules->getRegistry()->getListNames();
        foreach ($extensionsInfo as $rowId => $extensionInfo) {
            $moduleRowId = $extensionInfo['moduleRowId'];
            $rows[] = [
                'id'          => $rowId,
                'extensionId' => $rowId,
                'partitionId' => $this->partition['id'],
                'icon'        => $extensionInfo['smallIcon'],
                'name'        => $extensionInfo['name'],
                'module'      => $moduleNames[$moduleRowId]['name'] ?? SYMBOL_NONAME,
                'available'   => (int) isset($partitionItems[$rowId])
            ];
        }
        return $rows;
    }
}

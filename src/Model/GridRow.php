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
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля записи панели раздела.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Model
 */
class GridRow extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'],
                [
                    'visible',
                    'alias' => 'isVisible'
                ],
                ['name']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    if (isset($columns['visible'])) {
                        $visible = (int) $columns['visible'];
                        $message['message'] = $this->module->t('Partition element {0} - ' . ($visible > 0 ? 'show' : 'hide'), [$this->name]);
                        $message['title']   = $this->t($visible > 0 ? 'Show' : 'Hide');
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }
}

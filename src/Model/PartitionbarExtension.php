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
use Closure;
use Gm\Db\Sql\Where;
use Gm\Db\Sql\Select;
use Gm\Db\ActiveRecord;

/**
 * Модель данных связи расширений с элементами панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Model
 * @since 1.0
 */
class PartitionbarExtension extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'extensionId';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{panel_partitionbar_extensions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'partitionId' => 'partition_id',
            'extensionId' => 'extension_id'
        ];
    }

    /**
     * Возвращает запись по указанному идентификатору элемента панели раздела и 
     * расширения модуля.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param int|string $partitionId Идентификатор элемента панели разделов.
     * @param int $extensionId Идентификатор расширения модуля.
     * 
     * @return PartitionbarExtension|null Активная запись при успешном запросе, иначе `null`.
     */
    public function get(int $partitionId, int $extensionId): ?static
    {
        return $this->selectOne([
            'partition_id' => $partitionId,
            'extension_id' => $extensionId
        ]);
    }

    /**
     * {@inheritdoc}
     * 
     * @param bool $accessible Если `true`, возвратит все доступные расширения элементов панели 
     *     разделов для текущей роли пользователя (по умолчанию `true`).
     */
    public function fetchAll(
        string $fetchKey = null, 
        array $columns = ['*'], 
        Where|Closure|string|array|null $where = null, 
        string|array|null $order = null,
        bool $accessible = true
    ): array
    {
        /** @var Select $select */
        $select = $this->select($columns, $where);
        if ($order)
            $select->order($order);
        // проверка доступа
        if ($accessible) {
            // доступные пользователю идентификаторы расширений модулей
            $extensionsId = Gm::userIdentity()->getExtensions();
            if ($extensionsId)
                $select->where("extension_id IN ($extensionsId)");
            // нет доступных расширений модулей
            else
                return [];
        }
        return $this
            ->getDb()
                ->createCommand($select)
                    ->queryAll($fetchKey);
    }

    /**
     * Возвращает все доступные идентификаторы расширений модулей, соответствующие 
     * элементам панели разделов для текущей роли пользователя.
     * 
     * @param bool $group Если `true`, группирует результат по идентификатору элементов 
     *     панели разделов (по умолчанию `true`).
     * @param bool $toString Если `true`, возвратит идентификаторы через разделитель ',' 
     *     (по умолчанию `false`).
     * 
     * @return array|string
     */
    public function getAccessibleItems(bool $group = true, bool $toString = false): array|string
    {
        // доступные пользователю идентификаторы расширений модулей
        $extensionsId = Gm::userIdentity()->getExtensions();
        if (empty($extensionsId)) {
            return $toString ? '' : [];
        }
        /** @var \Gm\Db\Adapter\Adapter $db */
        $db = $this->getDb();
        /** @var \Gm\Db\Sql\Select $select */
        $select = $db
            ->select($this->tableName())
            ->columns(['*'])
            ->where("extension_id IN ($extensionsId)");
        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db
            ->createCommand($select)
                ->query();
        $rows = [];
        while ($row = $command->fetch()) {
            if ($group) {
                $id = $row['partition_id'];
                if (!isset($rows[$id])) {
                    $rows[$id] = [];
                }
                $rows[$id][] = $row['extension_id'];
            } else {
                $rows[] = $row['extension_id'];
            }
        }
        if ($group) {
            if ($toString) {
                foreach ($rows as $id => &$modules) {
                    $modules = implode(',', $modules);
                }
            } else
                return $rows;
        } else {
            return $toString ? implode(',', $rows) : $rows;
        }
    }

    /**
     * Возвращает идентификаторы модулей, соответствующие элементам панели разделов 
     * для текущей роли пользователя.
     * 
     * @param bool $group Если `true`, группирует результат по идентификатору элементов 
     *     панели разделов (по умолчанию `true`).
     * @param bool $toString Если `true`, возвратит идентификаторы через разделитель ',' 
     *     (по умолчанию `false`).
     * 
     * @return array|string
     */
    public function getItems(bool $group = true, bool $toString = false): array|string
    {
        /** @var \Gm\Db\Adapter\Adapter $db */
        $db = $this->getDb();
        /** @var \Gm\Db\Sql\Select $select */
        $select = $db
            ->select($this->tableName())
            ->columns(['*']);
        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db
            ->createCommand($select)
                ->query();
        $rows = [];
        while ($row = $command->fetch()) {
            if ($group) {
                $id = $row['partition_id'];
                if (!isset($rows[$id])) {
                    $rows[$id] = [];
                }
                $rows[$id][] = $row['module_id'];
            } else {
                $rows[] = $row['module_id'];
            }
        }
        if ($group) {
            foreach ($rows as $id => &$modules) {
                $modules = implode(',', $modules);
            }
        } else {
            return $toString ? implode(',', $rows) : $rows;
        }
    }


    /**
     * Возвращает все записи (модули) соответствующие элементам панели разделов.
     * 
     * Ключом каждой записи является значение первичного ключа {@see ActiveRecord::tableName()} 
     * текущей таблицы.
     * 
     * @see ActiveRecord::fetchAll()
     * 
     * @param bool $caching Указывает на принудительное кэширование. Если служба кэширования 
     *     отключена, кэширование не будет выполнено (по умолчанию `true`).
     * 
     * @return array
     */
    public function getAll(bool $caching = true, bool $accessible = true): ?array
    {
        if ($caching)
            return $this->cache(
                function () use ($accessible) {
                    return $this->fetchAll(null, $this->maskedAttributes(), null, null, $accessible); 
                },
                null,
                true
            );
        else
            return $this->fetchAll(null, $this->maskedAttributes(), null, null, $accessible);
    }

    /**
     * Удаляет записи по указанному идентификатору расширения.
     * 
     * @param int $extensionId Идентификатор расширения.
     * 
     * @return false|int Возвращает значение `false`, если ошибка выполнения запроса. 
     *     Иначе, количество удалённых записей.
     */
    public function deleteByExtension(int $extensionId): false|int
    {
        return $this->deleteRecord(['extension_id' => $extensionId]);
    }

    /**
     * Удаляет записи по указанному идентификатору раздела.
     * 
     * @param int $partitionId Идентификатор раздела.
     * 
     * @return bool|int Возвращает значение `false`, если ошибка выполнения запроса. 
     *     Иначе, количество удалённых записей.
     */
    public function deleteByPartition(int $partitionId): false|int
    {
        return $this->deleteRecord(['partition_id' => $partitionId]);
    }
}

<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Partitionbar\Controller;

use Gm;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Panel\Helper\ExtCombo;
use Gm\Panel\Widget\TabTreeGrid;
use Gm\Panel\Data\Model\FormModel;
use Gm\Panel\Helper\ExtGridTree as ExtGrid;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;
use Gm\Panel\Controller\TreeGridController;

/**
 * Контроллер панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Controller
 * @since 1.0
 */
class Grid extends TreeGridController
{
    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, string $default = null): ?string
    {
        switch ($this->actionName) {
            // изменение записи по указанному идентификатору
            case 'update':
                /** @var FormModel $model */
                $model = $this->lastDataModel;
                if ($model instanceof FormModel) {
                    $event   = $model->getEvents()->getLastEvent(true);
                    $columns = $event['columns'];
                    // если изменение видимости раздела
                    if (isset($columns['visible'])) {
                        $visible = (int) $columns['visible'];
                        return $this->module->t('partition element {0} is ' . ($visible > 0 ? 'shown' : 'hidden'), [$model->name]);
                    }
                }

            default:
                return parent::translateAction($params, $default);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabTreeGrid
    {
       /** @var TabTreeGrid $tab Сетка данных в виде дерева (Gm.view.grid.Tree Gm JS) */
        $tab = parent::createWidget();

        // столбцы (Gm.view.grid.Tree.columns GmJS)
        $tab->treeGrid->columns = [
            ExtGrid::columnAction(),
            [
                'xtype' => 'g-gridcolumn-control',
                'width' => 80,
                'items' => [
                    [
                        'iconCls'   => 'g-icon-svg g-icon_extension_small',
                        'dataIndex' => 'extensionsUrl',
                        'tooltip'   => '#Partition extensions',
                        'handler'   => 'loadWidgetFromCell'
                    ],
                    [
                        'iconCls'   => 'g-icon-svg g-icon_module_small',
                        'dataIndex' => 'modulesUrl',
                        'tooltip'   => '#Partition modules',
                        'handler'   => 'loadWidgetFromCell'
                    ],
                    [
                        'iconCls'   => 'g-icon-svg g-icon_user-roles_small',
                        'dataIndex' => 'rolesUrl',
                        'tooltip'   => '#Partition roles',
                        'handler'   => 'loadWidgetFromCell'
                    ]
                ]
            ],
            [
                'text'      => '№',
                'tooltip'   => '#Index number',
                'dataIndex' => 'itemIndex',
                'filter'    => ['type' => 'numeric'],
                'width'     => 70
            ],
            [
                'xtype'     => 'treecolumn',
                'text'      => ExtGrid::columnInfoIcon($this->t('Name')),
                'cellTip'   => HtmlGrid::tags([
                    HtmlGrid::header('{name}'),
                    HtmlGrid::fieldLabel($this->module->t('Name') . ' (' . Gm::$app->language->name . ')', '{nameLo}'),
                    HtmlGrid::fieldLabel($this->t('Icon'), '{iconTag}'),
                    HtmlGrid::fieldLabel($this->t('Code'), '{code}'),
                    HtmlGrid::fieldLabel($this->t('Subpartition count'), '{count}'),
                    HtmlGrid::fieldLabel(
                        $this->t('Visible'),
                        HtmlGrid::tplChecked('isVisible==1')
                    )
                ]),
                'dataIndex' => 'name',
                'filter'    => ['type' => 'string'],
                'width'     => 300
            ],
            [
                'text'      => $this->module->t('Name') . ' (' . Gm::$app->language->name . ')',
                'dataIndex' => 'nameLo',
                'cellTip'   => '{nameLo}',
                'width'     => 200
            ],
            [
                'xtype'     => 'templatecolumn',
                'tpl'       => '{iconTag} {icon}',
                'text'      => '#Icon / Image',
                'dataIndex' => 'icon',
                'cellTip'   => '{icon}',
                'filter'    => ['type' => 'string'],
                'width'     => 160
            ],
            [
                'text'      => '#Code',
                'dataIndex' => 'code',
                'cellTip'   => '{code}',
                'filter'    => ['type' => 'string'],
                'width'     => 160
            ],
            [
                'xtype'      => 'templatecolumn',
                'text'       => '#Roles',
                'dataIndex'  => 'roles',
                'hidden'     => true,
                'tpl'        => HtmlGrid::tpl(
                    '<div class="gm-partitionbar-grid-cell__items">' . ExtGrid::renderIcon('g-icon_size_16 g-icon_gridcolumn-user-roles', 'svg') . ' {.}</div>',
                    ['for' => 'roles']
                ),
                'supplement' => true,
                'width'      => 200
            ],
            [
                'xtype'      => 'templatecolumn',
                'text'       => '#Modules',
                'dataIndex'  => 'modules',
                'hidden'     => true,
                'tpl'        => HtmlGrid::tpl(
                    '<div class="gm-partitionbar-grid-cell__items">' . ExtGrid::renderIcon('g-icon_size_16 g-icon_module_small', 'svg') . ' {.}</div>',
                    ['for' => 'modules']
                ),
                'supplement' => true,
                'width'      => 200
            ],
            [
                'xtype'      => 'templatecolumn',
                'text'       => '#Extensions',
                'dataIndex'  => 'extensions',
                'hidden'     => true,
                'tpl'        => HtmlGrid::tpl(
                    '<div class="gm-partitionbar-grid-cell__items">' . ExtGrid::renderIcon('g-icon_size_16 g-icon_extension_small', 'svg') . ' {.}</div>',
                    ['for' => 'extensions']
                ),
                'supplement' => true,
                'width'      => 200
            ],
            [
                'text'      => ExtGrid::columnIcon('g-icon-m_nodes', 'svg'),
                'tooltip'   => '#Number of items in a section',
                'align'     => 'center',
                'dataIndex' => 'count',
                'filter'    => ['type' => 'numeric'],
                'width'     => 60
            ],
            [
                'text'      => ExtGrid::columnIcon('g-icon-m_visible', 'svg'),
                'xtype'     => 'g-gridcolumn-switch',
                'tooltip'   => '#Show / hide element',
                'selector'  => 'treepanel',
                'dataIndex' => 'isVisible',
                'filter'    => ['type' => 'boolean']
            ]
        ];

        // панель инструментов (Gm.view.grid.Tree.tbar GmJS)
        $tab->treeGrid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit',
                'columns',
                'search' => [
                    'items' => [
                        'help',
                        'search',
                        // инструмент "Фильтр"
                        'filter' => ExtGrid::popupFilter([
                            ExtCombo::trigger('#Partition', 'id', 'parentPartition')
                        ])
                    ]
                ]
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Tree.popupMenu GmJS)
        $tab->treeGrid->popupMenu = [
            'items' => [
                [
                    'text'        => '#Edit record',
                    'iconCls'     => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/form/view/{id}'),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ],
                '-',
                [
                    'text' => '#Partition extensions',
                    'iconCls'     => 'g-icon-svg g-icon_extension_small',
                    'handlerArgs' => [
                        'route'   => Gm::alias('@match', '/extensions/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ],
                [
                    'text' => '#Partition modules',
                    'iconCls'     => 'g-icon-svg g-icon_module_small',
                    'handlerArgs' => [
                        'route'   => Gm::alias('@match', '/modules/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ],
                [
                    'text'        => '#Partition roles',
                    'iconCls'     => 'g-icon-svg g-icon_user-roles_small',
                    'handlerArgs' => [
                        'route'   => Gm::alias('@match', '/roles/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ]
            ]
        ];

        // поле аудита записи
        $tab->treeGrid->logField = 'name';
        // количество строк в сетке
        $tab->treeGrid->store->pageSize = 50;
        // плагины сетки
        $tab->treeGrid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->treeGrid->bodyCls = 'g-grid_background';
        $tab->treeGrid->columnLines  = true;
        $tab->treeGrid->rowLines     = true;
        $tab->treeGrid->lines        = true;
        $tab->treeGrid->singleExpand = false;

        // панель вкладки компонента (Gm.view.tab.Widgets Gm JS)
        // навигатор панели
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            '<div class="gm-partitionbar-navinfo__icon">{iconTag}</div>',
            HtmlNav::header('{name}'),
            ['fieldset',
                [
                    HtmlNav::fieldLabel($this->module->t('Name') . ' (' . Gm::$app->language->name . ')', '{nameLo}'),
                    HtmlNav::fieldLabel($this->t('Index number'), '{index}'),
                    HtmlNav::fieldLabel($this->t('Code'), '{code}'),
                    HtmlNav::fieldLabel(
                        ExtGrid::columnIcon('g-icon-m_visible', 'svg') . ' ' . $this->t('Visible'),
                        HtmlNav::tplChecked('visible==1')
                    ),
                    HtmlNav::fieldLabel(
                        ExtGrid::columnIcon('g-icon-m_nodes', 'svg') . ' ' . $this->t('Number of items in a section'),
                        '{count}'
                    ),
                ]
            ],
            ['fieldset',
                [
                    HtmlNav::legend($this->t('Roles')),
                    HtmlGrid::tpl(
                        '<div>' . ExtGrid::renderIcon('g-icon_size_16 g-icon_gridcolumn-user-roles', 'svg') . ' {.}</div>',
                        ['for' => 'roles']
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Update'),
                        ['route' => Gm::alias('@match', '/roles/view/{id}'), 'long' => true]
                    )
                ]
            ],
            ['fieldset',
                [
                    HtmlNav::legend($this->t('Modules')),
                    HtmlGrid::tpl(
                        '<div>' . ExtGrid::renderIcon('g-icon_size_16 g-icon_gridcolumn-modules', 'svg') . ' {.}</div>',
                        ['for' => 'modules']
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Update'),
                        ['route' => Gm::alias('@match', '/modules/view/{id}'), 'long' => true]
                    )
                ]
            ],
            ['fieldset',
                [
                    HtmlNav::legend($this->t('Extensions')),
                    HtmlGrid::tpl(
                        '<div>' . ExtGrid::renderIcon('g-icon_size_16 g-icon_extension_small', 'svg') . ' {.}</div>',
                        ['for' => 'extensions']
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Update'),
                        ['route' => Gm::alias('@match', '/extensions/view/{id}'), 'long' => true]
                    )
                ]
            ]
        ]);

        // если открыто окно настройки служб (конфигурация), закрываем его
        $this->getResponse()->meta->cmdComponent('g-setting-window', 'close');

        $tab
            ->addCss('/grid.css')
            ->addRequire('Gm.view.grid.column.Switch');
        return $tab;
    }
}

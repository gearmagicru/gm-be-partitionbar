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
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер формы панели раздела.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Partitionbar\Controller
 * @since 1.0
 */
class Form extends FormController
{
    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var \Gm\FontAwesome\FontAwesome $fa */
        $fa = Gm::$app->fontAwesome;
        $fa->loadMap('v5.8 pro');

        $faItems = $fa->getRenderItems('fab ');

        /** @var EditWindow $window */
        $window = parent::createWidget();

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->autoScroll = true;
        $window->form->bodyPadding = 10;
        $window->form->layout = 'anchor';
        $window->form->defaults = [
            'labelAlign' => 'right',
            'labelWidth' => 95
        ];
        $window->form->controller = 'gm-be-partitionbar-form';
        $window->form->loadJSONFile('/form', 'items', [
            '@comboStoreUrl' => [Gm::alias('@match', '/trigger/combo')],
            '@iconStoreData' => $faItems
        ]);

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 480;
        $window->height = 520;
        $window->layout = 'fit';
        $window->resizable = false;
        $window
            ->setNamespaceJS('Gm.be.partitionbar')
            ->addRequire('Gm.be.partitionbar.FormController')
            ->addCss('/form.css');
        return $window;
    }
}

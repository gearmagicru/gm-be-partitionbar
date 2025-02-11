/*!
 * Контроллер формы.
 * Модуль "Панели разделов".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.partitionbar.FormController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-be-partitionbar-form',

    iconFields: null,

    /**
     * Возвращает компоненты формы (флажки).
     * @return {Object}
     */
    getIconFields: function () {
        if (this.iconFields === null) {
            this.iconFields = this.getViewCmp(['ffont', 'fglyph', 'ficon', 'fcss', 'ffcss']);
        }
        return this.iconFields;
    },

    /**
     * Срабатывает при клике на флаг выбора значка.
     * @param {Ext.form.field.Radio} me
     * @param {Boolean} value Значение.
     * @param {Boolean} oldValue Старое значение.
     * @param {Object} eOpts
     */
    onCheckIcon: function (me, value) {
        if (me.checked) {
            let form = this.getIconFields();
            Object.values(form).forEach((field) => field.hide());
            form['f' + me.inputValue].show();
        }
    }
});

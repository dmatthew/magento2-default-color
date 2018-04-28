define([
    'jquery'
], function ($) {
    'use strict';

    var mixin = {
        _RenderControls: function () {
            this._super();
            this._EmulateSelected(this.getDefaultAttributes());
        },

        getDefaultAttributes: function () {
            if (this.options.jsonConfig.default_color_value) {
                return {color: this.options.jsonConfig.default_color_value};
            }
            return {};
        }
    };

    return function (magentoSwatchRenderer) {
        $.widget('mage.SwatchRenderer', magentoSwatchRenderer, mixin);
        return $.mage.SwatchRenderer;
    };
});

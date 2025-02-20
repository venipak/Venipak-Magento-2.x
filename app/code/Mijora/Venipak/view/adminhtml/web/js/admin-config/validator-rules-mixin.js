define([
 'jquery'
], function ($) {
    'use strict';
    return function (target) {
        $.validator.addMethod(
            'validate-venipak-price',
            function (value) {
                console.log($.isNumeric(value));
                if (!$.isNumeric(value)){
                    const regex = /^(([0-9]*[.])?\d+(:)([0-9]*[.])?\d+(:)([0-9]*[.])?\d+(\|))+$/gm;
                    const str = value + "|";
                    let m;
                    var matched = false;
                    while ((m = regex.exec(str)) !== null) {
                        if (m.index === regex.lastIndex) {
                            regex.lastIndex++;
                        }
                        m.forEach((match, groupIndex) => {
                            matched = true;
                        });
                    }
                    return matched;
                }
                return $.isNumeric(value);
            },
            $.mage.__('Please enter a number or string that matches the pattern n:n:n|n:n:n ...')
        );
        $.validator.addMethod(
            'validate-venipak-price-by-country',
            function (value) {
                console.log($.isNumeric(value));
                if (!$.isNumeric(value)) {
                    const regex = /^((\w{2}:\d+(\.\d+)?\|)*(\w{2}:\d+(\.\d+)?|\d+(\.\d+)?))$/;
                    return regex.test(value);
                }
                return $.isNumeric(value);
            },
            $.mage.__('Please enter a number or string that matches the pattern XX:n|XX:n|n ...')
        );
        return target;
    };
});
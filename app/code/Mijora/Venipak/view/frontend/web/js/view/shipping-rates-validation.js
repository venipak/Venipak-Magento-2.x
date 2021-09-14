/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Mijora_Venipak/js/model/shipping-rates-validator',
        'Mijora_Venipak/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        venipakShippingRatesValidator,
        venipakShippingRatesValidationRules
    ) {
        'use strict';
        
        defaultShippingRatesValidator.registerValidator('venipak', venipakShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('venipak', venipakShippingRatesValidationRules);
        return Component;
    }
);

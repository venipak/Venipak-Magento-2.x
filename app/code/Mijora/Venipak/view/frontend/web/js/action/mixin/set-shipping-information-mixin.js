define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'Mijora_Venipak/js/venipak-data'
], function($, wrapper, quote, globalMessageList, $t, $venipakData) {
    'use strict';

    return function(shippingInformationAction) {

        return wrapper.wrap(
            shippingInformationAction,
            function(originalAction) {
                let selectedShippingMethod = quote.shippingMethod();
                let shippingAddress = quote.shippingAddress();
                
                
                if (selectedShippingMethod.carrier_code !== 'venipak') {
                    return originalAction();
                }
                
                let terminal = $venipakData.getPickupPoint();
                let doorCode = $venipakData.getDoorCode();
                let warehouseNumber = $venipakData.getWarehouseNumber();
                let cabinetNumber = $venipakData.getCabinetNumber();
                let deliveryTime = $venipakData.getDeliveryTime();
                let callBeforeDelivery = $venipakData.getCallBeforeDelivery();
                
                if (selectedShippingMethod.method_code === 'PICKUP_POINT' &&
                    !terminal) {
                    globalMessageList.addErrorMessage(
                        {message: $t('Select Venipak pickup point!')});
                    jQuery(window).scrollTop(0);
                    return originalAction();
                }
                
                if (shippingAddress.extensionAttributes === undefined) {
                    shippingAddress.extensionAttributes = {};
                }

                shippingAddress.extensionAttributes.venipak_data = JSON.stringify({ 
                    'pickupPoint': terminal, 
                    'doorCode': doorCode, 
                    'warehouseNumber': warehouseNumber, 
                    'cabinetNumber': cabinetNumber,
                    'deliveryTime': deliveryTime,
                    'callBeforeDelivery': callBeforeDelivery
                });

                return originalAction();
            });
    };
});

define(
    [
        'Mijora_Venipak/js/view/checkout/shipping/model/resource-url-manager',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'mage/storage',
        'Magento_Checkout/js/model/shipping-service',
        'Mijora_Venipak/js/view/checkout/shipping/model/parcel-terminal-registry',
        'Magento_Checkout/js/model/error-processor'
    ],
    function (resourceUrlManager, quote, customer, storage, shippingService, pickupPointRegistry, errorProcessor) {
        'use strict';

        return {
            /**
             * Get nearest machine list for specified address
             * @param {Object} address
             */
            getPickupPointList: function (address, form,group = 0) {
                shippingService.isLoading(true);
                var cacheKey = address.getCacheKey(),
                    cache = pickupPointRegistry.get(cacheKey),
                    serviceUrl = resourceUrlManager.getUrlForPickupPointList(quote,group);

                if (cache) {
                    form.setPickupPointList(cache);
                    shippingService.isLoading(false);
                } else {
                    storage.get(
                        serviceUrl, false
                    ).done(
                        function (result) {
                            pickupPointRegistry.set(cacheKey, result);
                            form.setPickupPointList(result);
                        }
                    ).fail(
                        function (response) {
                            errorProcessor.process(response);
                        }
                    ).always(
                        function () {
                            shippingService.isLoading(false);
                        }
                    );
                }
            }
        };
    }
);
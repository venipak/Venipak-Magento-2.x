define(
        ['mage/translate',
            'Magento_Ui/js/model/messageList',
            'Magento_Checkout/js/model/quote',
            'Mijora_Venipak/js/venipak-data'],
        function ($t, messageList, quote, $venipakData) {
            'use strict';
            return {
                validate: function () {
                    var isValid = true; //Put your validation logic here



                    let selectedShippingMethod = quote.shippingMethod();

                    if (selectedShippingMethod.carrier_code === 'venipak') {
                        let terminal = $venipakData.getPickupPoint();
                        if (selectedShippingMethod.method_code === 'PICKUP_POINT' &&
                                !terminal) {
                            messageList.addErrorMessage({message: $t('Select Venipak pickup point!')});
                            isValid = false;
                        }
                    }

                    return isValid;
                }
            }
        }
);

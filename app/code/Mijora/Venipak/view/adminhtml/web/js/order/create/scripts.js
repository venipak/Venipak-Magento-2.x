define([
    'jquery',
    'Magento_Sales/order/create/scripts'
], function (jQuery) {
    'use strict';


    AdminOrder.prototype.setVenipakData  = function(pickup_point) {
              var data = {};
              data['order[shipping_method]'] = 'venipak_PICKUP_POINT';
              data['order[venipak_data]'] = '{"pickupPoint":"'+pickup_point+'"}';
              this.loadArea(['shipping_method', 'totals', 'billing_method'], true, data);
            };
});
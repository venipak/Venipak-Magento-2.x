define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Mijora_Venipak/js/view/checkout/shipping/parcel-terminal-service',
    'mage/translate',
    'Mijora_Venipak/js/venipak-data',
    'mage/url',
    'leaflet',
    'leafletmarkercluster',
    'Mijora_Venipak/js/terminal-mapping',
    'Mijora_Venipak/js/terminals-map-init',
    'Mijora_Venipak/js/front'
], function ($, ko, Component, quote, shippingService, pickupPointService, t, venipakData, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mijora_Venipak/checkout/shipping/form'
        },
        
        

        initialize: function (config) {
            this.pickupPoints = ko.observableArray();
            this.isLoading = false;
            this._super();

        },
        hideSelect: function () {
            //console.log('hide');
            var method = quote.shippingMethod();
            var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
            if ($('#checkout-shipping-method-load .venipak_courrier_data').length === 0 && $('#checkout-shipping-method-load .mjvp-pp-container').length === 0){
                this.reloadPickupPoints();
            } else {
                this.hideCourier(selectedMethod);
                this.hidePickup(selectedMethod);
            }
            if (selectedMethod == 'venipak_PICKUP_POINT' && !venipakData.getPickupPoint()) {
                $('#shipping-method-buttons-container .button.continue').prop('disabled', true);
            } else {
                $('#shipping-method-buttons-container .button.continue').prop('disabled', false);
            }
        },
        hideCourier: function (selectedMethod) {
            if ($('#s_method_venipak_COURIER').length > 0 && $('#checkout-shipping-method-load .venipak_courrier_data').length === 0) {
                setTimeout(function (that, selectedMethod) {
                    that.hideCourier(selectedMethod);
                }, 1000, this, selectedMethod);
            } else {
                if (selectedMethod === 'venipak_COURIER') {
                    $('#checkout-shipping-method-load .venipak_courrier_data').slideDown();
                } else {
                    $('#checkout-shipping-method-load .venipak_courrier_data').slideUp();
                }
            }

        },
        hidePickup: function (selectedMethod) {
            if ($('#s_method_venipak_PICKUP_POINT').length > 0 && $('#checkout-step-shipping_method .mjvp-pp-container').length === 0) {
                setTimeout(function (that, selectedMethod) {
                    that.hidePickup(selectedMethod);
                }, 1000, this, selectedMethod);
            } else {
                if (selectedMethod === 'venipak_PICKUP_POINT') {
                    $('#checkout-step-shipping_method .mjvp-pp-container').slideDown();
                } else {
                    $('#checkout-step-shipping_method .mjvp-pp-container').slideUp();
                }
            }

        },
        
        addLogo: function() {
            if ($('#checkout-shipping-method-load img.venipak-logo').length === 0){
                if ($('#label_carrier_PICKUP_POINT_venipak').length > 0){
                    $('#label_carrier_PICKUP_POINT_venipak').html('<img src = "' + require.toUrl('Mijora_Venipak/images/') + 'venipak-logo-v2-resized.png" class = "venipak-logo" alt = "Venipak"/>');
                }
                if ($('#label_carrier_COURIER_venipak').length > 0){
                    $('#label_carrier_COURIER_venipak').html('<img src = "' + require.toUrl('Mijora_Venipak/images/') + 'venipak-logo-v2-resized.png" class = "venipak-logo" alt = "Venipak"/>');
                }
            }
        },
        
        moveSelect: function () {
            //console.log('move');
            var move_after = false;
            var container = $('#onepage-checkout-shipping-method-additional-load .mjvp-pp-container');
            if (container.length > 0) {
                mjvp_pickup_container = container.clone();
            }
            if (mjvp_pickup_container !== null && $('#checkout-shipping-method-load .tmjs-selected-terminal').length === 0){
                container = mjvp_pickup_container;
                $('#pickup-select-location').remove();
                
                move_after = false;
                if ($('#s_method_venipak_PICKUP_POINT').length > 0) {
                    move_after = $('#s_method_venipak_PICKUP_POINT').parents('tr');
                } else if ($('#label_method_PICKUP_POINT_venipak').length > 0) {
                    move_after = $('#label_method_PICKUP_POINT_venipak').parents('tr');
                }
                
                if (move_after !== false){
                    if ($('#pickup-select-location').length == 0) {
                        $('<tr id = "pickup-select-location" ><td colspan = "4" style = "border-top: none; padding: 0px"></td></tr>').insertAfter(move_after);
                    }
                    if ($('#pickup-select-location #mjvp-pickup-select-modal .tmjs-container').length === 0){
                        container.appendTo($('#pickup-select-location td'));

                        mjvp_pickup_el = $('#pickup-select-location #mjvp-pickup-select-modal')[0];
                    }
                    $('#checkout-step-shipping_method .mjvp-pp-container').show();
                }
            }


            

            //move courier data
            move_after = false;
            var courier_container = $('#onepage-checkout-shipping-method-additional-load .venipak_courrier_data');
            if (courier_container.length > 0) {
                mjvp_courier_container = courier_container.clone();
            }
            if (mjvp_courier_container !== null){
                courier_container = mjvp_courier_container;
                if ($('#s_method_venipak_COURIER').length > 0) {
                    move_after = $('#s_method_venipak_COURIER').parents('tr');
                } else if ($('#label_method_COURIER_venipak').length > 0) {
                    move_after = $('#label_method_COURIER_venipak').parents('tr');
                }
                if (move_after !== false){
                    if ($('#venipak-courier-data').length == 0) {
                        $('<tr id = "venipak-courier-data" ><td colspan = "4" style = "border-top: none; padding: 0px"></td></tr>').insertAfter(move_after);
                    }
                    courier_container.appendTo($('#venipak-courier-data td'));
                }
            }
            
            $('#mjvp-selected-terminal').val(venipakData.getPickupPoint());
            $('#venipak-courier-door-code').val(venipakData.getDoorCode());
            $('#venipak-courier-cabinet-number').val(venipakData.getCabinetNumber());
            $('#venipak-courier-warehouse-number').val(venipakData.getWarehouseNumber());
            $('#venipak-courier-delivery-time').val(venipakData.getDeliveryTime());
            if (venipakData.getCallBeforeDelivery() === 1 ){
                $('#venipak-courier-call-before-delivery').prop("checked", 1);
            }
            
            $('#checkout-step-shipping_method').on('change', '#mjvp-selected-terminal', function () {
                venipakData.setPickupPoint($(this).val());
                $('#shipping-method-buttons-container .button.continue').prop('disabled', false);
            });
            $('#checkout-step-shipping_method').on('focusout', '#venipak-courier-door-code', function () {
                venipakData.setDoorCode($(this).val());
            });
            $('#checkout-step-shipping_method').on('focusout', '#venipak-courier-cabinet-number', function () {
                venipakData.setCabinetNumber($(this).val());
            });
            $('#checkout-step-shipping_method').on('focusout', '#venipak-courier-warehouse-number', function () {
                venipakData.setWarehouseNumber($(this).val());
            });
            $('#checkout-step-shipping_method').on('change', '#venipak-courier-delivery-time', function () {
                venipakData.setDeliveryTime($(this).val());
            });
            $('#checkout-step-shipping_method').on('change', '#venipak-courier-call-before-delivery', function () {
                if($(this).is(":checked")) {
                    venipakData.setCallBeforeDelivery(1);
                } else {
                    venipakData.setCallBeforeDelivery(0);
                }
                
            });
            this.addLogo();
        },

        initMap: function () {
            this.addLogo();
            if ($('#onepage-checkout-shipping-method-additional-load #tmjs-modal-template').length > 0 && 
                    $('#onepage-checkout-shipping-method-additional-load #tmjs-modal-template').length>0 && 
                    $('#onepage-checkout-shipping-method-additional-load .mjvp-pp-container').length > 0 
                    && 
                    ($('#label_method_PICKUP_POINT_venipak').length || $('#label_method_COURIER_venipak').length)
                    ) {

                this.reloadPickupPoints();
            } else {
                setTimeout(function (that) {
                    that.initMap();
                }, 2000, this);
            }
        },

        initObservable: function () {
            this._super();

            this.initAfterLoad = ko.computed(function () {
                this.initMap();
            }, this);

            quote.shippingMethod.subscribe(function (method) {
                this.hideSelect();
                
                /*
                 var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                 if (selectedMethod == 'venipak_PARCEL_TERMINAL') {
                 this.reloadPickupPoints();
                 }*/
            }, this);


            return this;
        },

        setPickupPointList: function (list) {
            //console.log('set');
            if (this.isLoading){
                return false;
            }
            
            this.isLoading = true;
            mjvp_postal_code = quote.shippingAddress().postcode;
            mjvp_city = quote.shippingAddress().city;
            mjvp_country_code = quote.shippingAddress().countryId;
            mjvp_terminals = list;
            if ((!mjvp_modal_container || mjvp_modal_container === null) && $('#onepage-checkout-shipping-method-additional-load #mjvp-pickup-select-modal').length > 0){
                mjvp_modal_container = $('#onepage-checkout-shipping-method-additional-load #mjvp-pickup-select-modal');
            }
            if (!mjvp_map_template || mjvp_map_template === ''){
                mjvp_map_template = $('#onepage-checkout-shipping-method-additional-load #tmjs-modal-template').html();
            }
            if (!mjvp_map_template || !mjvp_modal_container){
                this.isLoading = false;
                setTimeout(function (that, list) {
                    that.setPickupPointList(list);
                }, 2000, this, list);
                return;
            }
            if (mjvp_map_template){
                $('#onepage-checkout-shipping-method-additional-load #tmjs-modal-template').remove();
            }
            mjvp_imgs_url = require.toUrl('Mijora_Venipak/images/');
            mjvp_front_controller_url = urlBuilder.build('venipak/frontend/front');
            mjvp_quote_id = quote.getQuoteId();
            mjvp_weight = 0.0;
            $.each(quote.getItems(), function(index,el){
                mjvp_weight += parseFloat(el.row_weight);
            });
            
            mjvp_terminal_select_translates = {
                'modal_header': t('Terminal map'),
                'terminal_list_header': t('Terminal list'),
                'seach_header': t('Search around'),
                'search_btn': t('Find'),
                'modal_open_btn': t('Select terminal'),
                'geolocation_btn': t('Use my location'),
                'your_position': t('Distance calculated from this point'),
                'nothing_found': t('Nothing found'),
                'no_cities_found': t('There were no cities found for your search term'),
                'geolocation_not_supported': t('Geolocation is not supported'),
                'select_pickup_point': t('Select a pickup point'),
                'select_btn': t('Select')
            };
            
            
            
            this.moveSelect();
            if ($('#pickup-select-location #mjvp-pickup-select-modal .tmjs-container').length === 0){
                venipak_custom_modal();
            }
            $('.tmjs-search-input, .mjvp-pickup-filter, .venipak_courrier_data input, .venipak_courrier_data select').removeAttr('disabled');
            this.hideSelect();
            this.isLoading = false;
        },

        reloadPickupPoints: function () {
            pickupPointService.getPickupPointList(quote.shippingAddress(), this, 1);
        }

    });
});
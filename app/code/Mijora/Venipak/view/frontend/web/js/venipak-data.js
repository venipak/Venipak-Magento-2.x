define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, storage) {
    'use strict';

    let getEmptyObject = function () {
        return {
            'selectedPickupPoint': null,
            'doorCode': null,
            'warehouseNumber': null,
            'cabinetNumber': null,
            'deliveryTime': null,
            'callBeforeDelivery': 0
        };
    };

    let cacheKey = 'venipak-data',
            saveData = function (data) {
                storage.set(cacheKey, data);
            },
            getData = function () {
                let data = storage.get(cacheKey)();

                if ($.isEmptyObject(data)) {
                    data = getEmptyObject();
                    saveData(data);
                }

                return data;
            };

    return {

        getPickupPoint: function () {
            return getData().selectedPickupPoint;
        },

        setPickupPoint: function (data) {
            let obj = getData();

            obj.selectedPickupPoint = data;

            saveData(obj);
        },

        getDoorCode: function () {
            return getData().doorCode;
        },

        setDoorCode: function (data) {
            let obj = getData();

            obj.doorCode = data;

            saveData(obj);
        },

        getWarehouseNumber: function () {
            return getData().warehouseNumber;
        },

        setWarehouseNumber: function (data) {
            let obj = getData();

            obj.warehouseNumber = data;

            saveData(obj);
        },

        getCabinetNumber: function () {
            return getData().cabinetNumber;
        },

        setCabinetNumber: function (data) {
            let obj = getData();

            obj.cabinetNumber = data;

            saveData(obj);
        },
        
        getDeliveryTime: function () {
            return getData().deliveryTime;
        },

        setDeliveryTime: function (data) {
            let obj = getData();

            obj.deliveryTime = data;

            saveData(obj);
        },
        
        getCallBeforeDelivery: function () {
            return getData().callBeforeDelivery;
        },

        setCallBeforeDelivery: function (data) {
            let obj = getData();

            obj.callBeforeDelivery = data;

            saveData(obj);
        }

    };
});

define([
    'jquery'
], function ($) {
    'use strict';

    function getForm(url, order_id, terminal_id) {
        return $('<form>', {
            'action': url,
            'method': 'GET'
        }).append($('<input>', {
            'name': 'form_key',
            'value': window.FORM_KEY,
            'type': 'hidden'
        })).append($('<input>', {
            'name': 'order_id',
            'value': order_id,
            'type': 'hidden'
        })).append($('<input>', {
            'name': 'terminal_id',
            'value': terminal_id,
            'type': 'hidden'
        }));
    }

    $('#change-terminal').on('click', function (e) {
        var select = $('#venipak_pickup_point');
        var url = select.attr('data-url');
        var order_id = select.attr('data-order');
        var terminal_id = select.val();
        getForm(url, order_id, terminal_id).appendTo('body').submit();
    });

    $('#venipakorder_carrier').on('change', function () {
        if ($('#venipakorder_carrier').val() === "pickup_point") {
            $('.venipakorder_pickup_point_container').fadeIn();
            $('.venipak-carrier-details').fadeOut();
        } else {
            $('.venipakorder_pickup_point_container').fadeOut();
            $('.venipak-carrier-details').fadeIn();
        }
    });
    $('#venipakorder_is_cod').on('change', function () {
        if ($('#venipakorder_is_cod').val() === "0") {
            $('#venipakorder_cod_amount').prop('disabled');
        } else {
            $('#venipakorder_cod_amount').removeProp('disabled');
        }
    });
    
    $('#venipakorder_carrier').trigger('change');
    $('#venipakorder_is_cod').trigger('change');
});
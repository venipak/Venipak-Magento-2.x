jQuery( document ).ready(function($) {
  if (typeof(mjvp_country_code) != 'undefined' && mjvp_country_code != null) {
    //mjvp_registerSelection('mjvp-selected-terminal');
  }
  if($('#mjvp-courier-extra-fields .alert-danger').length != 0 || $('.mjvp-pp-container .alert-danger').length != 0)
    $('#notifications .alert-danger').hide();
    $("body").on('click', '.mjvp-pickup-filter', e => {
        console.log(".mjvp-pickup-filter clicked");
        venipak_custom_modal.tmjs.dom.addOverlay();
        
        e.preventDefault();
        let clickedLink = $(e.target);
        if(clickedLink[0].nodeName == 'SPAN')
            clickedLink = $(clickedLink[0].parentElement);
        $('.mjvp-pickup-filter').removeClass('active');
        $.ajax({
            type: "POST",
            url: mjvp_front_controller_url + "?ajax=1&submitFilterTerminals=1&action=filter&quote_id="+mjvp_quote_id,
            dataType: "json",
            data: {
                'filter_key' : clickedLink.data('filter'),
            },
            success: function (res) {
                $('.tmjs-search-input').val('');
                $('#terminal-search-radius').val('');
                venipak_custom_modal.tmjs.dom.removeOverlay();
                if(typeof res.mjvp_terminals != "undefined")
                {
                    clickedLink.addClass('active');
                    var terminals = [];
                    mjvp_terminals = res.mjvp_terminals;
                    mjvp_terminals.forEach((terminal) => {
                        if(terminal.lat != 0 && terminal.lng != 0 && terminal.terminal)
                        {
                            terminal['coords'] = {
                                lat: terminal.lat,
                                lng: terminal.lng
                            };
                            // Pickup type
                            if(terminal.type == 1)
                                terminal['identifier'] = 'venipak-pickup';
                            // Locker type
                            else if(terminal.type == 3)
                                terminal['identifier'] = 'venipak-locker';
                            terminals.push(terminal);
                        }
                    });
                    venipak_custom_modal.tmjs.terminals_cache = terminals;
                    venipak_custom_modal.tmjs.setTerminals(terminals);
                    venipak_custom_modal.tmjs.dom.renderTerminalList(venipak_custom_modal.tmjs.map.locations);
                }
            },
        });
    });
});

jQuery(document).on("change", "input[name^='delivery_option[']", function() {
    if (document.getElementById('mjvp-selected-terminal')) {
        //mjvp_registerSelection('mjvp-selected-terminal');
    }
});

jQuery(document).on("change", "#mjvp-terminal-select-field", function() {
    document.getElementById("mjvp-selected-terminal").value = this.value;
    //mjvp_registerSelection('mjvp-selected-terminal');
});

function mjvp_registerSelection(selected_field_id) {
    jQuery('#'+selected_field_id).trigger('change');
    /*
  var ajaxData = {};
  //ajaxData.carrier_id = jQuery("input[name^='delivery_option[']:checked").val().split(',')[0];
  ajaxData.selected_terminal = document.getElementById(selected_field_id).value;
  ajaxData.country_code = document.getElementById("mjvp-pickup-country").value;

    var terminal = null;
    mjvp_terminals.forEach((val, i) => {
        if(parseInt(val.id) == parseInt(ajaxData.selected_terminal)) {
            terminal = val;
        }
    });
    ajaxData.terminal = terminal;

  jQuery.ajax(mjvp_front_controller_url,
    {
      data: ajaxData,
      type: "POST",
      dataType: "json",
    })
    .always(function (jqXHR, status) {
      if (typeof jqXHR === 'object' && jqXHR !== null && 'msg' in jqXHR) {
        console.log(jqXHR.msg);
      } else {
        console.log(jqXHR);
      }
    });
     */
}
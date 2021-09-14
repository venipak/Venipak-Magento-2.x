<?php

namespace Mijora\Venipak\Block\Adminhtml\Order;

class Order extends \Magento\Backend\Block\Widget\Grid\Container {

    protected function _construct() {
        $this->_controller = 'adminhtml_Order';
        $this->_blockGroup = 'Mijora_Venipak';
        $this->_headerText = __('Orders');
        parent::_construct();

        $this->buttonList->remove('add');
    }

}

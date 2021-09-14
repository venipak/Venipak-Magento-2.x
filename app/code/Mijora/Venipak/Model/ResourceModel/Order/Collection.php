<?php

namespace Mijora\Venipak\Model\ResourceModel\Order;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'venipak_order_collection';
    protected $_eventObject = 'venipak_order_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Mijora\Venipak\Model\Order', 'Mijora\Venipak\Model\ResourceModel\Order');
    }

}

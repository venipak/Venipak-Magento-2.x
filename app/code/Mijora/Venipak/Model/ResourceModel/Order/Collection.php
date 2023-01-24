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

    protected function _renderFiltersBefore() {
        $wherePart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
        foreach ($wherePart as $key => $cond) {
            $wherePart[$key] = str_replace('`fullname`', "CONCAT_WS(' ', customer.firstname, customer.lastname)", $cond);
        }
        $this->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        parent::_renderFiltersBefore();
    }

}

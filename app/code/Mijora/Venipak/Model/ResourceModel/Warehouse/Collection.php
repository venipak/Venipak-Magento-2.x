<?php

namespace Mijora\Venipak\Model\ResourceModel\Warehouse;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'warehouse_id';
    protected $_eventPrefix = 'venipak_warehouse_collection';
    protected $_eventObject = 'warehouse_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Mijora\Venipak\Model\Warehouse', 'Mijora\Venipak\Model\ResourceModel\Warehouse');
    }

    protected function _renderFiltersBefore() {
        if (isset($this->_orders['default'])){
            $this->_orders['`default`'] = $this->_orders['default'];
            unset($this->_orders['default']);
        }
        parent::_renderFiltersBefore();
    }

}

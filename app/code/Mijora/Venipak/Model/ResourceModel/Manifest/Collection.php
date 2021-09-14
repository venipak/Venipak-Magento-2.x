<?php

namespace Mijora\Venipak\Model\ResourceModel\Manifest;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'manifest_id';
    protected $_eventPrefix = 'venipak_manifest_collection';
    protected $_eventObject = 'venipak_manifest_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Mijora\Venipak\Model\Manifest', 'Mijora\Venipak\Model\ResourceModel\Manifest');
    }

}

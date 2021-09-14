<?php

namespace Mijora\Venipak\Model;

class Warehouse extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'venipak_warehouse';

    protected $_cacheTag = 'venipak_warehouse';
    protected $_eventPrefix = 'venipak_warehouse';

    protected function _construct() {
        $this->_init('Mijora\Venipak\Model\ResourceModel\Warehouse');
    }

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

}

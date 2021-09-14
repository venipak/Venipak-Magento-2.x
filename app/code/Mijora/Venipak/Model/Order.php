<?php

namespace Mijora\Venipak\Model;

class Order extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'venipak_order';

    protected $_cacheTag = 'venipak_order';
    protected $_eventPrefix = 'venipak_order';

    protected function _construct() {
        $this->_init('Mijora\Venipak\Model\ResourceModel\Order');
    }

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

}

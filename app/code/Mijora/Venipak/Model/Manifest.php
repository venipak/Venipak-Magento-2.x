<?php

namespace Mijora\Venipak\Model;

class Manifest extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'venipak_manifest';

    protected $_cacheTag = 'venipak_manifest';
    protected $_eventPrefix = 'venipak_manifest';

    protected function _construct() {
        $this->_init('Mijora\Venipak\Model\ResourceModel\Manifest');
    }

    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getManifestId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

}

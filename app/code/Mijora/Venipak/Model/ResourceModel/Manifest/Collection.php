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

    protected function _renderFiltersBefore() {
        $wherePart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
        foreach ($wherePart as $key => $cond) {
            $wherePart[$key] = str_replace('`created_at`', "`main_table`.`created_at`", $cond);
        }
        $this->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        parent::_renderFiltersBefore();
    }

}

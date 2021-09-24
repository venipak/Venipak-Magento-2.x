<?php

namespace Mijora\Venipak\Model\ResourceModel;

class Warehouse extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    public function __construct(
            \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct() {
        $this->_init('venipak_warehouse', 'warehouse_id');
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
        $default = $object->getDefault();
        if ($default) {
            $connection = $this->getConnection();
            $tableName = $object->getResource()->getTable('venipak_warehouse');
            $sql = "Update " . $tableName . " set `default` = '0' where `warehouse_id` != " . $object->getWarehouseId();
            $connection->query($sql);
        }
    }

}

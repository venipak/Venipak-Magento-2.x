<?php

namespace Mijora\Venipak\Block\Adminhtml;

/**
 * Class ModalBox
 *
 * @package StackExchange\MagentoTest\Block\Adminhtml\Order
 */
class CallCourier extends \Magento\Backend\Block\Template {

    protected $_template = 'order/callCourier.phtml';
    protected $warehouseFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array $data
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            array $data = [],
            \Mijora\Venipak\Model\WarehouseFactory $warehouseFactory
    ) {

        $this->warehouseFactory = $warehouseFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getInfo() {
        //Your block cod
        return '';
    }

    public function getFormUrl() {
        return $this->getUrl('mijora_venipak/manifest/callcourier');
    }

    public function getWarehouses() {
        $warehouse = $this->warehouseFactory->create();
        $collection = $warehouse->getCollection();
        return $collection->getData();
    }
    
    public function getDefaultWarehouse() {
        $warehouse = $this->warehouseFactory->create();
        $warehouse->load(1, 'default');
        return $warehouse;
    }

}

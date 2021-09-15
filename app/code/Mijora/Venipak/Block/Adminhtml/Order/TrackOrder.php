<?php

namespace Mijora\Venipak\Block\Adminhtml\Order;

/**
 * Class ModalBox
 *
 * @package StackExchange\MagentoTest\Block\Adminhtml\Order
 */
class TrackOrder extends \Magento\Backend\Block\Template {

    protected $_template = 'order/trackOrder.phtml';
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
        return $this->getUrl('mijora_venipak/order/trackorder');
    }


}

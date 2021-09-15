<?php

namespace Mijora\Venipak\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Context;
use Mijora\Venipak\Model\OrderFactory;

class ManifestOrders extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    private $_storeManager;
    private $imageHelper;
    private $orderFactory;

    public function __construct(
            Context $context,
            OrderFactory $orderFactory,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderFactory = $orderFactory;
    }

    /**
     * Renders grid column
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $orders = $this->orderFactory->create()->getCollection()->addFieldToSelect('*');
        $orders->addFieldToFilter('manifest_id', array('eq' => $row->getManifestId()));
        
        $orders_ids = [];
        
        foreach ($orders as $order){
            $orders_ids[] = $order->getId();
        }
        
        return implode(',', $orders_ids);
    }

}

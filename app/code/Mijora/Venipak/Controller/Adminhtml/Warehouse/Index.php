<?php

namespace Mijora\Venipak\Controller\Adminhtml\Warehouse;

class Index extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $warehouseFactory;
    protected $coreRegistry;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\WarehouseFactory $warehouseFactory,
            \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->warehouseFactory = $warehouseFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Venipak warehouses')));

        return $resultPage;
    }

}

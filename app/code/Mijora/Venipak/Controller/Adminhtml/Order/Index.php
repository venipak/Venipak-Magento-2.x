<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action {

    protected $resultPageFactory;

    public function __construct(
            Context $context,
            PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute() {
        $this->_view->loadLayout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Venipak orders'));
        $resultPage->setActiveMenu('Mijora_Venipak::orders');
        $resultPage->addBreadcrumb(__('Venipak'), __('Orders'));
        $this->_addContent($this->_view->getLayout()->createBlock('Mijora\Venipak\Block\Adminhtml\Order\Grid'));
        $this->_view->renderLayout();
    }

    protected function _isAllowed() {
        return true;
    }

}

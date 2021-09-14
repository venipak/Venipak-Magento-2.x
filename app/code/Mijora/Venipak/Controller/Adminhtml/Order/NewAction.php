<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

use Mijora\Venipak\Controller\Adminhtml\Order\Index;

class NewAction extends Index {

    public function execute() {
         $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/edit');
    }

}

<?php

namespace Mijora\Venipak\Controller\Adminhtml\Warehouse;

use Mijora\Venipak\Controller\Adminhtml\Warehouse\Index;

class NewAction extends Index {

    public function execute() {
         $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/edit');
    }

}

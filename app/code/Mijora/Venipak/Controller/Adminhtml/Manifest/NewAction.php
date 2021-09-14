<?php

namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

use Mijora\Venipak\Controller\Adminhtml\Manifest\Index;

class NewAction extends Index {

    public function execute() {
         $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/edit');
    }

}

<?php

namespace Mijora\Venipak\Controller\Adminhtml\Warehouse;

use Mijora\Venipak\Controller\Adminhtml\Warehouse\Index;
 
class Edit extends Index
{
    /**
     * @return void
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');
 
        $model = $this->warehouseFactory->create();
 
        if ($postId) {
            $model->load($postId);
            if (!$model->getWarehouseId()) {
                $this->messageManager->addError(__('This warehouse no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
 
        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('venipak_warehouses', $model);
 
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Venipak warehouses')));
 
        return $resultPage;
    }
}
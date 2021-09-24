<?php
namespace Mijora\Venipak\Controller\Adminhtml\Warehouse;

use Mijora\Venipak\Controller\Adminhtml\Warehouse\Index;

class Save extends Index {

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $model = $this->warehouseFactory->create();
            
            $formData = $this->getRequest()->getParam('warehouse');

            if (isset($formData['warehouse_id'])) {
                $model->load($formData['warehouse_id']);
            }
            
            $model->setData($formData);

            try {
                // Save news
                $model->save();

                // Display success message
                $this->messageManager->addSuccess(__('Warehouse has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getWarehouseId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $model->getWarehouseId()]);
        }
    }

}

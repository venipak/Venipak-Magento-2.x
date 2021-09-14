<?php
namespace Mijora\Venipak\Controller\Adminhtml\Order;

use Mijora\Venipak\Controller\Adminhtml\Order\Index;

class Save extends Index {

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $model = $this->orderFactory->create();
            
            $formData = $this->getRequest()->getParam('Order');

            if (isset($formData['Order_id'])) {
                $model->load($formData['Order_id']);
            }
            
            $model->setData($formData);

            try {
                // Save news
                $model->save();

                // Display success message
                $this->messageManager->addSuccess(__('Order has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getOrderId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $postsId]);
        }
    }

}

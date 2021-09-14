<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

use Mijora\Venipak\Controller\Adminhtml\Order\Index;
 
class Edit extends Index
{
    /**
     * @return void
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');
 
        $model = $this->orderFactory->create();
 
        if ($postId) {
            $model->load($postId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Order no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
 
        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('venipak_orders', $model);
 
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Venipak orders')));
 
        return $resultPage;
    }
}
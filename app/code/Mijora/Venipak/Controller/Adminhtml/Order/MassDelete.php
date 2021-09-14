<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

use Mijora\Venipak\Controller\Adminhtml\Order\Index;

class MassDelete extends Index {

    public function execute() {
        $ids = $this->getRequest()->getParam('ids');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $model = $this->orderFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The Order has been deleted.'));
            }
        }
        $this->_redirect('*/*/');
    }

}

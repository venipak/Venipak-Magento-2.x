<?php

namespace Mijora\Venipak\Controller\Adminhtml\Warehouse;

use Mijora\Venipak\Controller\Adminhtml\Warehouse\Index;

class MassDelete extends Index {

    public function execute() {
        $ids = $this->getRequest()->getParam('ids');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $model = $this->warehouseFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The warehouse has been deleted.'));
            }
        }
        $this->_redirect('*/*/');
    }

}

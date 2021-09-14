<?php

namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

use Mijora\Venipak\Controller\Adminhtml\Manifest\Index;

class MassDelete extends Index {

    public function execute() {
        $ids = $this->getRequest()->getParam('ids');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $model = $this->ManifestFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The Manifest has been deleted.'));
            }
        }
        $this->_redirect('*/*/');
    }

}

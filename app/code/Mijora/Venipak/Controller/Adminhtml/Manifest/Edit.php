<?php

namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

use Mijora\Venipak\Controller\Adminhtml\Manifest\Index;
 
class Edit extends Index
{
    /**
     * @return void
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');
 
        $model = $this->ManifestFactory->create();
 
        if ($postId) {
            $model->load($postId);
            if (!$model->getManifestId()) {
                $this->messageManager->addError(__('This Manifest no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
 
        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('venipak_Manifests', $model);
 
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Venipak Manifests')));
 
        return $resultPage;
    }
}
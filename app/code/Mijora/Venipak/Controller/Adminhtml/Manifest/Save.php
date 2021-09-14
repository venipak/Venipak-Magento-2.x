<?php
namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

use Mijora\Venipak\Controller\Adminhtml\Manifest\Index;

class Save extends Index {

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $model = $this->ManifestFactory->create();
            
            $formData = $this->getRequest()->getParam('Manifest');

            if (isset($formData['Manifest_id'])) {
                $model->load($formData['Manifest_id']);
            }
            
            $model->setData($formData);

            try {
                // Save news
                $model->save();

                // Display success message
                $this->messageManager->addSuccess(__('Manifest has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getManifestId(), '_current' => true]);
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

<?php

namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

class PrintManifest extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $venipakManifestFactory;
    protected $coreRegistry;
    protected $json;
    protected $carrier;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\ManifestFactory $venipakManifestFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Controller\Result\JsonFactory $json,
            \Mijora\Venipak\Model\Carrier $carrier
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakManifestFactory = $venipakManifestFactory;
        $this->coreRegistry = $coreRegistry;
        $this->json = $json;
        $this->carrier = $carrier;
    }

    public function execute() {

        $id = $this->getRequest()->getParam('id');
        
        $model = $this->venipakManifestFactory->create();
        $model->load($id);
        $number = $model->getManifestNumber();
        if (!$number){
            echo 'Manifest has no number';
            exit;
        }
        if (!$model->getIsClosed()){
            $model->setIsClosed(1);
            $model->save();
        }
        
        header("Content-Type: application/pdf; name=\" Label.pdf");
        header("Content-Transfer-Encoding: binary");
        // disable caching on client and proxies, if the download content vary
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo $this->carrier->printManifest($number);
    }

}

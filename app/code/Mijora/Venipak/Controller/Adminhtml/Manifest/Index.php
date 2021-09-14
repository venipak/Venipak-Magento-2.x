<?php

namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

class Index extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $ManifestFactory;
    protected $coreRegistry;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\ManifestFactory $ManifestFactory,
            \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->ManifestFactory = $ManifestFactory;
        $this->coreRegistry = $coreRegistry;
    }
    
    public function execute() {
        $this->_view->loadLayout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Venipak manifests'));
        $resultPage->setActiveMenu('Mijora_Venipak::manifest');
        $resultPage->addBreadcrumb(__('Venipak'), __('Manifests'));
        $this->_addContent($this->_view->getLayout()->createBlock('Mijora\Venipak\Block\Adminhtml\Manifest\Manifest'));
        
        $this->_view->renderLayout();
    }

}

<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

class Label extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $venipakOrderFactory;
    protected $coreRegistry;
    protected $json;
    protected $carrier;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Controller\Result\JsonFactory $json,
            \Mijora\Venipak\Model\Carrier $carrier
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->json = $json;
        $this->carrier = $carrier;
    }

    public function execute() {
        $labels = [];
        $id = $this->getRequest()->getParam('id');
        if (is_array($id)) {
            foreach ($id as $order_id){
                $order = $this->venipakOrderFactory->create();
                $order->load($order_id);
                $_labels = @json_decode($order->getLabelNumber());
                if (is_array($_labels)) {
                    $labels = array_merge($_labels, $labels);
                }
            }
        } else {
            $order = $this->venipakOrderFactory->create();
            $order->load($id);
            $labels = @json_decode($order->getLabelNumber());
            if (!is_array($labels)) {

                echo 'Order has no labels';
                exit;
            }
        }
        header("Content-Type: application/pdf; name=\" Label.pdf");
        header("Content-Transfer-Encoding: binary");
        // disable caching on client and proxies, if the download content vary
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo $this->carrier->printLabels($labels);
    }

}

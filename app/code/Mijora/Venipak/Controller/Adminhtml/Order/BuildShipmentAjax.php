<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

class BuildShipmentAjax extends \Magento\Backend\App\Action {

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

        $venipakOrderId = $this->getRequest()->getParam('venipak_order_id');
        $resultJson = $this->json->create();

        if (!$venipakOrderId) {
            return $resultJson->setData([
                        'message' => 'Venipak order id not found.',
                        'error' => true
            ]);
        }

        $model = $this->venipakOrderFactory->create();
        $model->load($venipakOrderId, 'id');
        
        try {
            /*
            if ($model->getLabelNumber()){
                throw new \Exception('Label has been genarated already');
            }*/
            
            if ($this->carrier->doShipment([$model->getOrderId()])) {
                return $resultJson->setData([
                            'message' => 'Venipak shipment created',
                            'error' => false
                ]);
            }
        } catch (\Exception $e) {
            return $resultJson->setData([
                        'message' => $e->getMessage(),
                        'error' => true
            ]);
        }
        return $resultJson->setData([
                    'message' => 'Failed to create shipment',
                    'error' => true
        ]);
    }

}

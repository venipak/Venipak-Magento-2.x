<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

class VenipakOrderSaveAjax extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $venipakOrderFactory;
    protected $orderFactory;
    protected $coreRegistry;
    protected $json;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Magento\Sales\Model\OrderFactory $orderFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Controller\Result\JsonFactory $json
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->orderFactory = $orderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->json = $json;
    }

    public function execute() {

        $venipakOrderId = $this->getRequest()->getParam('venipak_order_id');
        $orderId = $this->getRequest()->getParam('order_id');
        $resultJson = $this->json->create();
        
        if (!$venipakOrderId){
            return $resultJson->setData([
                    'message' => 'Venipak order id not found.',
                    'error' => true
            ]);
        }
        
        $model = $this->venipakOrderFactory->create();
        $model->load($venipakOrderId, 'id');
        
        $order = $this->orderFactory->create();
        $order->load($orderId);
        
        $formData = $this->getRequest()->getParam('venipakorder');

        if ( empty($formData['warehouse']) ) {
            return $resultJson->setData([
                'message' => 'Warehouse not selected',
                'error' => false
            ]);
        }
        
        $modelData = [
            'number_of_packages' => $formData['number_of_packages'],
            'weight' => $formData['weight'],
            'is_cod' => $formData['is_cod'],
            'cod_amount' => $formData['cod_amount'],
            'delivery_time' => $formData['delivery_time'],
            'door_code' => $formData['door_code'],
            'cabinet_number' => $formData['cabinet_number'],
            'warehouse_number' => $formData['warehouse_number'],
            'warehouse_id' => $formData['warehouse'],
            'call_before_delivery' => isset($formData['call_before_delivery'])?1:0,
            'return_signed_document' => isset($formData['signed_document'])?1:0
        ];
        
        if ($formData['carrier']){
            $order->setShippingMethod('venipak_' . strtoupper($formData['carrier']));
            $order->save();
        }
        
        $shippingAddress = $order->getShippingAddress();
        $data =  @json_decode($shippingAddress->getVenipakData());
        if (!is_object($data)){
            $data = new \stdClass();
        }
        $data->pickupPoint = $formData['pickup_point'];
        $shippingAddress->setVenipakData(json_encode($data));
        $shippingAddress->save();
        
        $model->addData($modelData);
        
        try {
            $model->save();

            return $resultJson->setData([
                'message' => 'Data saved',
                'error' => false
            ]);
                
        } catch (\Exception $e) {
            return $resultJson->setData([
                'message' => $e->getMessage(),
                'error' => true
            ]);
        } 
    }
}

<?php

namespace Mijora\Venipak\Controller\Adminhtml\Manifest;

class CallCourier extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $venipakManifestFactory;
    protected $orderFactory;
    protected $coreRegistry;
    protected $messageManager;
    protected $resultFactory;
    protected $venipakWarehouseFactory;
    protected $venipakOrderFactory;
    protected $carrier;
    protected $json;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\ManifestFactory $venipakManifestFactory,
            \Mijora\Venipak\Model\WarehouseFactory $venipakWarehouseFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Magento\Sales\Model\OrderFactory $orderFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\Controller\ResultFactory $resultFactory,
            \Mijora\Venipak\Model\Carrier $carrier,
            \Magento\Framework\Controller\Result\JsonFactory $json
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakManifestFactory = $venipakManifestFactory;
        $this->orderFactory = $orderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->venipakWarehouseFactory = $venipakWarehouseFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->carrier = $carrier;
        $this->json = $json;
    }

    public function execute() {
        $response = [];
        try {
            $formData = $this->getRequest()->getParam('venipakmanifest');
            $resultJson = $this->json->create();


            if (!isset($formData['id']) || !$formData['id']) {
                throw new \Exception(__('Manifest not found'));
            }

            $model = $this->venipakManifestFactory->create();
            $model->load($formData['id'], 'manifest_id');

            if (!$formData['arrival_date'] || !$formData['arrival_from'] || !$formData['arrival_to'] || !$formData['warehouse'] || !$formData['weight']) {
                throw new \Exception(__('Arrival date, weight and warehouse is required'));
            }
            $date = strtotime($formData['arrival_date']);
            $from = strtotime($formData['arrival_from']);
            $to = strtotime($formData['arrival_to']);
            $diff = ($to - $from)/3600;
            if ($diff <= 2){
                throw new \Exception(__('There is a minimum 2 hours gap between from and to dates required'));
            }

            $modelData = [
                'arrival_date_from' => date('Y-m-d', $date) . ' ' . date('H:i:s', $from),
                'arrival_date_to' => date('Y-m-d', $date) . ' ' . date('H:i:s', $to),
                'warehouse_id' => $formData['warehouse'],
                'comment' => $formData['comment'],
                'weight' => $formData['weight'],
            ];

            $model->addData($modelData);
            $this->makeCall($model, $formData);

            $model->save();
            
            $response['success'] = __('Courier called');
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }

        return $resultJson->setData($response);
    }

    private function makeCall($manifest, $formData) {
        
        $warehouse = $this->venipakWarehouseFactory->create();
        $warehouse->load($formData['warehouse']);
        
        $sender = [];
        $invitation_data = [
            'desc_type' => 3
        ];
        
        $sender['name'] = $warehouse->getName();
        $sender['code'] = $warehouse->getCompanyCode();
        $sender['country_code'] = $warehouse->getCountry();
        $sender['city'] = $warehouse->getCity();
        $sender['address'] = $warehouse->getAddress();
        $sender['postcode'] = $warehouse->getPostcode();
        $sender['contact_person'] = $warehouse->getContactName();
        $sender['contact_phone'] = $warehouse->getPhone();
        $sender['contact_email'] = $this->carrier->getConfigData('shop_email');
        $invitation_data['sender'] = $sender;

        // Get manifest weight
        $shipment_weight = $formData['weight'];
        //if weight not defined, calculate
        if(!$shipment_weight){
            $orders = $this->venipakOrderFactory->create()->getCollection() ->addFieldToSelect('*');
            $orders->addFieldToFilter('manifest_id', array('eq' => $manifest->getId()));
            $shipment_weight = 0;
            foreach ($orders as $order){
                $shipment_weight += $order->getWeight();
            }
            
        }
        $shipment_weight = round((float) $shipment_weight, 3);
        $invitation_data['weight'] = $shipment_weight;
        $manifest->setWeight($shipment_weight);

        // Calculate manifest volume
        $manifest_orders = [];

        $manifest_volume = 0;
        $product_volume = 0;
        /*
          foreach ($manifest_orders as $order) {
          $id_order = $order['id_order'];
          $order = new Order($id_order);
          $order_products = $order->getProducts();

          foreach ($order_products as $key => $product) {
          // Calculate volume in m3
          if (Configuration::get('PS_DIMENSION_UNIT') == 'm')
          $product_volume = $product['width'] * $product['height'] * $product['depth'];
          elseif (Configuration::get('PS_DIMENSION_UNIT') == 'cm')
          $product_volume = ($product['width'] * $product['height'] * $product['depth']) / 1000000;
          $manifest_volume += (float) $product_volume;
          }
          } */
        $invitation_data['volume'] = $manifest_volume;

        $arrival_from = $formData['arrival_from'];
        $arrival_to = $formData['arrival_to'];
        
        $arrival_from_parsed = new \DateTime($arrival_from);
        $arrival_to_parsed = new \DateTime($arrival_to);

        $invitation_data['date_y'] = $arrival_from_parsed->format('Y');
        $invitation_data['date_m'] = $arrival_from_parsed->format('m');
        $invitation_data['date_d'] = $arrival_from_parsed->format('d');
        $invitation_data['hour_from'] = $arrival_from_parsed->format('H');
        $invitation_data['min_from'] = $arrival_from_parsed->format('i');
        $invitation_data['hour_to'] = $arrival_to_parsed->format('H');
        $invitation_data['min_to'] = $arrival_to_parsed->format('i');

        if ($formData['comment']) {
            $invitation_data['comment'] = $formData['comment'];
        }
        $this->carrier->callCourier($invitation_data);
    }

}

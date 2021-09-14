<?php

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Mijora\Venipak\Model;

use Magento\Framework\Module\Dir;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Tracking\Result as TrackingResult;

/**
 * Omnivalt shipping implementation
 *
 * @author Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Carrier extends AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface {

    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'venipak';

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Rate request data
     *
     * @var RateRequest|null
     */
    protected $_request = null;

    /**
     * Rate result data
     *
     * @var Result|TrackingResult
     */
    protected $_result = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @inheritdoc
     */
    protected $_debugReplacePrivateDataKeys = [
        'Account', 'Password'
    ];

    /**
     * List of TrackReply errors
     * @var array
     */
    private static $trackingErrors = ['FAILURE', 'ERROR'];

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    private $XMLparser;
    protected $configWriter;

    /**
     * Session instance reference
     * 
     */
    protected $_checkoutSession;
    protected $orderFactory;
    protected $venipkaOrderFactory;
    protected $venipkaManifestFactory;
    protected $variableFactory;
    protected $convertOrder;
    protected $trackFactory;
    protected $api;
    protected $productFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Dir\Reader $configReader
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
            \Psr\Log\LoggerInterface $logger,
            Security $xmlSecurity,
            \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
            \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
            \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
            \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
            \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
            \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
            \Magento\Directory\Model\RegionFactory $regionFactory,
            \Magento\Directory\Model\CountryFactory $countryFactory,
            \Magento\Directory\Model\CurrencyFactory $currencyFactory,
            \Magento\Directory\Helper\Data $directoryData,
            \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Module\Dir\Reader $configReader,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Framework\Xml\Parser $parser,
            \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Sales\Model\OrderFactory $orderFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Mijora\Venipak\Model\ManifestFactory $venipakManifestFactory,
            \Magento\Variable\Model\VariableFactory $variableFactory,
            \Magento\Sales\Model\Convert\Order $convertOrder,
            \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $trackInterfaceFactory,
            \Mijora\Venipak\Model\Helper\MjvpApi $api,
            \Magento\Catalog\Model\ProductFactory $productFactory,
            array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;

        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->venipakManifestFactory = $venipakManifestFactory;
        $this->variableFactory = $variableFactory;
        $this->convertOrder = $convertOrder;
        $this->trackFactory = $trackInterfaceFactory;
        $this->XMLparser = $parser;
        $this->api = $api;
        $this->productFactory = $productFactory;
        parent::__construct(
                $scopeConfig,
                $rateErrorFactory,
                $logger,
                $xmlSecurity,
                $xmlElFactory,
                $rateFactory,
                $rateMethodFactory,
                $trackFactory,
                $trackErrorFactory,
                $trackStatusFactory,
                $regionFactory,
                $countryFactory,
                $currencyFactory,
                $directoryData,
                $stockRegistry,
                $data
        );
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool|null
     */
    public function collectRates(RateRequest $request) {

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->_rateFactory->create();
        
        $weight = $request->getPackageWeight();
        $packageValue = $request->getBaseCurrency()->convert($request->getPackageValueWithDiscount(), $request->getPackageCurrency());
        $packageValue = $request->getPackageValueWithDiscount();
        
        $this->_updateFreeMethodQuote($request);
        $isFreeEnabled = $this->getConfigData('free_shipping_enable');
        $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));
        $freeFrom = $this->getConfigData('free_shipping_subtotal');
        
        $max_weight = $this->getConfigData('max_package_weight');
        
        if ($weight > $max_weight){
            return false;
        }
        
        $courier_price = $this->getConfigData('courier_price');
        $pickup_point_price = $this->getConfigData('pickup_point_price');
        
        $isPriceByCountry = $this->getConfigData('price_by_country');
        
        $country_id = $this->_checkoutSession->getQuote()
                    ->getShippingAddress()
                    ->getCountryId();
        if ($isPriceByCountry){
            switch($country_id){
                case 'LT': 
                    $courier_price = $this->getConfigData('lt_courier_price');
                    $pickup_point_price = $this->getConfigData('lt_pickup_point_price');
                    break;
                case 'LV': 
                    $courier_price = $this->getConfigData('lv_courier_price');
                    $pickup_point_price = $this->getConfigData('lv_pickup_point_price');
                    break;
                case 'EE': 
                    $courier_price = $this->getConfigData('ee_courier_price');
                    $pickup_point_price = $this->getConfigData('ee_pickup_point_price');
                    break;
                case 'PL': 
                    $courier_price = $this->getConfigData('pl_courier_price');
                    $pickup_point_price = $this->getConfigData('pl_pickup_point_price');
                    break;
            }
        }
        
        $keyword = $this->getConfigData('ignore_keyword');
        if (trim($keyword)){
            foreach ($this->_checkoutSession->getQuote()->getItemsCollection() as $item){
                $product = $this->productFactory->create();
                $product_id = $item->getProductId();
                $product->load($product_id);
                $description = $product->getDescription();
                if (stripos($description, $keyword) !== false){
                    //found keyword, no shipping possible
                    return false;
                }
            }
        }
        
        $courier_price = $this->parsePriceRange($courier_price, $weight, 5);
        $pickup_point_price = $this->parsePriceRange($pickup_point_price, $weight, 5);
        
        foreach ($allowedMethods as $allowedMethod) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('venipak');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($allowedMethod);
            $method->setMethodTitle($this->getCode('method', $allowedMethod));            

            if ($allowedMethod == "COURIER") {
                $amount = $courier_price;
            }
            if ($allowedMethod == "PICKUP_POINT") {
                $amount = $pickup_point_price;
            }
            if ($isFreeEnabled && $packageValue >= $freeFrom)
                $amount = 0;

            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);
        }
        return $result;
    }
    
    private function parsePriceRange($price_data, $weight, $defaultPrice = 5){
        if (stripos($price_data, ':') !== false){
            $rows = explode('|', $price_data);
            foreach ($rows as $row){
                $data = explode(':', $row);
                //skip if array not 3 size
                if (count($data) != 3){
                    continue;
                }
                if ($weight > $data[0] && $weight <= $data[1]){
                    return $data[2];
                }
            }
        } else {
            return (float)$price_data;
        }
        return $defaultPrice;
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|false
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCode($type, $code = '') {

        $codes = [
            'method' => [
                'COURIER' => __('Courier'),
                'PICKUP_POINT' => __('Pickup point'),
            ],
            'label_size' => [
                'a4' => 'A4',
                '10x15' => '10x15',
            ],
            'country' => [
                'EE' => __('Estonia'),
                'LV' => __('Latvia'),
                'LT' => __('Lithuania'),
                'PL' => __('Poland'),
            ],
            'tracking' => [
            ],
            'terminal' => [],
        ];


        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * Get tracking
     *
     * @param string|string[] $trackings
     * @return Result|null
     */
    public function getTracking($trackings) {
//$this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }
        $this->_getXMLTracking($trackings);

        return $this->_result;
    }

    /**
     * Set tracking request
     *
     * @return void
     */
    protected function setTrackingReqeust() {
        $r = new \Magento\Framework\DataObject();

        $account = $this->getConfigData('account');
        $r->setAccount($account);

        $this->_rawTrackingRequest = $r;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }

        return $arr;
    }

    public function callVenipak() {
        
    }

    public function doShipment($ids) {
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $order_packages_mapping = [];
        

        $var = $this->variableFactory->create();
        $var->loadByCode('VENIPAK_DATA');
        if (!$var->getId()) {
            $var->setData(['code' => 'VENIPAK_DATA',
                'name' => '',
                'html_value' => '',
                'plain_value' => json_encode(['shipment_counter' => 1, 'manifest_counter' => 1, 'manifest_date' => date('Y-m-d')])
            ]);
            $var->save();
        }
        $counters = json_decode($var->getPlainValue(), true);
        if (!isset($counters['manifest_date']) || $counters['manifest_date'] != date('Y-m-d')) {
            $counters['manifest_date'] = date('Y-m-d');
            $counters['manifest_counter'] = 1;
        }

        $manifest = array(
            'manifest_id' => $counters['manifest_counter'],
            'manifest_name' => $this->getConfigData('shop_name'),
            'shipments' => array(),
        );

        foreach ($ids as $id) {
            $order = $this->orderFactory->create();
            $order->load($id);

            $venipakOrder = $this->venipakOrderFactory->create();
            $venipakOrder->load($order->getId(), 'order_id');

            $shipment_pack = [];
            $items = $order->getAllVisibleItems();
            $packages = $venipakOrder->getNumberOfPackages();
            $order_packages_mapping[$id] = $packages;
            for ($i = 1; $i <= $packages; $i++) {
                $shipment_pack[$i] = array(
                    'serial_number' => $counters['shipment_counter'],
                    'document_number' => '',
                    'weight' => round($order->getWeight() / $packages, 2),
                    'volume' => 0,
                );
                foreach ($items as $item) {
                    $product_volume = $item->getWidth() * $item->getHeight() * $item->getDepth();
                    $shipment_pack[$i]['volume'] += round((float) $product_volume / $packages);
                }
                $counters['shipment_counter']++;
            }

            $shippingAddress = $order->getShippingAddress();
            $contact_person = $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname();
            $contact_phone = $shippingAddress->getTelephone();
            $contact_email = $shippingAddress->getEmail();
            
            $consignee_code = '';
            if ($shippingAddress->GetVatId()){
                $consignee_code = $shippingAddress->GetVatId();
            }

            if ($order->getData('shipping_method') == 'venipak_COURIER') {
                $consignee = [
                    'name' => $contact_person,
                    'code' => $consignee_code,
                    'country_code' => $shippingAddress->getCountryId(),
                    'city' => $shippingAddress->getCity(),
                    'address' => $shippingAddress->getStreet()[0],
                    'postcode' => $shippingAddress->getPostcode(),
                    'person' => $contact_person,
                    'phone' => $contact_phone,
                    'email' => $contact_email,
                    'door_code' => $venipakOrder->getDoorCode(),
                    'cabinet_number' => $venipakOrder->getCabinetNumber(),
                    'warehouse_number' => $venipakOrder->getWarehouseNumber(),
                    'carrier_call' => $venipakOrder->getCallBeforeDelivery(),
                    'delivery_time' => 'nwd'.$venipakOrder->getDeliveryTime(),
                    'return_doc' => $venipakOrder->getReturnSignedDocument() ? 1 : 0,
                    'cod' => $venipakOrder->getIsCod() ? $venipakOrder->getCodAmount() : '',
                    'cod_type' => $venipakOrder->getIsCod() ? $currency : '',
                ];
            }
            if ($order->getData('shipping_method') == 'venipak_PICKUP_POINT') {
                $terminal_info = $this->getOrderPickup($order);
                if (!$terminal_info){
                    throw new \Exception(__('Terminal not found for order'));
                }
                $consignee = [
                    'name' => $terminal_info->name,
                    'code' => $terminal_info->code,
                    'country_code' => $terminal_info->country,
                    'city' => $terminal_info->city,
                    'address' => $terminal_info->address,
                    'postcode' => $terminal_info->zip,
                    'person' => $contact_person,
                    'phone' => $contact_phone,
                    'email' => $contact_email,
                    'cod' => $venipakOrder->getIsCod() ? $venipakOrder->getCodAmount() : '',
                    'cod_type' => $venipakOrder->getIsCod() ? $currency : '',
                ];
            }
            $manifest['shipments'][] = array(
                'order_id' => $id,
                'order_code' => $order->getIncrementId(),
                'consignee' => $consignee,
                'packs' => $shipment_pack,
            );
        }
        $manifest_xml = $this->api->buildManifestXml($manifest);
        if ($this->isXMLContentValid($manifest_xml)) {
            $status = $this->api->sendXml($manifest_xml);
            if (!isset($status['error'])){
                $manifest = $this->venipakManifestFactory->create();
                $manifest->setManifestNumber($this->api->getManifestNumber($counters['manifest_counter']));
                $manifest->save();
                $var->addData(['plain_value' => json_encode($counters)]);
                $var->save();
                if (isset($status['text']) && is_array($status['text'])) {

                    $offset = 0;
                    foreach ($order_packages_mapping as $order_id => $mapping) {

                        $order_labels = array_slice($status['text'], $offset, $mapping);
                        $this->saveOrderData($order_id, $order_labels, $manifest);

                        $offset += $mapping;
                    }
                } elseif (isset($status['text'])) {
                    $order_id = array_key_first($order_packages_mapping);
                    $this->saveOrderData($order_id, [$status['text']], $manifest);
                } 
                $counters['manifest_counter']++;
            } else {
                $error_text = '';
                foreach($status['error'] as $error){
                    $error_text .= $status['error']['text'] . '<br/>';
                }
                throw new \Exception($error_text);
            }
        }
        
        return true;
    }
    
    public function callCourier($invitation_data){
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $courier_invitation_xml = $this->api->buildCourierInvitationXml($invitation_data);
        if ($this->isXMLContentValid($courier_invitation_xml)) {
            $status = $this->api->sendXml($courier_invitation_xml);
            if (!isset($status['error']) && $status['text']) {
            } else {
                $error_text = '';
                foreach($status['error'] as $error){
                    $error_text .= $status['error']['text'] . '<br/>';
                }
                throw new \Exception($error_text);
            }
        } else {
            throw new \Exception("Invalid XML format.");
        }
    }

    private function saveOrderData($order_id, $labels, $manifest) {
        $venipakOrder = $this->venipakOrderFactory->create();
        $venipakOrder->load($order_id, 'order_id');
        $venipakOrder->setLabelNumber(json_encode($labels));
        $venipakOrder->setLabelGeneratedAt(date('Y-m-d H:i:s'));
        $venipakOrder->setManifestId($manifest->getId());
        $venipakOrder->save();

        $order = $this->orderFactory->create();
        $order->load($order_id);
        $order->addStatusHistoryComment('Ready for Venipak', false);
        $order->save();
        $this->setOrderShipment($order, $labels);
    }

    private function setOrderShipment($order, $labels) {
        foreach ($labels as $label) {
            $shipment = $this->convertOrder->toShipment($order);

            foreach ($order->getAllItems() AS $orderItem) {
                // Check if order item has qty to ship or is virtual
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                $shipment->addItem($shipmentItem);
            }

            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);

            try {
                $shipment->save();
                $shipment->getOrder()->save();
                $track = $this->trackFactory->create()->setNumber(
                    $label
                )->setCarrierCode(
                    $order->getData('shipping_method')
                )->setTitle(
                    'Venipak'
                );
                $pdf = $this->printLabels([$label]);
                if ($pdf){
                    $shipment->setShippingLabel($pdf);
                }
                $shipment->addTrack($track);
                $shipment->save();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                        __($e->getMessage())
                );
            }
        }
    }
    
    public function printLabels($labels){
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $this->api->setSize($this->getConfigData('label_size'));
        $pdf = $this->api->getLabelPdf($labels);
        return $pdf;
    }
    
    public function printManifest($number){
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $this->api->setSize($this->getConfigData('label_size'));
        $pdf = $this->api->getManifestPdf($number);
        return $pdf;
    }

    private function getOrderPickup($order) {
        $shippingAddress = $order->getShippingAddress();
        $pickups = $this->api->getTerminals($shippingAddress->getCountryId());
        $pickup_id = false;
        $data = @json_decode($shippingAddress->getVenipakData());
        if (is_object($data)) {
            $pickup_id = $data->pickupPoint;
        }
        if (is_array($pickups)) {
            foreach ($pickups as $pickup) {
                if ($pickup->id == $pickup_id) {
                    return $pickup;
                }
            }
        }
        return false;
    }

    public function isXMLContentValid($xmlContent, $version = '1.0', $encoding = 'utf-8') {
        if (trim($xmlContent) == '') {
            return false;
        }

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($xmlContent);

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request) {
        
    }

    /**
     * Recursive replace sensitive fields in debug data by the mask
     * @param array $data
     * @return string
     */
    protected function filterDebugData($data) {
        foreach (array_keys($data) as $key) {
            if (is_array($data[$key])) {
                $data[$key] = $this->filterDebugData($data[$key]);
            } elseif (in_array($key, $this->_debugReplacePrivateDataKeys)) {
                $data[$key] = self::DEBUG_KEYS_MASK;
            }
        }
        return $data;
    }

}

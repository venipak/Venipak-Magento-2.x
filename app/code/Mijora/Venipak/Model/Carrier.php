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
    private $venipakTracking = null;
    private $venipakLabel = null;

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

        if ($this->getConfigFlag('test_mode')) {
            $this->api->setTestMode();
        }
        $this->api->setURLs($this->getConfigData('api_prod_url'), $this->getConfigData('api_test_url'));
        
        $this->api->setVersion("1.0.2");
        if ($this->getConfigFlag('sender_address')){
            $this->api->setConsignor($this->buildConsignorXml());
        }
        
        if ($this->getConfigFlag('enable_return') && $this->getConfigData('return_days') ){
            $this->api->setReturnConsignee($this->buildReturnConsigneeXml());
            $this->api->setReturnService($this->getConfigData('return_days'));
        }

        //check terminals list
        $var = $this->variableFactory->create();
        $var->loadByCode('VENIPAK_REFRESH');
        if (!$var->getId() || $var->getPlainValue() < time() - 3600 * 24) {
            $this->refreshPickUpPoints();
            if (!$var->getId()){
                $var->setData(['code' => 'VENIPAK_REFRESH',
                    'plain_value' => time()
                ]);
            } else {
                $var->addData(['plain_value' => time()]);
            }
            $var->save();
        }
    }
    
    public function getConfig($name){
        return $this->getConfigData($name);
    }

    private function refreshPickUpPoints() {
        $countries = ['LT', 'LV', 'EE', 'PL'];
        foreach ($countries as $country) {
            $parcel_terminals = $this->api->getTerminals($country);
            if ($parcel_terminals) {
                $var = $this->variableFactory->create();
                $var->loadByCode('VENIPAK_PICKUPS_' . $country);
                if (!$var->getId()){
                    $var->setData(['code' => 'VENIPAK_PICKUPS_' . $country,
                        'plain_value' => json_encode($parcel_terminals)
                    ]);
                } else {
                    $var->addData(['plain_value' => json_encode($parcel_terminals)
                    ]);
                }
                $var->save();
            }
        }
    }
    
    /**
     * Build consignor XML structure
     */
    private function buildConsignorXml() {
        $xml_code = '<consignor>';
        $xml_code .= '<name>' . $this->getConfigData('sender_name') . '</name>';
        $xml_code .= '<company_code>' . $this->getConfigData('company_code') . '</company_code>';
        $xml_code .= '<country>' . $this->getConfigData('shop_country_code') . '</country>';
        $xml_code .= '<city>' . $this->getConfigData('shop_city') . '</city>';
        $xml_code .= '<address>' . $this->getConfigData('shop_address') . '</address>';
        $xml_code .= '<post_code>' . $this->getConfigData('shop_postcode') . '</post_code>';
        $xml_code .= '<contact_person>' . $this->getConfigData('shop_name') . '</contact_person>';
        $xml_code .= '<contact_tel>' . $this->getConfigData('shop_phone') . '</contact_tel>';
        $xml_code .= '<contact_email>' . $this->getConfigData('shop_email') . '</contact_email>';
        $xml_code .= '</consignor>';
        return $xml_code;
    }
    
    /**
     * Build consignor XML structure
     */
    private function buildReturnConsigneeXml() {
        $xml_code = '<return_consignee>';
        $xml_code .= '<name>' . $this->getConfigData('sender_name') . '</name>';
        $xml_code .= '<company_code>' . $this->getConfigData('company_code') . '</company_code>';
        $xml_code .= '<country>' . $this->getConfigData('shop_country_code') . '</country>';
        $xml_code .= '<city>' . $this->getConfigData('shop_city') . '</city>';
        $xml_code .= '<address>' . $this->getConfigData('shop_address') . '</address>';
        $xml_code .= '<post_code>' . $this->getConfigData('shop_postcode') . '</post_code>';
        $xml_code .= '<contact_person>' . $this->getConfigData('shop_name') . '</contact_person>';
        $xml_code .= '<contact_tel>' . $this->getConfigData('shop_phone') . '</contact_tel>';
        $xml_code .= '<contact_email>' . $this->getConfigData('shop_email') . '</contact_email>';
        $xml_code .= '</return_consignee>';
        return $xml_code;
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

        if ($weight > $max_weight) {
            return false;
        }

        $courier_price = $this->getConfigData('courier_price');
        $pickup_point_price = $this->getConfigData('pickup_point_price');

        $isPriceByCountry = $this->getConfigData('price_by_country');

        $country_id = $this->_checkoutSession->getQuote()
                ->getShippingAddress()
                ->getCountryId();
        $allowed_countries = $this->getCode('country');
        if (!$country_id) {
            $country_id = $request->getDestCountryId();
        }
        if (!isset($allowed_countries[$country_id])) {
            return false;
        }

        if ($isPriceByCountry) {
            switch ($country_id) {
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
        if ($keyword && trim($keyword)) {
            foreach ($this->_checkoutSession->getQuote()->getItemsCollection() as $item) {
                $product = $this->productFactory->create();
                $product_id = $item->getProductId();
                $product->load($product_id);
                $description = $product->getDescription();
                if (stripos($description, $keyword) !== false) {
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

    private function parsePriceRange($price_data, $weight, $defaultPrice = 5) {
        if (stripos($price_data, ':') !== false) {
            $rows = explode('|', $price_data);
            foreach ($rows as $row) {
                $data = explode(':', $row);
                //skip if array not 3 size
                if (count($data) != 3) {
                    continue;
                }
                if ($weight == 0 && $data[0] == 0) {
                    return $data[2];
                }
                if ($weight > $data[0] && $weight <= $data[1]) {
                    return $data[2];
                }
            }
        } else {
            return (float) $price_data;
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

    private function groupOrdersByWarehouse($ids) {
        $grouped = [];
        foreach ($ids as $id) {
            $venipakOrder = $this->venipakOrderFactory->create();
            $venipakOrder->load($id, 'order_id');
            $warehouse_id = $venipakOrder->getWarehouseId();
            if (!isset($grouped[$warehouse_id])) {
                $grouped[$warehouse_id] = [];
            }
            $grouped[$warehouse_id][] = $venipakOrder;
        }
        return $grouped;
    }

    public function doShipment($ids, $magento_action = false) {
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $order_packages_mapping = [];

        $result = new \Magento\Framework\DataObject();

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
        $shop_id = $this->getConfigData('shop_id');

        $grouped = $this->groupOrdersByWarehouse($ids);

        foreach ($grouped as $warehouse_id => $venipakOrders) {

            $manifest_id = $counters['manifest_counter'];
            if ($shop_id) {
                $manifest_id = $shop_id . sprintf('%02d', (int) $manifest_id);
            }
            $manifest = array(
                'manifest_id' => $manifest_id,
                'manifest_name' => $this->getConfigData('shop_name'),
                'shipments' => array(),
            );

            $venipakManifest = $this->venipakManifestFactory->create()->getCollection()->addFieldToSelect('*');
            $venipakManifest->addFieldToFilter('is_closed', array('eq' => 0))
                    ->addFieldToFilter('warehouse_id', array('eq' => $warehouse_id))
                    ->addFieldToFilter('created_at', array('like' => date('Y-m-d') . ' %'));
            if (count($venipakManifest) > 0) {
                $venipakManifest = $venipakManifest->getFirstItem();
                $manifest['manifest_id'] = (int) substr($venipakManifest->getManifestNumber(), -3);
            } else {
                $venipakManifest = false;
            }

            foreach ($venipakOrders as $venipakOrder) {
                $id = $venipakOrder->getOrderId();
                $order = $this->orderFactory->create();
                $order->load($venipakOrder->getOrderId());

                $shipment_pack = [];
                $items = $order->getAllVisibleItems();
                $packages = $venipakOrder->getNumberOfPackages();
                $order_packages_mapping[$id] = $packages;
                for ($i = 1; $i <= $packages; $i++) {
                    $shipment_id = $counters['shipment_counter'];
                    if ($shop_id) {
                        $shipment_id = $shop_id . sprintf('%06d', (int) $shipment_id);
                    }
                    $shipment_pack[$i] = array(
                        'serial_number' => $shipment_id,
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
                if ($shippingAddress->GetVatId()) {
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
                        'delivery_time' => 'nwd' . $venipakOrder->getDeliveryTime(),
                        'return_doc' => $venipakOrder->getReturnSignedDocument() ? 1 : 0,
                        'cod' => $venipakOrder->getIsCod() ? $venipakOrder->getCodAmount() : '',
                        'cod_type' => $venipakOrder->getIsCod() ? $currency : '',
                    ];
                }
                if ($order->getData('shipping_method') == 'venipak_PICKUP_POINT') {
                    $terminal_info = $this->getOrderPickup($order);
                    if (!$terminal_info) {
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
                if (!isset($status['error'])) {

                    if (!$venipakManifest) {
                        $venipakManifest = $this->venipakManifestFactory->create();
                        $venipakManifest->setManifestNumber($this->api->getManifestNumber($manifest_id));
                        $venipakManifest->setWarehouseId($warehouse_id);
                        $venipakManifest->save();
                        $counters['manifest_counter']++;
                    }

                    $var->addData(['plain_value' => json_encode($counters)]);
                    $var->save();
                    $tmp_counter = 1;
                    if (isset($status['text']) && is_array($status['text'])) {

                        $offset = 0;
                        foreach ($order_packages_mapping as $order_id => $mapping) {

                            $order_labels = array_slice($status['text'], $offset, $mapping);
                            $this->saveOrderData($order_id, $order_labels, $venipakManifest, ($magento_action && $tmp_counter == 1));

                            $offset += $mapping;
                            $tmp_counter++;
                        }
                    } elseif (isset($status['text'])) {
                        $order_id = array_key_first($order_packages_mapping);
                        $this->saveOrderData($order_id, [$status['text']], $venipakManifest, ($magento_action && $tmp_counter == 1));
                    }
                } else {
                    $error_text = '';
                    foreach ($status['error'] as $error) {
                        $error_text .= $status['error']['text'] . '<br/>';
                    }
                    throw new \Exception($error_text);
                }
            }
        }

        return true;
    }

    public function callCourier($invitation_data) {
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $courier_invitation_xml = $this->api->buildCourierInvitationXml($invitation_data);
        if ($this->isXMLContentValid($courier_invitation_xml)) {
            $status = $this->api->sendXml($courier_invitation_xml);
            if (!isset($status['error']) && $status['text']) {
                
            } else {
                $error_text = '';
                foreach ($status['error'] as $error) {
                    $error_text .= $status['error']['text'] . '<br/>';
                }
                throw new \Exception($error_text);
            }
        } else {
            throw new \Exception("Invalid XML format.");
        }
    }

    private function saveOrderData($order_id, $labels, $manifest, $magento_action = false) {
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
        //if shipping by magento, do not create shipment
        if ($magento_action) {
            $this->venipakTracking = $labels[0];
            $pdf = $this->printLabels([$labels[0]]);
            if ($pdf) {
                $this->venipakLabel = $pdf;
            }
        } else {
            $this->setOrderShipment($order, $labels);
        }
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
                if ($pdf) {
                    $shipment->setShippingLabel($pdf);
                }
                $shipment->addTrack($track);
                $shipment->save();
            } catch (\Exception $e) {
                /*
                  throw new \Magento\Framework\Exception\LocalizedException(
                  __($e->getMessage())
                  ); */
            }
        }
    }

    public function printLabels($labels) {
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $this->api->setSize($this->getConfigData('label_size'));
        $pdf = $this->api->getLabelPdf($labels);
        return $pdf;
    }

    public function printManifest($number) {
        $this->api->setApiId($this->getConfigData('api_id'));
        $this->api->setUsername($this->getConfigData('account'));
        $this->api->setPassword($this->getConfigData('password'));
        $this->api->setSize($this->getConfigData('label_size'));
        $pdf = $this->api->getManifestPdf($number);
        return $pdf;
    }

    public function getVenipakTracking($labels, $type = 'track_single') {
        $trackingData = $this->api->getTracking($labels, $type);
        return $trackingData;
    }

    public function getOrderPickup($order) {
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

    public function getTerminals($country) {
        $var = $this->variableFactory->create();
        $var->loadByCode('VENIPAK_PICKUPS_' . strtoupper($country));
        if ($var->getId() && is_array(json_decode($var->getPlainValue()))){
            return json_decode($var->getPlainValue());
        }
        return $this->api->getTerminals($country);
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
        $result = new \Magento\Framework\DataObject();
        try {
            $order = $request->getOrderShipment()->getOrder();
            $this->doShipment([$order->getId()], true);
        } catch (\Exception $ex) {
            $result->setErrors($ex->getMessage());
        }
        if ($this->venipakLabel && $this->venipakTracking) {
            $result->setTrackingNumber($this->venipakTracking);
            $result->setShippingLabelContent($this->venipakLabel);
        }
        return $result;
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

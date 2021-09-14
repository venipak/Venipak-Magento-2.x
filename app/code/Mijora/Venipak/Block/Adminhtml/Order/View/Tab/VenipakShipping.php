<?php

namespace Mijora\Venipak\Block\Adminhtml\Order\View\Tab;

use Mijora\Venipak\Model\Helper\MjvpApi;

class VenipakShipping extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/view/tab/venipakshipping.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    protected $orderFactory;
    protected $warehouseFactory;
    protected $api;
    
    public $urlBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            array $data = [],
            \Mijora\Venipak\Model\OrderFactory $orderFactory,
            \Mijora\Venipak\Model\WarehouseFactory $warehouseFactory,
            MjvpApi $api,
            \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->coreRegistry = $registry;
        $this->orderFactory = $orderFactory;
        $this->warehouseFactory = $warehouseFactory;
        $this->api = $api;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }
    
    public function getWarehouses() {
        $warehouse = $this->warehouseFactory->create();
        $collection = $warehouse->getCollection();
        return $collection->getData();
    }

    public function getTerminals() {
        $order = $this->getOrder();
        $shippingAddress = $order->getShippingAddress();
        return $this->api->getTerminals($shippingAddress->getCountryId());
    }

    public function getCurrentTerminal() {

        $order = $this->getOrder();
        $shippingAddress = $order->getShippingAddress();
        $data =  @json_decode($shippingAddress->getVenipakData());
        if (is_object($data)){
            return $data->pickupPoint;
        }
        return null;
    }

    public function getOrder() {
        return $this->coreRegistry->registry('current_order');
    }

    public function getVenipakOrder() {
        $order = $this->getOrder();
        $model = $this->orderFactory->create();
        $model->load($order->getId(), 'order_id');
        if (!$model->getId()){
            $model = $this->createVenipakOrder($model, $order);
        }
        return $model;
    }
    
    private function createVenipakOrder($model, $order){
        $model->setOrderId($order->getId());
        $shippingAddress = $order->getShippingAddress();
        $data =  @json_decode($shippingAddress->getVenipakData());
        if (is_object($data)){
            $model->setDoorCode($data->doorCode ?? null);
            $model->setWarehouseNumber($data->warehouseNumber ?? null);
            $model->setCabinetNumber($data->cabinetNumber ?? null);
            $model->setDeliveryTime($data->deliveryTime ?? null);
            $model->setCallBeforeDelivery($data->callBeforeDelivery ?? null);
        }
        $payment_method = $order->getPayment()->getMethodInstance()->getCode();
        if (stripos('cashondelivery', $payment_method) !== false) {
            $model->setIsCod(1);
            $model->setCodAmount(round($order->getGrandTotal(), 2));
        }
        $model->save();
        return $model;
    }
    
    public function getLabels($model){
        if ($model->getLabelNumber() !== null){
            $labels = @json_decode($model->getLabelNumber());
            if (is_array($labels)){
                return $labels;
            }
        }
        return [];
    }
    
    public function labelUrl($id){
        return $this->urlBuilder->getUrl('mijora_venipak/order/label',['id'=>$id]);
    }

    public function isVenipakShippingMethod() {
        $order = $this->getOrder();
        $methods = array(
            'VENIPAK_PICKUP_POINT',
            'VENIPAK_COURIER'
        );
        $order_shipping_method = strtoupper($order->getData('shipping_method'));
        return in_array($order_shipping_method, $methods);
    }

    public function getShippingMethod() {
        $order = $this->getOrder();
        return str_ireplace('venipak_','',strtolower($order->getData('shipping_method')));
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Venipak shipping settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Venipak shipping');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {

        return $this->isVenipakShippingMethod($this->getOrder());
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
// For me, I wanted this tab to always show
// You can play around with conditions to
// show the tab later
        return false;
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass() {
// I wanted mine to load via AJAX when it's selected
// That's what this does
//return 'ajax only';
        return '';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass() {
        return $this->getTabClass();
    }

    /**
     * View URL getter
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId) {
        return $this->getUrl('venipakshipping/*/*', ['order_id' => $orderId]);
    }

}

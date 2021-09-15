<?php
namespace Mijora\Venipak\Block\Adminhtml\Sales;

use Magento\Sales\Model\OrderRepository;

class Terminal extends \Magento\Backend\Block\Template {
   
    protected $carrier;
    
    protected $pickupPoints;
    
    
   
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [], 
        \Mijora\Venipak\Model\Carrier $carrier
    ) {
        $this->coreRegistry = $registry;
        $this->carrier = $carrier;
        parent::__construct($context, $data);
        
        $this->pickupPoints = $this->carrier->getTerminals('');
    }
    
    public function getTerminalName(){
        //$orderRepository = new \Magento\Sales\Model\OrderRepository();
        $order_id = $this->getRequest()->getParam('order_id');
        $order = $this->getOrder();
        //$order =  $orderRepository->get($order_id);
        if (strtoupper($order->getData('shipping_method')) == strtoupper('venipak_PICKUP_POINT')) {
            return $this->getTerminal($order);
        }
        return false;
    
    }
    
    public function getCurrentTerminal(){
        //$orderRepository = new \Magento\Sales\Model\OrderRepository();
        $order_id = $this->getRequest()->getParam('order_id');
        $order = $this->getOrder();
        //$order =  $orderRepository->get($order_id);
        if (strtoupper($order->getData('shipping_method')) == strtoupper('venipak_PICKUP_POINT')) {
            return $this->getTerminalId($order);
        }
        return false;
    
    }
    
    public function getTerminalId($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $data =  @json_decode($shippingAddress->getVenipakData());
        if (is_object($data)){
            return $data->pickupPoint;
        }
        return null;
    } 
    
    public function getTerminal($order)
    {
        $terminal_id = $this->getTerminalId($order);
        if (is_array($this->pickupPoints)){
            foreach ($this->pickupPoints as $pickup){
                if ($pickup->id == $terminal_id){
                    return $pickup;
                }
            }
        }
        return false;
    } 
   
    public function getTerminals()
    {
        return $this->pickupPoints;
    }
    
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }
    
}
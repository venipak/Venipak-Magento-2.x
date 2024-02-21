<?php

namespace Mijora\Venipak\Block\Adminhtml\Order\Create\Shipping\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form {
    
    protected $carrier;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $taxData,
        \Mijora\Venipak\Model\Carrier $carrier,
        array $data = []
    ) {
        $this->carrier = $carrier;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $taxData, $data);
    }

    public function getCurrentTerminal() {
        $data =  @json_decode($this->getAddress()->getVenipakData());
        if (is_object($data)){
            return $data->pickupPoint;
        }
        return null;
    }

    public function getTerminals() {
        return $this->carrier->getTerminals($this->getAddress()->getCountryId());
    }

}

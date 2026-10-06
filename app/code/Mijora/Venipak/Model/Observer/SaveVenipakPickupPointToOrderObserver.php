<?php

namespace Mijora\Venipak\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveVenipakPickupPointToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Mijora\Venipak\Model\ShippingDescription
     */
    protected $shippingDescription;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Mijora\Venipak\Model\ShippingDescription $shippingDescription
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Mijora\Venipak\Model\ShippingDescription $shippingDescription
    )
    {
        $this->_objectManager = $objectmanager;
        $this->shippingDescription = $shippingDescription;
    }

    public function execute(EventObserver $observer)
    {

        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
        $quote_address = $quote->getShippingAddress();
        $order_address = $order->getShippingAddress();
        $order_address->setVenipakData( $quote_address->getVenipakData());
        $this->shippingDescription->updatePickupPoint($order);
        return $this;
    }

}
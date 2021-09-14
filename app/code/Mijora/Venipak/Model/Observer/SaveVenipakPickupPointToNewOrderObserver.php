<?php

namespace Mijora\Venipak\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveVenipakPickupPointToNewOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected $_request;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,\Magento\Framework\App\RequestInterface $request)
    {
        $this->objectManager = $objectmanager;
        $this->_request = $request;
    }

    public function execute(EventObserver $observer)
    {
        $params = $this->_request->getParams();
        if (isset($params['order']['venipak_data'])){
            $quote = $this->getQuote();
            $quote_address = $quote->getShippingAddress();
            $quote_address->setVenipakData( $params['order']['venipak_data']);
            $quote_address->save();
        }
        return $this;
    }

    protected function getSession()
    {
        return $this->objectManager->get('Magento\Backend\Model\Session\Quote');
    }

    protected function getQuote()
    {
        return $this->getSession()->getQuote();
    }

}
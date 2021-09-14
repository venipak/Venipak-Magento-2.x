<?php
namespace Mijora\Venipak\Model\Quote;

use Magento\Quote\Model\Quote\Address;
use Mijora\Venipak\Model\Carrier;

class AddressPlugin
{
    /**
     * Hook into setShippingMethod.
     * As this is magic function processed by __call method we need to hook around __call
     * to get the name of the called method. after__call does not provide this information.
     *
     * @param Address $subject
     * @param callable $proceed
     * @param string $method
     * @param mixed $vars
     * @return Address
     */
    public function around__call($subject, $proceed, $method, $vars)
    {
    	
        $result = $proceed($method, $vars);
        
        if ($method == 'setShippingMethod'
            && $subject->getExtensionAttributes()
            && $subject->getExtensionAttributes()->getVenipakData()
        ) {
            $subject->setVenipakData($subject->getExtensionAttributes()->getVenipakData());
        }
        return $result;

    }
}
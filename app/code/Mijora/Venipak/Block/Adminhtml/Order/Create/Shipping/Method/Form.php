<?php

namespace Mijora\Venipak\Block\Adminhtml\Order\Create\Shipping\Method;

use Mijora\Venipak\Model\Helper\MjvpApi;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form {

    public function getCurrentTerminal() {
        $data =  @json_decode($this->getAddress()->getVenipakData());
        if (is_object($data)){
            return $data->pickupPoint;
        }
        return null;
    }

    public function getTerminals() {
        $api = new MjvpApi();
        $parcel_terminals = $api->getTerminals($this->getAddress()->getCountryId());
        return $parcel_terminals;
    }

}

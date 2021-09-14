<?php

namespace Mijora\Venipak\Model;

use Magento\Framework\DataObject;
use Mijora\Venipak\Api\Data\PickupPointInterface;

class PickupPoint extends DataObject implements PickupPointInterface
{
    /**
     * @return string
     */
    public function getAddress() {
        return (string) $this->_getData('address');
    }
    
    /**
     * @return string
     */
    public function getName() {
        return (string) $this->_getData('name');
    }
    
    /**
     * @return int
     */
    public function getType() {
        return (int) $this->_getData('type');
    }
    
    /**
     * @return string
     */
    public function getLat() {
        return (string) $this->_getData('lat');
    }
    
    /**
     * @return string
     */
    public function getLng() {
        return (string) $this->_getData('lng');
    }
    
    /**
     * @return string
     */
    public function getTerminal() {
        return (string) $this->_getData('terminal');
    }
    
    /**
     * @return string
     */
    public function getCountry() {
        return (string) $this->_getData('country');
    }
    
    /**
     * @return string
     */
    public function getCity() {
        return (string) $this->_getData('city');
    }
    
    /**
     * @return integer
     */
    public function getPickUpEnabled() {
        return (int) $this->_getData('pick_up_enabled');
    }
    
    /**
     * @return integer
     */
    public function getId() {
        return (int)$this->_getData('id');
    }

}
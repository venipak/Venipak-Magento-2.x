<?php

namespace Mijora\Venipak\Api\Data;

/**
 * Office Interface
 */
interface PickupPointInterface {

    /**
     * @return string
     */
    public function getAddress();
    
    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getType();
    
    /**
     * @return string
     */
    public function getLat();
    
    /**
     * @return string
     */
    public function getLng();
    
    /**
     * @return string
     */
    public function getTerminal();
    
    /**
     * @return string
     */
    public function getCountry();
    
    /**
     * @return string
     */
    public function getCity();
    
    /**
     * @return int
     */
    public function getPickUpEnabled();
    
    /**
     * @return int
     */
    public function getId();
}
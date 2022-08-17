<?php

namespace Mijora\Venipak\Model;

use Mijora\Venipak\Api\PickupPointManagementInterface;
use Mijora\Venipak\Api\Data\PickupPointInterfaceFactory;
use Magento\Framework\Xml\Parser;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir;

class PickupPointManagement implements PickupPointManagementInterface {

    protected $pickupPointFactory;
    protected $carrier;

    /**
     * PickupPointManagement constructor.
     * 
     * @param PickupPointInterfaceFactory $pickupPointInterfaceFactory
     * @param \Mijora\Venipak\Model\Carrier $carrier
     */
    
    public function __construct(
            PickupPointInterfaceFactory $pickupPointInterfaceFactory,
            \Mijora\Venipak\Model\Carrier $carrier
            ) {
        
        $this->pickupPointFactory = $pickupPointInterfaceFactory;
        $this->carrier = $carrier;
    }

    /**
     * Get pickup points
     *
     * @param string $group
     * @param string $city
     * @param string $country
     * @return array
     */
    public function fetchPickupPoints($group, $city, $country) {
        $mapped_pickup_points = [];
        //return [];
        $pickup_points = $this->carrier->getTerminals($country);
        if (!is_array($pickup_points)){
            return [];
        }
        foreach ($pickup_points as $pickup_point){
            $mapped_pickup_point = $this->pickupPointFactory->create();
            $mapped_pickup_point->setId($pickup_point->id);
            $mapped_pickup_point->setName($pickup_point->name);
            $mapped_pickup_point->setAddress($pickup_point->address);
            $mapped_pickup_point->setType($pickup_point->type);
            $mapped_pickup_point->setLat($pickup_point->lat);
            $mapped_pickup_point->setLng($pickup_point->lng);
            $mapped_pickup_point->setTerminal($pickup_point->terminal);
            $mapped_pickup_point->setCity($pickup_point->city);
            $mapped_pickup_point->setCountry($pickup_point->country);
            $mapped_pickup_point->setPickUpEnabled($pickup_point->pick_up_enabled);
            $mapped_pickup_points[] = $mapped_pickup_point;
        }
        //var_dump($pickup_points);
        //return [];
        return $mapped_pickup_points;
    }

}

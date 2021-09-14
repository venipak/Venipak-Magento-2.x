<?php

namespace Mijora\Venipak\Model;

use Mijora\Venipak\Api\PickupPointManagementInterface;
use Mijora\Venipak\Api\Data\PickupPointInterfaceFactory;
use Mijora\Venipak\Model\Carrier;
use Magento\Framework\Xml\Parser;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir;
use Mijora\Venipak\Model\Helper\MjvpApi;

class PickupPointManagement implements PickupPointManagementInterface {

    protected $pickupPointFactory;

    /**
     * OfficeManagement constructor.
     * @param PickupPointInterfaceFactory $officeInterfaceFactory
     */
    
    public function __construct(PickupPointInterfaceFactory $pickupPointInterfaceFactory) {
        $this->pickupPointFactory = $pickupPointInterfaceFactory;
    }

    /**
     * Get offices for the given postcode and city
     *
     * @param string $postcode
     * @param string $limit
     * @param string $country
     * @param string $group
     * @return \Mijora\Venipak\Api\Data\OfficeInterface[]
     */
    public function fetchPickupPoints($group, $city, $country) {
        $mapped_pickup_points = [];
        //return [];
        $api = new MjvpApi();
        $pickup_points = $api->getTerminals($country);
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

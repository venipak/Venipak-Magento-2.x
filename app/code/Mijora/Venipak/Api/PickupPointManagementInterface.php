<?php

namespace Mijora\Venipak\Api;

interface PickupPointManagementInterface
{

    /**
     * Find parcel terminals for the customer
     *
     * @param string $postcode
     * @param string $city
     * @param string $country
     * @return \Mijora\Venipak\Api\Data\PickupPointInterface[]
     */
    public function fetchPickupPoints($group, $city, $country );
}
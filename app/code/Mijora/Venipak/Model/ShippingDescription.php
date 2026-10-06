<?php

namespace Mijora\Venipak\Model;

/**
 * Adds selected pickup point to order shipping description, so it is shown in emails, invoices and admin
 */
class ShippingDescription {

    const PICKUP_POINT_METHOD = 'venipak_PICKUP_POINT';

    protected $carrier;

    public function __construct(
            \Mijora\Venipak\Model\Carrier $carrier
    ) {
        $this->carrier = $carrier;
    }

    /**
     * Updates pickup point in order shipping description. Order and its shipping address must be saved by caller.
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function updatePickupPoint($order) {
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress) {
            return;
        }

        $data = @json_decode($shippingAddress->getVenipakData() ?? '');
        if (!is_object($data)) {
            $data = new \stdClass();
        }

        $isPickupPoint = $order->getData('shipping_method') == self::PICKUP_POINT_METHOD;
        $oldSuffix = $data->descriptionSuffix ?? '';
        if (!$isPickupPoint && $oldSuffix === '') {
            return;
        }

        $description = (string) $order->getShippingDescription();

        // remove previously added pickup point
        if ($oldSuffix !== '' && substr($description, -strlen($oldSuffix)) === $oldSuffix) {
            $description = substr($description, 0, -strlen($oldSuffix));
        }
        unset($data->descriptionSuffix);

        if ($isPickupPoint && !empty($data->pickupPoint)) {
            $terminal = $this->findTerminal($shippingAddress->getCountryId(), $data->pickupPoint);
            if ($terminal) {
                $suffix = ' (' . implode(', ', array_filter([$terminal->name ?? '', $terminal->address ?? '', $terminal->city ?? ''])) . ')';
                $description .= $suffix;
                $data->descriptionSuffix = $suffix;
            }
        }

        $order->setShippingDescription($description);
        $shippingAddress->setVenipakData(json_encode($data));
    }

    private function findTerminal($country, $terminalId) {
        foreach ($this->carrier->getTerminals($country) as $terminal) {
            if (isset($terminal->id) && $terminal->id == $terminalId) {
                return $terminal;
            }
        }
        return false;
    }

}

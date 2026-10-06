<?php

namespace Mijora\Venipak\Plugin\Email;

use Mijora\Venipak\Model\Carrier;

/**
 * Shipment email tracking link leads directly to Venipak tracking page instead of Magento tracking popup
 */
class TrackingUrlPlugin
{
    /**
     * @param \Magento\Sales\Block\DataProviders\Email\Shipment\TrackingUrl $subject
     * @param string $result
     * @param \Magento\Sales\Model\Order\Shipment\Track $track
     * @return string
     */
    public function afterGetUrl($subject, $result, $track)
    {
        // old shipments have carrier code venipak_COURIER or venipak_PICKUP_POINT
        if ($track && strpos((string) $track->getCarrierCode(), Carrier::CODE) === 0 && $track->getNumber()) {
            return Carrier::TRACKING_URL . rawurlencode($track->getNumber());
        }
        return $result;
    }
}

<?php

namespace Mijora\Venipak\Plugin\Email;

/**
 * Adds Venipak tracking numbers after items table in order, invoice and credit memo emails.
 * Shipment email is skipped, because it already shows tracking numbers.
 */
class ItemsTrackingPlugin
{
    const TEMPLATE = 'Mijora_Venipak::email/tracking.phtml';

    protected $venipakOrderFactory;
    protected $logger;

    public function __construct(
        \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml($subject, $result)
    {
        if ($subject instanceof \Magento\Sales\Block\Order\Email\Shipment\Items) {
            return $result;
        }

        try {
            $order = $subject->getOrder();
            if (!$order || !$order->getId() || strpos((string) $order->getData('shipping_method'), 'venipak_') !== 0) {
                return $result;
            }

            $trackingNumbers = $this->getTrackingNumbers($order->getId());
            if (empty($trackingNumbers)) {
                return $result;
            }

            $html = $subject->getLayout()
                ->createBlock(\Magento\Framework\View\Element\Template::class)
                ->setTemplate(self::TEMPLATE)
                ->setTrackingNumbers($trackingNumbers)
                ->toHtml();

            return $result . $html;
        } catch (\Exception $e) {
            $this->logger->error('Venipak: failed to add tracking numbers to email. ' . $e->getMessage());
        }

        return $result;
    }

    private function getTrackingNumbers($orderId)
    {
        $venipakOrder = $this->venipakOrderFactory->create();
        $venipakOrder->load($orderId, 'order_id');
        if (!$venipakOrder->getLabelNumber()) {
            return [];
        }

        $labels = @json_decode($venipakOrder->getLabelNumber(), true);
        return is_array($labels) ? array_filter($labels) : [];
    }
}

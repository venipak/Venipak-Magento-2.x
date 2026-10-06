<?php

namespace Mijora\Venipak\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Registers Venipak shipment (same as "Generate labels" button) when order status
 * is changed to the status selected in "Auto-register shipment on status" setting
 */
class AutoRegisterShipmentObserver implements ObserverInterface
{
    const CONFIG_PATH_STATUS = 'carriers/venipak/auto_register_status';

    const VENIPAK_METHODS = ['venipak_COURIER', 'venipak_PICKUP_POINT'];

    /**
     * Orders being registered in this request, to avoid repeated registration
     * when order is saved again during shipment creation
     *
     * @var array
     */
    private static $processing = [];

    protected $scopeConfig;
    protected $carrier;
    protected $orderInitializer;
    protected $orderFactory;
    protected $messageManager;
    protected $appState;
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mijora\Venipak\Model\Carrier $carrier,
        \Mijora\Venipak\Model\OrderInitializer $orderInitializer,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\State $appState,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->carrier = $carrier;
        $this->orderInitializer = $orderInitializer;
        $this->orderFactory = $orderFactory;
        $this->messageManager = $messageManager;
        $this->appState = $appState;
        $this->logger = $logger;
    }

    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$order || !$order->getId() || !$order->dataHasChangedFor('status')) {
            return $this;
        }

        // Skip new orders, which are created with the selected status
        if (!$order->getOrigData('status')) {
            return $this;
        }

        $status = $this->scopeConfig->getValue(self::CONFIG_PATH_STATUS, ScopeInterface::SCOPE_STORE, $order->getStoreId());
        if (!$status || $order->getStatus() != $status) {
            return $this;
        }

        if (!in_array($order->getData('shipping_method'), self::VENIPAK_METHODS)) {
            return $this;
        }

        $orderId = $order->getId();
        if (isset(self::$processing[$orderId])) {
            return $this;
        }
        self::$processing[$orderId] = true;

        // Register only after order is committed to database
        $order->getResource()->addCommitCallback(function () use ($orderId) {
            $this->registerShipment($orderId);
        });

        return $this;
    }

    private function registerShipment($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        if (!$order->getId()) {
            return;
        }

        try {
            $venipakOrder = $this->orderInitializer->getVenipakOrder($order);
            if ($venipakOrder->getLabelNumber()) {
                return;
            }

            $this->carrier->doShipment([$orderId]);
            if (!$this->orderInitializer->getVenipakOrder($order)->getLabelNumber()) {
                throw new \Exception(__('Failed to create shipment'));
            }
            $this->addAdminMessage(__('Venipak shipment for order #%1 registered automatically', $order->getIncrementId()), false);
        } catch (\Exception $e) {
            $error = $this->formatError($e->getMessage());
            $this->logger->error('Venipak: failed to automatically register shipment for order #' . $order->getIncrementId() . '. ' . $error);
            $this->addAdminMessage(__('Failed to automatically register Venipak shipment for order #%1: %2', $order->getIncrementId(), $error), true);
            $this->addOrderComment($orderId, __('Failed to automatically register Venipak shipment: %1', $error));
        }
    }

    private function formatError($message)
    {
        $message = str_replace('</li><li>', '; ', $message);
        return trim(strip_tags($message));
    }

    private function addOrderComment($orderId, $comment)
    {
        try {
            $order = $this->orderFactory->create()->load($orderId);
            $order->addStatusHistoryComment($comment, false);
            $order->save();
        } catch (\Exception $e) {
            $this->logger->error('Venipak: failed to add order comment. ' . $e->getMessage());
        }
    }

    /**
     * Messages are shown only in admin, so customer would not see them in checkout
     */
    private function addAdminMessage($message, $isError)
    {
        try {
            if ($this->appState->getAreaCode() !== \Magento\Framework\App\Area::AREA_ADMINHTML) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        if ($isError) {
            $this->messageManager->addErrorMessage($message);
        } else {
            $this->messageManager->addSuccessMessage($message);
        }
    }
}

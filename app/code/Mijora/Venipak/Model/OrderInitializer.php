<?php

namespace Mijora\Venipak\Model;

/**
 * Loads Venipak order data for Magento order or creates it with default values
 */
class OrderInitializer {

    protected $venipakOrderFactory;
    protected $warehouseFactory;

    public function __construct(
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Mijora\Venipak\Model\WarehouseFactory $warehouseFactory
    ) {
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->warehouseFactory = $warehouseFactory;
    }

    public function getVenipakOrder($order) {
        $model = $this->venipakOrderFactory->create();
        $model->load($order->getId(), 'order_id');
        if (!$model->getId()) {
            $model = $this->createVenipakOrder($model, $order);
        }
        return $model;
    }

    public function getDefaultWarehouse() {
        $warehouse = $this->warehouseFactory->create();
        $warehouse->load(1, 'default');
        return $warehouse;
    }

    private function createVenipakOrder($model, $order) {
        $model->setOrderId($order->getId());
        $shippingAddress = $order->getShippingAddress();
        $data = @json_decode($shippingAddress->getVenipakData());
        if (is_object($data)) {
            $model->setDoorCode($data->doorCode ?? null);
            $model->setWarehouseNumber($data->warehouseNumber ?? null);
            $model->setCabinetNumber($data->cabinetNumber ?? null);
            $model->setDeliveryTime($data->deliveryTime ?? null);
            $model->setCallBeforeDelivery($data->callBeforeDelivery ?? null);
        }
        $payment_method = $order->getPayment()->getMethodInstance()->getCode();
        if (stripos('cashondelivery', $payment_method) !== false || stripos('venipak_cod', $payment_method) !== false) {
            $model->setIsCod(1);
            $model->setCodAmount(round($order->getGrandTotal(), 2));
        }
        $default_warehouse = $this->getDefaultWarehouse();
        if ($default_warehouse) {
            $model->setWarehouseId($default_warehouse->getWarehouseId());
        }
        $model->setNumberOfPackages(1);
        $model->setWeight($order->getWeight());
        $model->save();
        return $model;
    }

}

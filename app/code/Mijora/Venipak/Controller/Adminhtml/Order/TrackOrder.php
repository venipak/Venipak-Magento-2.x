<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

class TrackOrder extends \Magento\Backend\App\Action {

    protected $resultPageFactory;
    protected $venipakManifestFactory;
    protected $orderFactory;
    protected $coreRegistry;
    protected $messageManager;
    protected $resultFactory;
    protected $venipakWarehouseFactory;
    protected $venipakOrderFactory;
    protected $carrier;
    protected $json;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Mijora\Venipak\Model\ManifestFactory $venipakManifestFactory,
            \Mijora\Venipak\Model\WarehouseFactory $venipakWarehouseFactory,
            \Mijora\Venipak\Model\OrderFactory $venipakOrderFactory,
            \Magento\Sales\Model\OrderFactory $orderFactory,
            \Magento\Framework\Registry $coreRegistry,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\Controller\ResultFactory $resultFactory,
            \Mijora\Venipak\Model\Carrier $carrier,
            \Magento\Framework\Controller\Result\JsonFactory $json
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->venipakManifestFactory = $venipakManifestFactory;
        $this->orderFactory = $orderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->venipakWarehouseFactory = $venipakWarehouseFactory;
        $this->venipakOrderFactory = $venipakOrderFactory;
        $this->carrier = $carrier;
        $this->json = $json;
    }

    public function execute() {
        $id = $this->getRequest()->getParam('id');
        $resultJson = $this->json->create();

        if (!$id) {
            return $resultJson->setData([
                        'html' => __('Order ID not found')
            ]);
        }

        $model = $this->venipakOrderFactory->create();
        $model->load($id, 'id');

        $labels = @json_decode($model->getLabelNumber(), true);
        if (!is_array($labels)) {
            return $resultJson->setData([
                        'html' => __('No tracking numbers found')
            ]);
        }
        $html = $this->buildHeader($model, $labels);

        foreach ($labels as $label) {
            $data = $this->carrier->getVenipakTracking($label);
            
            $html .= $this->buildStartTable($model, $label);
            $html .= $this->buildRows($data);
            $html .= $this->buildEndTable($model, $label);
        }
        return $resultJson->setData([
                    'html' => $html
        ]);
    }

    private function buildHeader($order, $labels) {
        return '<hr><h2 align="center" class="tracking-order-heading">Order #' . $order->getId() . ' (packets: ' . implode(', ', $labels) . ')</h2>';
    }

    private function buildStartTable($order, $label) {
        return '<p align="center" class="tracking-status mb-0">Pack No.
                                <b>' . $label . '</b> status information:
                            </p>
                            <table border="1" align="center" cellspacing="0" cellpadding="1">
                                <thead>
                                <tr>
                                    <th>Package No.</th>
                                    <th>Shipment No.</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Terminal</th>
                                </tr>
                                </thead>
                                <tbody>';
    }

    private function buildEndTable($order, $label) {
        return '</tbody></table>';
    }

    private function buildRows($data) {
        $return = '';
        $rows = explode('\n', $data);
        foreach ($rows as $key => $row) {
            if ($key == 0) {
                continue;
            }
            $row = str_getcsv($row);
            $return .= '<tr>
                                    <th>' . $row[0] . '</th>
                                    <th>' . $row[1] . '</th>
                                    <th>' . $row[2] . '</th>
                                    <th>' . $row[3] . '</th>
                                    <th>' . $row[4] . '</th>
                                </tr>';
        }
        return $return;
    }

}

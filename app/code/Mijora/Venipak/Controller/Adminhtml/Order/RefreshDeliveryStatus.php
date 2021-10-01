<?php

namespace Mijora\Venipak\Controller\Adminhtml\Order;

class RefreshDeliveryStatus extends \Magento\Backend\App\Action {

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
    private $lastStatus = null;

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

        $models = $this->venipakOrderFactory->create()->getCollection()->addFieldToSelect('*');
        $models->addFieldToFilter('delivery_status', [array('neq' => 'Delivered'), array('null' => true)]);
        $models->addFieldToFilter('updated_at', array('gt' => date('Y-m-d H:i:s', strtotime("-1 month"))));
               
        foreach ($models as $model) {
            $this->lastStatus = null;
            $labels = @json_decode($model->getLabelNumber(), true);
            
            if (!is_array($labels)) {
                continue;
            }

            foreach ($labels as $label) {
                $data = $this->carrier->getVenipakTracking($label);
                $this->checkStatuses($data);
            }
            
            if ($this->lastStatus) {
                $model->setDeliveryStatus($this->lastStatus);
                $model->save();
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    private function checkStatuses($data) {
        $rows = explode("\n", $data);
        foreach ($rows as $key => $row) {
            if ($key == 0) {
                continue;
            }
            $row = str_getcsv($row);
            if (count($row) != 5) {
                continue;
            }
            $this->lastStatus = $row[3];
        }
    }

}
